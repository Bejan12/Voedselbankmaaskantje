<?php

class HomepageModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getProductVoorraadOverzicht()
    {
        $sql = "CALL GetProductVoorraadOverzicht()";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getGebruikerInfo($gebruikerID)
    {
        $sql = "SELECT 
                    p.Voornaam,
                    p.Achternaam,
                    r.Naam as Rol
                FROM gebruiker g
                JOIN persoon p ON g.PersoonID = p.PersoonID
                JOIN gebruikerrol gr ON g.GebruikerID = gr.GebruikerID
                JOIN rol r ON gr.RolID = r.RolID
                WHERE g.GebruikerID = :gebruikerID";

        $this->db->query($sql);
        $this->db->bind(':gebruikerID', $gebruikerID);
        return $this->db->single();
    }

    public function getToegankelijkeFuncties($gebruikerID)
    {
        $sql = "SELECT DISTINCT r.Naam as Rol
                FROM gebruiker g
                JOIN gebruikerrol gr ON g.GebruikerID = gr.GebruikerID
                JOIN rol r ON gr.RolID = r.RolID
                WHERE g.GebruikerID = :gebruikerID";

        $this->db->query($sql);
        $this->db->bind(':gebruikerID', $gebruikerID);
        return $this->db->resultSet();
    }

    public function getVoedselpakkettenMetFilter($filter = null)
    {
        $sql = "SELECT 
                    v.VoedselpakketID,
                    CONCAT(p.Voornaam, ' ', p.Achternaam) as KlantNaam,
                    v.DatumSamenstelling,
                    v.DatumUitgifte
                FROM voedselpakket v
                JOIN klant k ON v.KlantID = k.KlantID
                JOIN gebruiker g ON k.GebruikerID = g.GebruikerID
                JOIN persoon p ON g.PersoonID = p.PersoonID";

        if ($filter) {
            $sql .= " WHERE v.DatumUitgifte = :filter";
            $this->db->query($sql);
            $this->db->bind(':filter', $filter);
        } else {
            $this->db->query($sql);
        }

        return $this->db->resultSet();
    }
}
