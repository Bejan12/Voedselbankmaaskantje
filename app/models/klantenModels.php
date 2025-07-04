<?php

class klantenModels
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getKlanten()
    {
        $this->db->query("SELECT k.KlantID, p.Voornaam, p.Achternaam, p.Email, p.Telefoon,
                                 k.AantalVolwassenen, k.AantalKinderen, k.AantalBabys
                          FROM klant k
                          JOIN gebruiker g ON k.GebruikerID = g.GebruikerID
                          JOIN persoon p ON g.PersoonID = p.PersoonID
                          ORDER BY k.KlantID ASC");
        return $this->db->resultSet();
    }

    public function addKlant($data)
    {
        // Voeg persoon toe
        $this->db->query("INSERT INTO persoon (Voornaam, Achternaam, Adres, Telefoon, Email) VALUES (:voornaam, :achternaam, :adres, :telefoon, :email)");
        $this->db->bind(':voornaam', $data['voornaam']);
        $this->db->bind(':achternaam', $data['achternaam']);
        $this->db->bind(':adres', $data['adres']);
        $this->db->bind(':telefoon', $data['telefoon']);
        $this->db->bind(':email', $data['email']);
        $this->db->execute();
        $persoonId = $this->db->outQuery("SELECT LAST_INSERT_ID() as id")->fetch(PDO::FETCH_OBJ)->id;

        // Controleer of gebruikersnaam al bestaat
        $gebruikersnaam = strtolower($data['voornaam'] . '.' . $data['achternaam']);
        $this->db->query("SELECT COUNT(*) as aantal FROM gebruiker WHERE Gebruikersnaam = :gebruikersnaam");
        $this->db->bind(':gebruikersnaam', $gebruikersnaam);
        $aantal = $this->db->single()->aantal;
        if ($aantal > 0) {
            // Verwijder de zojuist toegevoegde persoon om orphan records te voorkomen
            $this->db->query("DELETE FROM persoon WHERE PersoonID = :persoonid");
            $this->db->bind(':persoonid', $persoonId);
            $this->db->execute();
            return false; // Of geef een foutmelding terug
        }

        // Voeg gebruiker toe
        $this->db->query("INSERT INTO gebruiker (PersoonID, Gebruikersnaam, WachtwoordHash, IsGeblokkeerd) VALUES (:persoonid, :gebruikersnaam, :wachtwoord, 0)");
        $this->db->bind(':persoonid', $persoonId);
        $this->db->bind(':gebruikersnaam', $gebruikersnaam);
        $this->db->bind(':wachtwoord', hex2bin(hash('sha256', 'welkom')));
        $this->db->execute();
        $gebruikerId = $this->db->outQuery("SELECT LAST_INSERT_ID() as id")->fetch(PDO::FETCH_OBJ)->id;

        // Voeg klant toe
        $this->db->query("INSERT INTO klant (GebruikerID, AantalVolwassenen, AantalKinderen, AantalBabys, GeenVarkensvlees, Veganistisch, Vegetarisch)
            VALUES (:gebruikerid, :volw, :kind, :baby, :geen_varkensvlees, :veganistisch, :vegetarisch)");
        $this->db->bind(':gebruikerid', $gebruikerId);
        $this->db->bind(':volw', $data['aantal_volwassenen']);
        $this->db->bind(':kind', $data['aantal_kinderen']);
        $this->db->bind(':baby', $data['aantal_babys']);
        $this->db->bind(':geen_varkensvlees', $data['geen_varkensvlees']);
        $this->db->bind(':veganistisch', $data['veganistisch']);
        $this->db->bind(':vegetarisch', $data['vegetarisch']);
        return $this->db->execute();
    }

    public function getKlantById($klantId)
    {
        $this->db->query("SELECT 
                k.KlantID, 
                p.Voornaam, 
                p.Achternaam, 
                p.Adres,
                p.Email, 
                p.Telefoon,
                k.AantalVolwassenen,
                k.AantalKinderen,
                k.AantalBabys,
                k.GeenVarkensvlees,
                k.Veganistisch,
                k.Vegetarisch
            FROM klant k
            JOIN gebruiker g ON k.GebruikerID = g.GebruikerID
            JOIN persoon p ON g.PersoonID = p.PersoonID
            WHERE k.KlantID = :klantid");
        $this->db->bind(':klantid', $klantId);
        return $this->db->single();
    }

    public function updateKlant($klantId, $data)
    {
        $this->db->query("SELECT g.PersoonID, g.GebruikerID FROM klant k JOIN gebruiker g ON k.GebruikerID = g.GebruikerID WHERE k.KlantID = :klantid");
        $this->db->bind(':klantid', $klantId);
        $ids = $this->db->single();
        if (!$ids) return false;

        $this->db->query("UPDATE persoon SET Voornaam = :voornaam, Achternaam = :achternaam, Adres = :adres, Telefoon = :telefoon, Email = :email WHERE PersoonID = :persoonid");
        $this->db->bind(':voornaam', $data['voornaam']);
        $this->db->bind(':achternaam', $data['achternaam']);
        $this->db->bind(':adres', $data['adres']);
        $this->db->bind(':telefoon', $data['telefoon']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':persoonid', $ids->PersoonID);
        $this->db->execute();

        // Update ook de gebruikersnaam
        $gebruikersnaam = strtolower($data['voornaam'] . '.' . $data['achternaam']);
        $this->db->query("UPDATE gebruiker SET Gebruikersnaam = :gebruikersnaam WHERE GebruikerID = :gebruikerid");
        $this->db->bind(':gebruikersnaam', $gebruikersnaam);
        $this->db->bind(':gebruikerid', $ids->GebruikerID);
        $this->db->execute();

        $this->db->query("UPDATE klant SET 
            AantalVolwassenen = :volw, 
            AantalKinderen = :kind, 
            AantalBabys = :baby, 
            GeenVarkensvlees = :geen_varkensvlees, 
            Veganistisch = :veganistisch, 
            Vegetarisch = :vegetarisch
            WHERE KlantID = :klantid");
        $this->db->bind(':volw', $data['aantal_volwassenen']);
        $this->db->bind(':kind', $data['aantal_kinderen']);
        $this->db->bind(':baby', $data['aantal_babys']);
        $this->db->bind(':geen_varkensvlees', $data['geen_varkensvlees']);
        $this->db->bind(':veganistisch', $data['veganistisch']);
        $this->db->bind(':vegetarisch', $data['vegetarisch']);
        $this->db->bind(':klantid', $klantId);
        return $this->db->execute();
    }

    public function checkBestaandeEmail($email, $huidigeKlantId)
    {
        $this->db->query("SELECT p.PersoonID 
                          FROM persoon p
                          JOIN gebruiker g ON p.PersoonID = g.PersoonID
                          JOIN klant k ON g.GebruikerID = k.GebruikerID
                          WHERE p.Email = :email AND k.KlantID != :huidige_klant_id");
        $this->db->bind(':email', $email);
        $this->db->bind(':huidige_klant_id', $huidigeKlantId);
        
        $result = $this->db->single();
        return $result !== false;
    }

    public function checkBestaandeGebruikersnaam($voornaam, $achternaam, $huidigeKlantId)
    {
        $gebruikersnaam = strtolower($voornaam . '.' . $achternaam);
        $this->db->query("SELECT k.KlantID 
                          FROM gebruiker g
                          JOIN klant k ON g.GebruikerID = k.GebruikerID
                          WHERE g.Gebruikersnaam = :gebruikersnaam AND k.KlantID != :huidige_klant_id");
        $this->db->bind(':gebruikersnaam', $gebruikersnaam);
        $this->db->bind(':huidige_klant_id', $huidigeKlantId);
        
        $result = $this->db->single();
        return $result !== false;
    }

    public function deleteKlant($klantId)
    {
        // Controleer op actieve reserveringen of verplichtingen
        try {
            // Deze query controleert of de 'reservering' tabel bestaat.
            $this->db->query("SELECT 1 FROM reservering LIMIT 1");
            $this->db->execute();

            // Als de tabel bestaat, controleer op actieve reserveringen.
            $this->db->query("SELECT COUNT(*) as aantal FROM reservering WHERE KlantID = :klantid AND status = 'actief'");
            $this->db->bind(':klantid', $klantId);
            $aantal = $this->db->single()->aantal;

            if ($aantal > 0) {
                // Er zijn nog actieve reserveringen/verplichtingen
                return [
                    'success' => false,
                    'message' => 'Klant kan niet worden verwijderd vanwege een probleem in het systeem'
                ];
            }
        } catch (\PDOException $e) {
            // Als de reservering-tabel niet bestaat, negeer deze check en ga door.
        }

        // Haal gebruiker en persoon ID op
        $this->db->query("SELECT GebruikerID FROM klant WHERE KlantID = :klantid");
        $this->db->bind(':klantid', $klantId);
        $klant = $this->db->single();
        if (!$klant) {
            return [
                'success' => false,
                'message' => 'Klant niet gevonden'
            ];
        }

        $gebruikerId = $klant->GebruikerID;

        $this->db->query("SELECT PersoonID FROM gebruiker WHERE GebruikerID = :gebruikerid");
        $this->db->bind(':gebruikerid', $gebruikerId);
        $gebruiker = $this->db->single();
        $persoonId = $gebruiker ? $gebruiker->PersoonID : null;

        // Verwijder klant
        $this->db->query("DELETE FROM klant WHERE KlantID = :klantid");
        $this->db->bind(':klantid', $klantId);
        $this->db->execute();

        // Verwijder gebruiker
        $this->db->query("DELETE FROM gebruiker WHERE GebruikerID = :gebruikerid");
        $this->db->bind(':gebruikerid', $gebruikerId);
        $this->db->execute();

        // Verwijder persoon
        if ($persoonId) {
            $this->db->query("DELETE FROM persoon WHERE PersoonID = :persoonid");
            $this->db->bind(':persoonid', $persoonId);
            $this->db->execute();
        }

        return [
            'success' => true,
            'message' => 'Klant succesvol verwijderd.'
        ];
    }

    public function getAfgerondeVoedselpakkettenByKlant($klantId)
    {
        // Alleen pakketten met een uitgiftedatum (dus afgerond)
        $this->db->query("
            SELECT 
                vp.VoedselpakketID,
                vp.DatumUitgifte,
                GROUP_CONCAT(CONCAT(p.ProductNaam, ' (', vpp.Aantal, ')') SEPARATOR ', ') AS producten
            FROM voedselpakket vp
            LEFT JOIN voedselpakketproduct vpp ON vp.VoedselpakketID = vpp.VoedselpakketID
            LEFT JOIN product p ON vpp.ProductID = p.ProductID
            WHERE vp.KlantID = :klantid AND vp.DatumUitgifte IS NOT NULL
            GROUP BY vp.VoedselpakketID, vp.DatumUitgifte
            ORDER BY vp.DatumUitgifte DESC
        ");
        $this->db->bind(':klantid', $klantId);
        return $this->db->resultSet();
    }

    public function getLopendeVoedselpakkettenByKlant($klantId)
    {
        $this->db->query("SELECT *
                          FROM Voedselpakket
                          WHERE KlantId = :klantId AND DatumUitgifte IS NULL
                          ORDER BY DatumSamenstelling DESC");
        $this->db->bind(':klantId', $klantId);
        return $this->db->resultSet();
    }
}






