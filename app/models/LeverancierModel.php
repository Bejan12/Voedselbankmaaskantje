<?php

class LeverancierModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAllLeveranciers($sort = 'eerstvolgende')
    {
        try {
            if ($sort === 'recent') {
                $orderBy = "LeverancierID DESC";
            } else {
                // Eerst alle toekomstige leveringen, gesorteerd op dichtstbijzijnde datum, daarna de rest
                $orderBy = "
                    (EerstvolgendeLevering < NOW()) ASC, 
                    ABS(TIMESTAMPDIFF(SECOND, NOW(), EerstvolgendeLevering)) ASC
                ";
            }

            $this->db->query("SELECT l.LeverancierID, 
                                     l.LeverancierNummer,
                                     l.Bedrijfsnaam, 
                                     l.Adres, 
                                     l.ContactNaam, 
                                     l.ContactEmail, 
                                     l.ContactTelefoon, 
                                     DATE_FORMAT(l.EerstvolgendeLevering, '%d-%m-%Y %H:%i') as EerstvolgendeLevering,
                                     t.TypeNaam AS LeverancierType,
                                     l.Status,
                                     CASE 
                                         WHEN l.Status = 'onderweg' THEN 1 
                                         ELSE 0 
                                     END as IsOnderweg
                              FROM leverancier l
                              JOIN leverancier_type t ON l.LeverancierTypeID = t.LeverancierTypeID
                              ORDER BY $orderBy");
            
            return $this->db->resultSet();
        } catch (\Throwable $e) {
            // Gooi de error door naar de controller
            throw $e;
        }
    }

    private function generateUniqueLeverancierNummer()
    {
        do {
            $nummer = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            $this->db->query("SELECT LeverancierID FROM leverancier WHERE LeverancierNummer = :nummer");
            $this->db->bind(':nummer', $nummer);
            $exists = $this->db->single();
        } while ($exists);
        
        return $nummer;
    }

    public function getAllLeverancierTypes()
    {
        try {
            $this->db->query("SELECT LeverancierTypeID, TypeNaam FROM leverancier_type ORDER BY TypeNaam");
            return $this->db->resultSet();
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function addLeverancier($data)
    {
        try {
            // Generate unique leverancier number
            $leverancierNummer = $this->generateUniqueLeverancierNummer();
            
            // Convert datetime-local format to MySQL datetime format
            $leveringDatum = DateTime::createFromFormat('Y-m-d\TH:i', $data['eerstvolgendelevering']);
            $mysqlDateTime = $leveringDatum ? $leveringDatum->format('Y-m-d H:i:s') : $data['eerstvolgendelevering'];
            
            $this->db->query("INSERT INTO leverancier (LeverancierNummer, GebruikerID, LeverancierTypeID, Bedrijfsnaam, Adres, ContactNaam, ContactEmail, ContactTelefoon, EerstvolgendeLevering, Status) 
                              VALUES (:leverancier_nummer, 1, :leverancier_type_id, :bedrijfsnaam, :adres, :contactnaam, :contactemail, :contacttelefoon, :eerstvolgendelevering, 'gepland')");
            
            $this->db->bind(':leverancier_nummer', $leverancierNummer);
            $this->db->bind(':leverancier_type_id', $data['leverancier_type_id']);
            $this->db->bind(':bedrijfsnaam', $data['bedrijfsnaam']);
            $this->db->bind(':adres', $data['adres']);
            $this->db->bind(':contactnaam', $data['contactnaam']);
            $this->db->bind(':contactemail', $data['contactemail']);
            $this->db->bind(':contacttelefoon', $data['contacttelefoon']);
            $this->db->bind(':eerstvolgendelevering', $mysqlDateTime);
            
            return $this->db->execute();
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function deleteLeverancierById($id)
    {
        try {
            $this->db->query("DELETE FROM leverancier WHERE LeverancierID = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function emailExists($email)
    {
        try {
            $this->db->query("SELECT LeverancierID FROM leverancier WHERE ContactEmail = :email");
            $this->db->bind(':email', $email);
            
            $result = $this->db->single();
            return $result ? true : false;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function getLeverancierById($id)
    {
        try {
            $this->db->query("SELECT l.LeverancierID, 
                                     l.LeverancierNummer,
                                     l.Bedrijfsnaam, 
                                     l.Adres, 
                                     l.ContactNaam, 
                                     l.ContactEmail, 
                                     l.ContactTelefoon, 
                                     l.EerstvolgendeLevering,
                                     l.Status,
                                     t.TypeNaam AS LeverancierType
                              FROM leverancier l
                              JOIN leverancier_type t ON l.LeverancierTypeID = t.LeverancierTypeID
                              WHERE l.LeverancierID = :id");
            
            $this->db->bind(':id', $id);
            return $this->db->single();
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function updateLeverancier($id, $data)
    {
        try {
            $leveringDatum = DateTime::createFromFormat('Y-m-d\TH:i', $data['eerstvolgendelevering']);
            $mysqlDateTime = $leveringDatum ? $leveringDatum->format('Y-m-d H:i:s') : $data['eerstvolgendelevering'];
            
            $this->db->query("UPDATE leverancier SET 
                              Bedrijfsnaam = :bedrijfsnaam,
                              Adres = :adres,
                              ContactNaam = :contactnaam,
                              ContactEmail = :contactemail,
                              ContactTelefoon = :contacttelefoon,
                              EerstvolgendeLevering = :eerstvolgendelevering,
                              Status = :status
                              WHERE LeverancierID = :id");
            
            $this->db->bind(':id', $id);
            $this->db->bind(':bedrijfsnaam', $data['bedrijfsnaam']);
            $this->db->bind(':adres', $data['adres']);
            $this->db->bind(':contactnaam', $data['contactnaam']);
            $this->db->bind(':contactemail', $data['contactemail']);
            $this->db->bind(':contacttelefoon', $data['contacttelefoon']);
            $this->db->bind(':eerstvolgendelevering', $mysqlDateTime);
            $this->db->bind(':status', $data['status']);
            
            return $this->db->execute();
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function emailExistsForOtherLeverancier($email, $leverancier_id)
    {
        try {
            $this->db->query("SELECT LeverancierID FROM leverancier WHERE ContactEmail = :email AND LeverancierID != :id");
            $this->db->bind(':email', $email);
            $this->db->bind(':id', $leverancier_id);
            
            $result = $this->db->single();
            return $result ? true : false;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function canDeleteLeverancier($id)
    {
        try {
            $this->db->query("SELECT Status FROM leverancier WHERE LeverancierID = :id");
            $this->db->bind(':id', $id);
            
            $result = $this->db->single();
            
            if (!$result) {
                return false; // Leverancier bestaat niet
            }
            
            // Kan niet verwijderen als status 'onderweg' is
            return $result->Status !== 'onderweg';
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
