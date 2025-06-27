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
     * Haal alle voedselpakketten op, inclusief klantnaam, email, allergieën en pakketkeuze
     * @param string|null $filter
     * @return array
     */
    public function getAll($filter = null, $datum = null)
    {
        // Alleen kolommen selecteren die daadwerkelijk bestaan in de database!
        $sql = "SELECT v.PakketNummer, v.VoedselpakketID, CONCAT(p.Voornaam, ' ', p.Achternaam) AS KlantNaam, p.Email, v.DatumSamenstelling, v.DatumUitgifte,
        v.Status, v.Opmerking, v.DatumAangemaakt, v.DatumGewijzigd, v.IsActief,
        c.Naam AS PakketKeuze,
        GROUP_CONCAT(DISTINCT a.Naam SEPARATOR ', ') AS Allergieen
        FROM voedselpakket v
        JOIN klant k ON v.KlantID = k.KlantID
        JOIN gebruiker g ON k.GebruikerID = g.GebruikerID
        JOIN persoon p ON g.PersoonID = p.PersoonID
        LEFT JOIN klantallergie ka ON k.KlantID = ka.KlantID
        LEFT JOIN allergie a ON ka.AllergieID = a.AllergieID
        LEFT JOIN categorie c ON v.PakketCategorieID = c.CategorieID";
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
        $sql .= " GROUP BY v.PakketNummer, v.VoedselpakketID, p.Voornaam, p.Achternaam, p.Email, v.DatumSamenstelling, v.DatumUitgifte, v.Status, v.Opmerking, v.DatumAangemaakt, v.DatumGewijzigd, v.IsActief, c.Naam
        ORDER BY v.VoedselpakketID DESC";
        try {
            $this->db->query($sql);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log(date('[Y-m-d H:i:s]') . " Fout bij ophalen voedselpakketten: " . $e->getMessage());
            return [];
        }
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
        try {
            $this->db->query($sql);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log(date('[Y-m-d H:i:s]') . " Fout bij ophalen klanten: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Haal alle categorieën op voor pakketkeuze
     * @return array
     */
    public function getAllCategorieen()
    {
        $sql = "SELECT CategorieID, Naam FROM categorie ORDER BY Naam";
        try {
            $this->db->query($sql);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log(date('[Y-m-d H:i:s]') . " Fout bij ophalen categorieën: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Voeg een nieuw voedselpakket toe, met validatie op duplicaat en datum
     * @param int $klantId
     * @param string $datumSamenstelling (YYYY-MM-DD)
     * @param int $pakketCategorieId
     * @param int $aantalProducten
     * @param string $status
     * @param string|null $opmerking
     * @return array ['success'=>bool, 'message'=>string]
     */
    public function addPakket($klantId, $datumSamenstelling, $pakketCategorieId, $aantalProducten, $status, $opmerking = null)
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
        // Genereer uniek pakketnummer
        $sql = "SELECT COUNT(*) as totaal FROM voedselpakket";
        $this->db->query($sql);
        $totaal = $this->db->single()->totaal + 1;
        $pakketNummer = sprintf('#%03d', $totaal);
        // Insert uitvoeren
        try {
            $sql = "INSERT INTO voedselpakket (KlantID, PakketNummer, PakketCategorieID, DatumSamenstelling, Status, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
                    VALUES (:klantId, :pakketNummer, :pakketCategorieId, :datum, :status, 1, :opmerking, NOW(), NOW())";
            $this->db->query($sql);
            $this->db->bind(':klantId', $klantId, PDO::PARAM_INT);
            $this->db->bind(':pakketNummer', $pakketNummer, PDO::PARAM_STR);
            $this->db->bind(':pakketCategorieId', $pakketCategorieId, PDO::PARAM_INT);
            $this->db->bind(':datum', $datumSamenstelling, PDO::PARAM_STR);
            $this->db->bind(':status', $status, PDO::PARAM_STR);
            $this->db->bind(':opmerking', $opmerking, PDO::PARAM_STR);
            $this->db->execute();
            error_log(date('[Y-m-d H:i:s]') . " Voedselpakket succesvol aangemaakt voor klant $klantId op $datumSamenstelling");
            return ['success'=>true, 'message'=>'Voedselpakket is succesvol aangemaakt'];
        } catch (Exception $e) {
            error_log(date('[Y-m-d H:i:s]') . " Fout bij toevoegen voedselpakket: " . $e->getMessage());
            return ['success'=>false, 'message'=>'Er is een fout opgetreden bij het aanmaken van het voedselpakket.'];
        }
    }

    /**
     * Haal alle producten op per categorie, inclusief allergie-informatie
     * @param int $categorieId
     * @return array
     */
    public function getProductenPerCategorie($categorieId)
    {
        $sql = "SELECT p.ProductID, p.ProductNaam,
        GROUP_CONCAT(DISTINCT a.Naam SEPARATOR ', ') AS Allergieen
        FROM product p
        LEFT JOIN productallergie pa ON p.ProductID = pa.ProductID
        LEFT JOIN allergie a ON pa.AllergieID = a.AllergieID
        WHERE p.CategorieID = :categorieId
        GROUP BY p.ProductID, p.ProductNaam
        ORDER BY p.ProductNaam";
        try {
            $this->db->query($sql);
            $this->db->bind(':categorieId', $categorieId, PDO::PARAM_INT);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log(date('[Y-m-d H:i:s]') . " Fout bij ophalen producten per categorie: " . $e->getMessage());
            return [];
        }
    }
}
