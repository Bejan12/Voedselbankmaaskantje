<?php


class MagazijnvoorraadModel
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

    /**
     * Haalt alle producten op voor de dropdown
     */
    public function getAlleProducten()
    {
        try {
            $this->db->query('
                SELECT 
                    p.ProductID,
                    p.ProductNaam,
                    p.EAN
                FROM Product p
                ORDER BY p.ProductNaam ASC
            ');
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Fout bij ophalen alle producten: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Zoekt een product op basis van ProductID
     */
    public function zoekProductOpID($productId)
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
                WHERE p.ProductID = :product_id
            ');
            
            $this->db->bind(':product_id', $productId);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Fout bij zoeken product op ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Haalt alle categorieën op
     */
    public function getAlleCategorieën()
    {
        try {
            $this->db->query('
                SELECT CategorieID, Naam 
                FROM Categorie 
                ORDER BY Naam ASC
            ');
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Fout bij ophalen categorieën: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Haalt alle leveranciers op
     */
    public function getAlleLeveranciers()
    {
        try {
            $this->db->query('
                SELECT LeverancierID, Bedrijfsnaam 
                FROM Leverancier 
                ORDER BY Bedrijfsnaam ASC
            ');
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Fout bij ophalen leveranciers: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Haalt alle allergieën op
     */
    public function getAlleAllergieën()
    {
        try {
            $this->db->query('
                SELECT AllergieID, Naam 
                FROM Allergie 
                ORDER BY Naam ASC
            ');
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Fout bij ophalen allergieën: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Voegt een nieuw product toe
     */
    public function voegProductToe($leverancier_id, $allergie_id, $categorie_id, $productnaam, $ean, $aantal_voorraad)
    {
        try {
            // Gebruik de stored procedure als die bestaat
            try {
                $this->db->query('CALL VoegProductToe(:leverancier_id, :allergie_id, :categorie_id, :productnaam, :ean, :aantal_voorraad)');
                $this->db->bind(':leverancier_id', $leverancier_id);
                $this->db->bind(':allergie_id', $allergie_id);
                $this->db->bind(':categorie_id', $categorie_id);
                $this->db->bind(':productnaam', $productnaam);
                $this->db->bind(':ean', $ean);
                $this->db->bind(':aantal_voorraad', $aantal_voorraad);
                
                return $this->db->execute();
            } catch (Exception $e) {
                // Als stored procedure niet werkt, gebruik normale INSERT
                error_log("Stored procedure failed, using normal INSERT: " . $e->getMessage());
                
                $this->db->query('
                    INSERT INTO Product (LeverancierID, AllergieID, CategorieID, ProductNaam, EAN, AantalInVoorraad) 
                    VALUES (:leverancier_id, :allergie_id, :categorie_id, :productnaam, :ean, :aantal_voorraad)
                ');
                
                $this->db->bind(':leverancier_id', $leverancier_id);
                $this->db->bind(':allergie_id', $allergie_id);
                $this->db->bind(':categorie_id', $categorie_id);
                $this->db->bind(':productnaam', $productnaam);
                $this->db->bind(':ean', $ean);
                $this->db->bind(':aantal_voorraad', $aantal_voorraad);
                
                $success = $this->db->execute();
                
                // Voeg ook toe aan voedselopslag tabel
                if ($success) {
                    try {
                        $this->db->query('
                            INSERT INTO Voedselopslag (ProductID, AantalInMagazijn, LaatsteAanleverDatum) 
                            VALUES (LAST_INSERT_ID(), :aantal_voorraad, CURDATE())
                        ');
                        $this->db->bind(':aantal_voorraad', $aantal_voorraad);
                        $this->db->execute();
                    } catch (Exception $e) {
                        error_log("Fout bij toevoegen aan voedselopslag: " . $e->getMessage());
                        // Dit is niet kritiek, dus we gaan door
                    }
                }
                
                return $success;
            }
        } catch (Exception $e) {
            error_log("Fout bij toevoegen product: " . $e->getMessage());
            return false;
        }
    }
}