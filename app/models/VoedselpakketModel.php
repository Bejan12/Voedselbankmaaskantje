<?php
/**
 * Model voor Voedselpakketten
 * Bevat methodes voor ophalen en toevoegen van pakketten, inclusief validatie en logging.
 */
class VoedselpakketModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Haal alle voedselpakketten op, inclusief klantnaam, email en allergieën/wensen
     * @param string|null $filter
     * @return array
     */
    public function getAll($filter = null, $datum = null)
    {
        $sql = "SELECT v.VoedselpakketID, CONCAT(p.Voornaam, ' ', p.Achternaam) AS KlantNaam, p.Email, v.DatumSamenstelling, v.DatumUitgifte,
        GROUP_CONCAT(DISTINCT a.Naam SEPARATOR ', ') AS Allergieen
        FROM voedselpakket v
        JOIN klant k ON v.KlantID = k.KlantID
        JOIN gebruiker g ON k.GebruikerID = g.GebruikerID
        JOIN persoon p ON g.PersoonID = p.PersoonID
        LEFT JOIN klantallergie ka ON k.KlantID = ka.KlantID
        LEFT JOIN allergie a ON ka.AllergieID = a.AllergieID";
        $where = [];
        if ($filter === 'beschikbaar') {
            $where[] = "v.DatumUitgifte IS NULL";
        } elseif ($filter === 'niet_beschikbaar') {
            $where[] = "v.DatumUitgifte IS NOT NULL";
        }
        if ($datum) {
            $where[] = "v.DatumSamenstelling = :datum";
        }
        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " GROUP BY v.VoedselpakketID, p.Voornaam, p.Achternaam, p.Email, v.DatumSamenstelling, v.DatumUitgifte
        ORDER BY v.DatumSamenstelling DESC";
        $this->db->query($sql);
        if ($datum) {
            $this->db->bind(':datum', $datum, PDO::PARAM_STR);
        }
        return $this->db->resultSet();
    }

    /**
     * Haal alle klanten op met hun allergieën voor het toevoegen van een pakket
     * @return array
     */
    public function getAllKlantenMetAllergieen()
    {
        $sql = "SELECT k.KlantID, CONCAT(p.Voornaam, ' ', p.Achternaam) AS Naam, p.Email,
        GROUP_CONCAT(DISTINCT a.Naam SEPARATOR ', ') AS Allergieen
        FROM klant k
        JOIN gebruiker g ON k.GebruikerID = g.GebruikerID
        JOIN persoon p ON g.PersoonID = p.PersoonID
        LEFT JOIN klantallergie ka ON k.KlantID = ka.KlantID
        LEFT JOIN allergie a ON ka.AllergieID = a.AllergieID
        GROUP BY k.KlantID, p.Voornaam, p.Achternaam, p.Email
        ORDER BY p.Achternaam";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Voeg een nieuw voedselpakket toe, met validatie op duplicaat en datum
     * @param int $klantId
     * @param string $datumSamenstelling (YYYY-MM-DD)
     * @return array ['success'=>bool, 'message'=>string]
     */
    public function addPakket($klantId, $datumSamenstelling)
    {
        // Controleer of de datum in de toekomst ligt
        $inputDate = strtotime($datumSamenstelling);
        $today = strtotime(date('Y-m-d'));
        // NIEUW: Controleer of de datum boven 2029 ligt
        if ($inputDate > strtotime('2029-12-31')) {
            error_log(date('[Y-m-d H:i:s]') . " Poging tot invoer van datum boven 2029: $datumSamenstelling");
            return ['success'=>false, 'message'=>'Datum mag niet later zijn dan 31-12-2029'];
        }
        if ($inputDate < $today) {
            error_log(date('[Y-m-d H:i:s]') . " Poging tot invoer van datum in het verleden: $datumSamenstelling");
            return ['success'=>false, 'message'=>'Je kunt geen datum in het verleden kiezen.'];
        }
        // Controleer op duplicaat
        $sql = "SELECT COUNT(*) as aantal FROM voedselpakket WHERE KlantID = :klantId AND DatumSamenstelling = :datum";
        $this->db->query($sql);
        $this->db->bind(':klantId', $klantId, PDO::PARAM_INT);
        $this->db->bind(':datum', $datumSamenstelling, PDO::PARAM_STR);
        $result = $this->db->single();
        if ($result && $result->aantal > 0) {
            error_log(date('[Y-m-d H:i:s]') . " Dubbel pakket voor klant $klantId op $datumSamenstelling");
            return ['success'=>false, 'message'=>'Dit voedselpakket bestaat al'];
        }
        // Insert uitvoeren
        try {
            $sql = "INSERT INTO voedselpakket (KlantID, DatumSamenstelling) VALUES (:klantId, :datum)";
            $this->db->query($sql);
            $this->db->bind(':klantId', $klantId, PDO::PARAM_INT);
            $this->db->bind(':datum', $datumSamenstelling, PDO::PARAM_STR);
            $this->db->execute();
            error_log(date('[Y-m-d H:i:s]') . " Voedselpakket succesvol aangemaakt voor klant $klantId op $datumSamenstelling");
            return ['success'=>true, 'message'=>'Voedselpakket is succesvol aangemaakt'];
        } catch (Exception $e) {
            error_log(date('[Y-m-d H:i:s]') . " Fout bij toevoegen voedselpakket: " . $e->getMessage());
            return ['success'=>false, 'message'=>'Er is een fout opgetreden bij het aanmaken van het voedselpakket.'];
        }
    }

    /**
     * Haal klantgegevens op basis van klant ID
     * @param int $klantId
     * @return object|null
     */
    public function getKlantById($klantId)
    {
        $sql = "SELECT k.*, p.Geboortedatum FROM klant k JOIN gebruiker g ON k.GebruikerID = g.GebruikerID JOIN persoon p ON g.PersoonID = p.PersoonID WHERE k.KlantID = :klantId";
        $this->db->query($sql);
        $this->db->bind(':klantId', $klantId, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Haal pakketgegevens op basis van pakket ID
     * @param int $id
     * @return object|null
     */
    public function getPakketById($id)
    {
        $sql = "SELECT v.* FROM voedselpakket v WHERE v.VoedselpakketID = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Verwijder een voedselpakket als het nog niet is uitgegeven en niet door admin is aangemaakt
     */
    public function deletePakket($id)
    {
        $pakket = $this->getPakketById($id);
        if (!$pakket) {
            return ['success'=>false, 'message'=>'Pakket niet gevonden.'];
        }
        // Controle: niet uitgegeven
        if (!empty($pakket->DatumUitgifte)) {
            return ['success'=>false, 'message'=>'Voedselpakket is al uitgegeven en kan niet worden verwijderd.'];
        }
        // Controle: niet door admin (pas aan naar jouw kolom/structuur indien nodig)
        if (isset($pakket->AangemaaktDoor) && strtolower($pakket->AangemaaktDoor) === 'admin') {
            return ['success'=>false, 'message'=>'Voedselpakket van een admin kan niet worden verwijderd'];
        }
        $sql = "DELETE FROM voedselpakket WHERE VoedselpakketID = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->execute();
        return ['success'=>true, 'message'=>'Voedselpakket is succesvol verwijderd'];
    }

    /**
     * Update een voedselpakket als het nog niet is verzonden, inclusief status
     */
    public function updatePakket($id, $klantId, $datum, $status = null)
    {
        $pakket = $this->getPakketById($id);
        if (!$pakket) {
            return ['success'=>false, 'message'=>'Pakket niet gevonden.'];
        }
        // Controle: niet uitgeleverd
        if (!empty($pakket->DatumUitgifte) || (isset($pakket->Status) && strtolower($pakket->Status) === 'geleverd')) {
            return ['success'=>false, 'message'=>'Voedselpakket is al uitgeleverd en kan niet meer worden aangepast'];
        }
        // Controle: datum mag niet na 2027-12-31 zijn
        if (strtotime($datum) > strtotime('2027-12-31')) {
            return ['success'=>false, 'message'=>'Datum mag niet later zijn dan 31-12-2027'];
        }
        $sql = "UPDATE voedselpakket SET KlantID = :klantId, DatumSamenstelling = :datum";
        if ($status !== null) {
            $sql .= ", Status = :status";
        }
        $sql .= " WHERE VoedselpakketID = :id";
        $this->db->query($sql);
        $this->db->bind(':klantId', $klantId, PDO::PARAM_INT);
        $this->db->bind(':datum', $datum, PDO::PARAM_STR);
        if ($status !== null) {
            $this->db->bind(':status', $status, PDO::PARAM_STR);
        }
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->execute();
        return ['success'=>true, 'message'=>'Voedselpakket is succesvol gewijzigd'];
    }
}
