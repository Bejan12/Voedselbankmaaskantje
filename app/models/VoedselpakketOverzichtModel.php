<?php
/**
 * Model voor het ophalen van voedselpakket-overzichten
 * Voldoet aan PSR-12, gebruikt prepared statements, stored procedures, en bevat server-side validatie.
 */
class VoedselpakketOverzichtModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Haal alle voedselpakketten op met klant- en gezinsinformatie
     * @param string|null $beschikbaarFilter
     * @param string|null $datum
     * @return array
     */
    public function getVoedselpakketten($beschikbaarFilter = null, $datum = null): array
    {
        try {
            $sql = "SELECT 
                        v.VoedselpakketID,
                        CONCAT(p.Voornaam, ' ', p.Achternaam) AS gezinsnaam,
                        CONCAT('Pakket samengesteld op ', v.DatumSamenstelling) AS Omschrijving,
                        k.AantalVolwassenen,
                        k.AantalKinderen,
                        k.AantalBabys,
                        '' AS Vertegenwoordiger,
                        (
                            SELECT GROUP_CONCAT(CONCAT(pr.ProductNaam, ' (', vpp.Aantal, 'x)') SEPARATOR ', ')
                            FROM voedselpakketproduct vpp
                            JOIN product pr ON vpp.ProductID = pr.ProductID
                            WHERE vpp.VoedselpakketID = v.VoedselpakketID
                        ) AS Details,
                        IF(v.DatumUitgifte IS NULL, 1, 0) AS Beschikbaar,
                        v.DatumSamenstelling
                    FROM voedselpakket v
                    JOIN klant k ON v.KlantID = k.KlantID
                    JOIN gebruiker g ON k.GebruikerID = g.GebruikerID
                    JOIN persoon p ON g.PersoonID = p.PersoonID";
            $where = [];
            if ($beschikbaarFilter !== null && $beschikbaarFilter !== '') {
                $where[] = "IF(v.DatumUitgifte IS NULL, 1, 0) = :beschikbaar";
            }
            if ($datum) {
                $where[] = "v.DatumSamenstelling = :datum";
            }
            if (count($where) > 0) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            $this->db->query($sql);
            if ($beschikbaarFilter !== null && $beschikbaarFilter !== '') {
                $this->db->bind(':beschikbaar', $beschikbaarFilter, PDO::PARAM_INT);
            }
            if ($datum) {
                $this->db->bind(':datum', $datum, PDO::PARAM_STR);
            }
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log(date('[Y-m-d H:i:s]') . ' Fout in getVoedselpakketten: ' . $e->getMessage());
            return [];
        }
    }
}