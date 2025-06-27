<?php

class LeverancierModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAllLeveranciers()
    {
        $this->db->query("SELECT LeverancierID, 
                                 Bedrijfsnaam, 
                                 Adres, 
                                 ContactNaam, 
                                 ContactEmail, 
                                 ContactTelefoon, 
                                 DATE_FORMAT(EerstvolgendeLevering, '%d-%m-%Y %H:%i') as EerstvolgendeLevering 
                          FROM leverancier 
                          ORDER BY Bedrijfsnaam");
        
        return $this->db->resultSet();
    }
}
