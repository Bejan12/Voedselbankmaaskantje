<?php
class VoedselpakketModel
{
    private $db;
    public function __construct()
    {
        $this->db = new Database();
    }
    public function getAll($filter = null)
    {
        $sql = "SELECT v.VoedselpakketID, CONCAT(p.Voornaam, ' ', p.Achternaam) AS KlantNaam, v.DatumSamenstelling, v.DatumUitgifte FROM voedselpakket v JOIN klant k ON v.KlantID = k.KlantID JOIN gebruiker g ON k.GebruikerID = g.GebruikerID JOIN persoon p ON g.PersoonID = p.PersoonID";
        if ($filter === 'beschikbaar') {
            $sql .= " WHERE v.DatumUitgifte IS NULL";
        } elseif ($filter === 'niet_beschikbaar') {
            $sql .= " WHERE v.DatumUitgifte IS NOT NULL";
        }
        $sql .= " ORDER BY v.DatumSamenstelling DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
}
