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

    /**
     * Haal alle klanten op voor dropdown
     * @return array
     */
    public function getKlanten(): array
    {
        try {
            $sql = "SELECT 
                        k.KlantID,
                        CONCAT(p.Voornaam, ' ', p.Achternaam) AS gezinsnaam
                    FROM klant k
                    JOIN gebruiker g ON k.GebruikerID = g.GebruikerID
                    JOIN persoon p ON g.PersoonID = p.PersoonID
                    ORDER BY p.Achternaam, p.Voornaam";
            
            $this->db->query($sql);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log(date('[Y-m-d H:i:s]') . ' Fout in getKlanten: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Haal alle producten op voor dropdown
     * @return array
     */
    public function getProducten(): array
    {
        try {
            $sql = "SELECT 
                        ProductID,
                        ProductNaam
                    FROM product
                    ORDER BY ProductNaam";
            
            $this->db->query($sql);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log(date('[Y-m-d H:i:s]') . ' Fout in getProducten: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Voeg nieuw voedselpakket toe aan database
     * @param int $klantId
     * @param string $datum
     * @param array $producten
     * @param array $aantallen
     * @return bool
     */
    public function voegVoedselpakketToe($klantId, $datum, $producten, $aantallen): bool
    {
        try {
            // Start transaction voor consistentie
            $this->db->query("START TRANSACTION");
            $this->db->execute();
            
            // Voeg voedselpakket toe
            $this->db->query("INSERT INTO voedselpakket (KlantID, DatumSamenstelling, DatumUitgifte) VALUES (:klantId, :datum, NULL)");
            $this->db->bind(':klantId', $klantId);
            $this->db->bind(':datum', $datum);
            $success = $this->db->execute();
            
            if (!$success) {
                throw new Exception("Kon voedselpakket niet toevoegen");
            }
            
            // Haal het nieuwe voedselpakket ID op
            $this->db->query("SELECT LAST_INSERT_ID() as id");
            $result = $this->db->single();
            $voedselpakketId = $result->id;
            
            // Voeg producten toe aan voedselpakketproduct tabel
            for ($i = 0; $i < count($producten); $i++) {
                $this->db->query("INSERT INTO voedselpakketproduct (VoedselpakketID, ProductID, Aantal) VALUES (:voedselpakketId, :productId, :aantal)");
                $this->db->bind(':voedselpakketId', $voedselpakketId);
                $this->db->bind(':productId', $producten[$i]);
                $this->db->bind(':aantal', $aantallen[$i]);
                $productSuccess = $this->db->execute();
                
                if (!$productSuccess) {
                    throw new Exception("Kon product niet toevoegen aan voedselpakket");
                }
            }
            
            // Commit transaction
            $this->db->query("COMMIT");
            $this->db->execute();
            
            return true;
        } catch (Exception $e) {
            // Rollback transaction bij fout
            $this->db->query("ROLLBACK");
            $this->db->execute();
            error_log(date('[Y-m-d H:i:s]') . ' Fout in voegVoedselpakketToe: ' . $e->getMessage());
            return false;
        }
    }
}