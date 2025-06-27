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
    public function getAll($filter = null)
    {
        $sql = "SELECT v.VoedselpakketID, CONCAT(p.Voornaam, ' ', p.Achternaam) AS KlantNaam, p.Email, v.DatumSamenstelling, v.DatumUitgifte,
        GROUP_CONCAT(DISTINCT a.Naam SEPARATOR ', ') AS Allergieen
        FROM voedselpakket v
        JOIN klant k ON v.KlantID = k.KlantID
        JOIN gebruiker g ON k.GebruikerID = g.GebruikerID
        JOIN persoon p ON g.PersoonID = p.PersoonID
        LEFT JOIN klantallergie ka ON k.KlantID = ka.KlantID
        LEFT JOIN allergie a ON ka.AllergieID = a.AllergieID";
        if ($filter === 'beschikbaar') {
            $sql .= " WHERE v.DatumUitgifte IS NULL";
        } elseif ($filter === 'niet_beschikbaar') {
            $sql .= " WHERE v.DatumUitgifte IS NOT NULL";
        }
        $sql .= " GROUP BY v.VoedselpakketID, p.Voornaam, p.Achternaam, p.Email, v.DatumSamenstelling, v.DatumUitgifte
        ORDER BY v.DatumSamenstelling DESC";
        $this->db->query($sql);
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
}
