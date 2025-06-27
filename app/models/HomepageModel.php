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
                    v.DatumUitgifte,
                    GROUP_CONCAT(DISTINCT l.Bedrijfsnaam SEPARATOR ', ') as Leveranciers
                FROM voedselpakket v
                JOIN klant k ON v.KlantID = k.KlantID
                JOIN gebruiker g ON k.GebruikerID = g.GebruikerID
                JOIN persoon p ON g.PersoonID = p.PersoonID
                LEFT JOIN voedselpakketproduct vpp ON v.VoedselpakketID = vpp.VoedselpakketID
                LEFT JOIN product pr ON vpp.ProductID = pr.ProductID
                LEFT JOIN leverancier l ON pr.LeverancierID = l.LeverancierID";

        if ($filter) {
            $sql .= " WHERE v.DatumUitgifte = :filter";
        }
        $sql .= " GROUP BY v.VoedselpakketID, p.Voornaam, p.Achternaam, v.DatumSamenstelling, v.DatumUitgifte";

        if ($filter) {
            $this->db->query($sql);
            $this->db->bind(':filter', $filter, PDO::PARAM_STR);
        } else {
            $this->db->query($sql);
        }

        return $this->db->resultSet();
    }
}
