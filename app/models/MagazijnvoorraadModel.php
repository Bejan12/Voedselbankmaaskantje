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
     * Haalt een specifieke categorie op basis van ID
     */
    public function getCategorieById($categorieId)
    {
        try {
            $this->db->query('
                SELECT CategorieID, Naam 
                FROM Categorie 
                WHERE CategorieID = :categorie_id
            ');
            
            $this->db->bind(':categorie_id', $categorieId);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Fout bij ophalen categorie: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Voegt een nieuw product toe
     */
    public function voegProductToe($leverancier_id, $allergie_id, $categorie_id, $productnaam, $ean, $aantal_voorraad)
    {
        try {
            // Als allergie_id null is, zet het naar NULL voor de database
            if ($allergie_id === 0 || $allergie_id === '0') {
                $allergie_id = null;
            }
            
            // Debug logging
            error_log("Toevoegen product: Leverancier=$leverancier_id, Allergie=$allergie_id, Categorie=$categorie_id, Naam=$productnaam, EAN=$ean, Voorraad=$aantal_voorraad");
            
            // Probeer eerst normale INSERT (stored procedure kan problemen hebben)
            $this->db->query('
                INSERT INTO Product (LeverancierID, AllergieID, CategorieID, ProductNaam, EAN, AantalInVoorraad) 
                VALUES (:leverancier_id, :allergie_id, :categorie_id, :productnaam, :ean, :aantal_voorraad)
            ');
            
            $this->db->bind(':leverancier_id', $leverancier_id);
            $this->db->bind(':allergie_id', $allergie_id, $allergie_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $this->db->bind(':categorie_id', $categorie_id);
            $this->db->bind(':productnaam', $productnaam);
            $this->db->bind(':ean', $ean);
            $this->db->bind(':aantal_voorraad', $aantal_voorraad);
            
            $success = $this->db->execute();
            
            if ($success) {
                // Probeer ook toe te voegen aan voedselopslag tabel als die bestaat
                try {
                    $this->db->query('
                        INSERT INTO Voedselopslag (ProductID, AantalInMagazijn, LaatsteAanleverDatum) 
                        VALUES (LAST_INSERT_ID(), :aantal_voorraad, CURDATE())
                    ');
                    $this->db->bind(':aantal_voorraad', $aantal_voorraad);
                    $this->db->execute();
                    error_log("Product succesvol toegevoegd aan voedselopslag");
                } catch (Exception $e) {
                    error_log("Fout bij toevoegen aan voedselopslag (niet kritiek): " . $e->getMessage());
                    // Dit is niet kritiek, dus we gaan door
                }
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("Fout bij toevoegen product (details): " . $e->getMessage());
            
            // Controleer specifieke fouten
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if (strpos($e->getMessage(), 'ProductNaam') !== false) {
                    throw new Exception('Er bestaat al een product met deze naam');
                } elseif (strpos($e->getMessage(), 'EAN') !== false) {
                    throw new Exception('Er bestaat al een product met deze EAN-code');
                } else {
                    throw new Exception('Dit product bestaat al in het systeem');
                }
            } elseif (strpos($e->getMessage(), 'foreign key constraint') !== false || strpos($e->getMessage(), 'Cannot add or update') !== false) {
                if (strpos($e->getMessage(), 'LeverancierID') !== false) {
                    throw new Exception('Onbekende leverancier geselecteerd');
                } elseif (strpos($e->getMessage(), 'CategorieID') !== false) {
                    throw new Exception('Onbekende categorie geselecteerd');
                } elseif (strpos($e->getMessage(), 'AllergieID') !== false) {
                    throw new Exception('Onbekende allergie geselecteerd');
                } else {
                    throw new Exception('Ongeldige gegevens: controleer leverancier, categorie en allergie');
                }
            } else {
                throw new Exception('Database fout: ' . $e->getMessage());
            }
        }
    }

    /**
     * Controleert of een EAN-code al bestaat
     */
    public function eanBestaat($ean)
    {
        try {
            $this->db->query('SELECT COUNT(*) as count FROM Product WHERE EAN = :ean');
            $this->db->bind(':ean', $ean);
            $result = $this->db->single();
            return $result->count > 0;
        } catch (Exception $e) {
            error_log("Fout bij controleren EAN: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Genereert een unieke EAN-code
     */
    public function generateUniqueEAN()
    {
        $maxAttempts = 10;
        $attempts = 0;
        
        do {
            $ean = $this->generateEANCode();
            $attempts++;
        } while ($this->eanBestaat($ean) && $attempts < $maxAttempts);
        
        if ($attempts >= $maxAttempts) {
            throw new Exception('Kon geen unieke EAN-code genereren');
        }
        
        return $ean;
    }

    /**
     * Genereert een EAN-13 code met check digit
     */
    private function generateEANCode()
    {
        // Genereer 12 willekeurige cijfers
        $ean12 = '';
        for ($i = 0; $i < 12; $i++) {
            $ean12 .= mt_rand(0, 9);
        }
        
        // Bereken check digit
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$ean12[$i] * ($i % 2 === 0 ? 1 : 3);
        }
        $checkDigit = (10 - ($sum % 10)) % 10;
        
        return $ean12 . $checkDigit;
    }
}