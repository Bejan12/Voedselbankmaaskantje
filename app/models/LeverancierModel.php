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
            $orderBy = "EerstvolgendeLevering ASC";
            if ($sort === 'recent') {
                $orderBy = "LeverancierID DESC";
            }

            $this->db->query("SELECT LeverancierID, 
                                     Bedrijfsnaam, 
                                     Adres, 
                                     ContactNaam, 
                                     ContactEmail, 
                                     ContactTelefoon, 
                                     DATE_FORMAT(EerstvolgendeLevering, '%d-%m-%Y %H:%i') as EerstvolgendeLevering 
                              FROM leverancier 
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
            $this->db->query("INSERT INTO leverancier (Bedrijfsnaam, Adres, ContactNaam, ContactEmail, ContactTelefoon, EerstvolgendeLevering) 
                              VALUES (:bedrijfsnaam, :adres, :contactnaam, :contactemail, :contacttelefoon, :eerstvolgendelevering)");
            
            $this->db->bind(':bedrijfsnaam', $data['bedrijfsnaam']);
            $this->db->bind(':adres', $data['adres']);
            $this->db->bind(':contactnaam', $data['contactnaam']);
            $this->db->bind(':contactemail', $data['contactemail']);
            $this->db->bind(':contacttelefoon', $data['contacttelefoon']);
            $this->db->bind(':eerstvolgendelevering', $data['eerstvolgendelevering']);
            
            return $this->db->execute();
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
