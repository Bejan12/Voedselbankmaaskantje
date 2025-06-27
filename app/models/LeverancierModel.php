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
                                     l.Bedrijfsnaam, 
                                     l.Adres, 
                                     l.ContactNaam, 
                                     l.ContactEmail, 
                                     l.ContactTelefoon, 
                                     DATE_FORMAT(l.EerstvolgendeLevering, '%d-%m-%Y %H:%i') as EerstvolgendeLevering,
                                     t.TypeNaam AS LeverancierType
                              FROM leverancier l
                              JOIN leverancier_type t ON l.LeverancierTypeID = t.LeverancierTypeID
                              ORDER BY $orderBy");
            
            return $this->db->resultSet();
        } catch (\Throwable $e) {
            // Gooi de error door naar de controller
            throw $e;
        }
    }

    public function addLeverancier($data)
    {
        try {
            // Convert datetime-local format to MySQL datetime format
            $leveringDatum = DateTime::createFromFormat('Y-m-d\TH:i', $data['eerstvolgendelevering']);
            $mysqlDateTime = $leveringDatum ? $leveringDatum->format('Y-m-d H:i:s') : $data['eerstvolgendelevering'];
            
            $this->db->query("INSERT INTO leverancier (GebruikerID, LeverancierTypeID, Bedrijfsnaam, Adres, ContactNaam, ContactEmail, ContactTelefoon, EerstvolgendeLevering) 
                              VALUES (1, 1, :bedrijfsnaam, :adres, :contactnaam, :contactemail, :contacttelefoon, :eerstvolgendelevering)");
            
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
}
