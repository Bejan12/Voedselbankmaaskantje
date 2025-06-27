<?php


class Magazijnvoorraad
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Haalt alle voorraadgegevens op met behulp van de stored procedure
     */
    public function getVoorraadOverzicht()
    {
        try {
            $this->db->query('CALL GetProductVoorraadOverzicht()');
            return $this->db->resultSet();
        } catch (Exception $e) {
            // Log de error en return een lege array
            error_log("Fout bij ophalen voorraadoverzicht: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Zoekt een product op basis van EAN-code
     */
    public function zoekProductOpEAN($ean)
    {
        try {
            $this->db->query('
                SELECT 
                    p.ProductID,
                    p.ProductNaam,
                    p.EAN,
                    c.Naam AS Categorie,
                    p.AantalInVoorraad,
                    l.Bedrijfsnaam AS Leverancier
                FROM Product p
                JOIN Categorie c ON p.CategorieID = c.CategorieID
                JOIN Leverancier l ON p.LeverancierID = l.LeverancierID
                WHERE p.EAN = :ean
            ');
            
            $this->db->bind(':ean', $ean);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Fout bij zoeken product op EAN: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Haalt voorraadgegevens op gesorteerd op een specifieke kolom
     */
    public function getVoorraadOverzichtGesorteerd($sorteerKolom = 'ProductNaam', $sorteerRichting = 'ASC')
    {
        try {
            // Whitelist van toegestane sorteerkolommen voor security
            $toegestaneKolommen = ['ProductNaam', 'EAN', 'Categorie', 'AantalInVoorraad', 'Leverancier'];
            $toegestaneRichtingen = ['ASC', 'DESC'];

            if (!in_array($sorteerKolom, $toegestaneKolommen)) {
                $sorteerKolom = 'ProductNaam';
            }

            if (!in_array(strtoupper($sorteerRichting), $toegestaneRichtingen)) {
                $sorteerRichting = 'ASC';
            }

            $this->db->query("
                SELECT 
                    p.ProductID,
                    p.ProductNaam,
                    p.EAN,
                    c.Naam AS Categorie,
                    p.AantalInVoorraad,
                    l.Bedrijfsnaam AS Leverancier
                FROM Product p
                JOIN Categorie c ON p.CategorieID = c.CategorieID
                JOIN Leverancier l ON p.LeverancierID = l.LeverancierID
                ORDER BY {$sorteerKolom} {$sorteerRichting}
            ");

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Fout bij ophalen gesorteerd voorraadoverzicht: " . $e->getMessage());
            return [];
        }
    }
}