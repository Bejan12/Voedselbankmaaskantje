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
        $this->db->query("SELECT 
                k.KlantID, 
                p.Voornaam, 
                p.Achternaam, 
                p.Email, 
                p.Telefoon
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
        $persoonId = $this->db->outQuery("SELECT LAST_INSERT_ID() as id")->fetch()->id;

        // Voeg gebruiker toe
        $this->db->query("INSERT INTO gebruiker (PersoonID, Gebruikersnaam, WachtwoordHash, IsGeblokkeerd) VALUES (:persoonid, :gebruikersnaam, :wachtwoord, 0)");
        $this->db->bind(':persoonid', $persoonId);
        $this->db->bind(':gebruikersnaam', strtolower($data['voornaam'] . '.' . $data['achternaam']));
        // Standaard wachtwoord: 'welkom' (SHA256 hash)
        $this->db->bind(':wachtwoord', hex2bin(hash('sha256', 'welkom')));
        $this->db->execute();
        $gebruikerId = $this->db->outQuery("SELECT LAST_INSERT_ID() as id")->fetch()->id;

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
}
