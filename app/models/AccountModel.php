<?php

class AccountModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function findUserByUsername($gebruikersnaam)
    {
        $this->db->query('SELECT * FROM Gebruiker WHERE Gebruikersnaam = :gebruikersnaam');
        $this->db->bind(':gebruikersnaam', $gebruikersnaam);
        
        return $this->db->single();
    }

    public function findUserByEmail($email)
    {
        $this->db->query('SELECT g.*, p.Email, p.Voornaam, p.Achternaam 
                         FROM Gebruiker g 
                         JOIN Persoon p ON g.PersoonID = p.PersoonID 
                         WHERE p.Email = :email');
        $this->db->bind(':email', $email);
        
        return $this->db->single();
    }

    public function register($data)
    {
        try {
            // Start transaction
            $this->db->query('START TRANSACTION');
            $this->db->execute();

            // Insert into Persoon table first
            $this->db->query('INSERT INTO Persoon (Voornaam, Achternaam, Email, Telefoon, Geboortedatum) 
                             VALUES (:voornaam, :achternaam, :email, :telefoon, :geboortedatum)');
            
            $this->db->bind(':voornaam', $data['voornaam']);
            $this->db->bind(':achternaam', $data['achternaam']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':telefoon', $data['telefoon']);
            $this->db->bind(':geboortedatum', !empty($data['geboortedatum']) ? $data['geboortedatum'] : null);
            
            $this->db->execute();

            // Get the inserted PersoonID
            $this->db->query('SELECT LAST_INSERT_ID() as id');
            $result = $this->db->single();
            $persoonId = $result->id;

            // Hash the password
            $hashedPassword = password_hash($data['wachtwoord'], PASSWORD_DEFAULT);

            // Insert into Gebruiker table
            $this->db->query('INSERT INTO Gebruiker (PersoonID, Gebruikersnaam, WachtwoordHash) 
                             VALUES (:persoonid, :gebruikersnaam, :wachtwoordhash)');
            
            $this->db->bind(':persoonid', $persoonId);
            $this->db->bind(':gebruikersnaam', $data['gebruikersnaam']);
            $this->db->bind(':wachtwoordhash', $hashedPassword);
            
            $this->db->execute();

            // Commit transaction
            $this->db->query('COMMIT');
            $this->db->execute();

            return true;

        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->query('ROLLBACK');
            $this->db->execute();
            return false;
        }
    }
}
