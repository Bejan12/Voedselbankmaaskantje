<?php


class Magazijnvoorraad extends BaseController
{
    private $magazijnvoorraadModel;

    public function __construct()
    {
        $this->magazijnvoorraadModel = $this->model('MagazijnvoorraadModel');
    }

    public function index()
    {
        try {
            // Check voor succesbericht in URL
            $success = $_GET['success'] ?? null;
            
            // Haal alle voorraadgegevens op via de model
            $voorraadGegevens = $this->magazijnvoorraadModel->getVoorraadOverzicht();
            
            // Als er geen voorraadgegevens zijn via de stored procedure, probeer de normale methode
            if (empty($voorraadGegevens)) {
                $voorraadGegevens = $this->magazijnvoorraadModel->getVoorraadOverzichtGesorteerd();
            }
            
            // Haal alle producten op voor de dropdown
            $alleProducten = $this->magazijnvoorraadModel->getAlleProducten();
            
            // Als getAlleProducten leeg is, gebruik de voorraadgegevens
            if (empty($alleProducten) && !empty($voorraadGegevens)) {
                $alleProducten = $voorraadGegevens; // Gebruik dezelfde data
            }

            $data = [
                'title' => 'Overzicht Magazijnvoorraad',
                'voorraadGegevens' => $voorraadGegevens,
                'alleProducten' => $alleProducten,
                'heeftGegevens' => !empty($voorraadGegevens),
                'success' => $success
            ];

            $this->view('magazijnvoorraad/index', $data);

        } catch (Exception $e) {
            $data = [
                'title' => 'Overzicht Magazijnvoorraad',
                'voorraadGegevens' => [],
                'alleProducten' => [],
                'heeftGegevens' => false,
                'error' => 'Er is een fout opgetreden bij het laden van de voorraadgegevens: ' . $e->getMessage()
            ];

            $this->view('magazijnvoorraad/index', $data);
        }
    }

    public function nieuwProduct()
    {
        try {
            // Haal alle categorieën, leveranciers en allergieën op
            $categorieën = $this->magazijnvoorraadModel->getAlleCategorieën();
            $leveranciers = $this->magazijnvoorraadModel->getAlleLeveranciers();
            $allergieën = $this->magazijnvoorraadModel->getAlleAllergieën();

            $data = [
                'title' => 'Nieuw Product Toevoegen',
                'categorieën' => $categorieën,
                'leveranciers' => $leveranciers,
                'allergieën' => $allergieën
            ];

            $this->view('magazijnvoorraad/nieuwproduct', $data);

        } catch (Exception $e) {
            $data = [
                'title' => 'Nieuw Product Toevoegen',
                'categorieën' => [],
                'leveranciers' => [],
                'allergieën' => [],
                'error' => 'Er is een fout opgetreden bij het laden van het formulier: ' . $e->getMessage()
            ];

            $this->view('magazijnvoorraad/nieuwproduct', $data);
        }
    }

    public function voegProductToe()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Valideer en sanitize input
            $productnaam = trim($_POST['productnaam'] ?? '');
            $ean = trim($_POST['ean'] ?? '');
            $categorie_id = (int) ($_POST['categorie_id'] ?? 0);
            $leverancier_id = (int) ($_POST['leverancier_id'] ?? 0);
            $aantal_voorraad = (int) ($_POST['aantal_voorraad'] ?? 0);
            
            // Fix allergie_id handling - lege string wordt null
            $allergie_id = null;
            if (isset($_POST['allergie_id']) && $_POST['allergie_id'] !== '' && $_POST['allergie_id'] !== '0') {
                $allergie_id = (int) $_POST['allergie_id'];
            }

            // Bewaar formulierdata voor bij fout
            $formData = [
                'productnaam' => $productnaam,
                'ean' => $ean,
                'categorie_id' => $categorie_id,
                'leverancier_id' => $leverancier_id,
                'aantal_voorraad' => $aantal_voorraad,
                'allergie_id' => $allergie_id
            ];

            // Validatie
            $errors = [];

            if (empty($productnaam)) {
                $errors[] = 'Productnaam is verplicht';
            }

            if (empty($ean) || !preg_match('/^[0-9]{13}$/', $ean)) {
                $errors[] = 'EAN-code moet precies 13 cijfers bevatten';
            }

            if ($categorie_id <= 0) {
                $errors[] = 'Categorie is verplicht';
            }

            if ($leverancier_id <= 0) {
                $errors[] = 'Leverancier is verplicht';
            }

            if ($aantal_voorraad < 0) {
                $errors[] = 'Aantal in voorraad kan niet negatief zijn';
            }

            // Slimme categorie validatie
            if ($categorie_id > 0 && !empty($productnaam)) {
                $categorieValidatie = $this->validateProductCategory($productnaam, $categorie_id);
                if (!$categorieValidatie['isValid']) {
                    $errors[] = $categorieValidatie['message'];
                }
            }

            // Als er validatiefouten zijn
            if (!empty($errors)) {
                $data = [
                    'title' => 'Nieuw Product Toevoegen',
                    'categorieën' => $this->magazijnvoorraadModel->getAlleCategorieën(),
                    'leveranciers' => $this->magazijnvoorraadModel->getAlleLeveranciers(),
                    'allergieën' => $this->magazijnvoorraadModel->getAlleAllergieën(),
                    'formData' => $formData,
                    'error' => implode('<br>', $errors)
                ];

                $this->view('magazijnvoorraad/nieuwproduct', $data);
                return;
            }

            try {
                // Controleer of EAN al bestaat
                $bestaandProduct = $this->magazijnvoorraadModel->zoekProductOpEAN($ean);
                if ($bestaandProduct) {
                    $data = [
                        'title' => 'Nieuw Product Toevoegen',
                        'categorieën' => $this->magazijnvoorraadModel->getAlleCategorieën(),
                        'leveranciers' => $this->magazijnvoorraadModel->getAlleLeveranciers(),
                        'allergieën' => $this->magazijnvoorraadModel->getAlleAllergieën(),
                        'formData' => $formData,
                        'error' => 'Product met deze EAN-code bestaat al: ' . htmlspecialchars($bestaandProduct->ProductNaam)
                    ];

                    $this->view('magazijnvoorraad/nieuwproduct', $data);
                    return;
                }

                // Debug logging
                error_log("Controller: Toevoegen product - Naam: $productnaam, EAN: $ean, Cat: $categorie_id, Lev: $leverancier_id, All: " . ($allergie_id ?? 'NULL') . ", Voorr: $aantal_voorraad");

                // Voeg product toe
                $success = $this->magazijnvoorraadModel->voegProductToe(
                    $leverancier_id, 
                    $allergie_id, 
                    $categorie_id, 
                    $productnaam, 
                    $ean, 
                    $aantal_voorraad
                );

                if ($success) {
                    // Redirect naar overzicht met succesbericht
                    header('Location: ' . URLROOT . '/magazijnvoorraad?success=' . urlencode('Product "' . $productnaam . '" succesvol toegevoegd'));
                    exit;
                } else {
                    throw new Exception('Product kon niet worden opgeslagen in de database');
                }

            } catch (Exception $e) {
                error_log("Controller error bij toevoegen product: " . $e->getMessage());
                
                $data = [
                    'title' => 'Nieuw Product Toevoegen',
                    'categorieën' => $this->magazijnvoorraadModel->getAlleCategorieën(),
                    'leveranciers' => $this->magazijnvoorraadModel->getAlleLeveranciers(),
                    'allergieën' => $this->magazijnvoorraadModel->getAlleAllergieën(),
                    'formData' => $formData,
                    'error' => $e->getMessage()
                ];

                $this->view('magazijnvoorraad/nieuwproduct', $data);
            }
        } else {
            // Redirect als niet POST
            header('Location: ' . URLROOT . '/magazijnvoorraad/nieuwProduct');
            exit;
        }
    }

    /**
     * Valideer of een product bij de juiste categorie hoort - HARDE VALIDATIE
     */
    private function validateProductCategory($productnaam, $categorie_id)
    {
        try {
            // Haal categorie naam op
            $categorie = $this->magazijnvoorraadModel->getCategorieById($categorie_id);
            if (!$categorie) {
                return ['isValid' => false, 'message' => 'Onbekende categorie'];
            }

            $productNaamLower = strtolower($productnaam);
            $categorieNaamLower = strtolower($categorie->Naam);

            // Definieer trefwoorden per categorie (aangepast aan jouw database categorieën)
            $categoryKeywords = [
                'aardappelen, groente, fruit' => ['aardappel', 'wortel', 'ui', 'tomaat', 'sla', 'paprika', 'courgette', 'broccoli', 'spinazie', 'appel', 'banaan', 'sinaasappel', 'peer', 'druif', 'aardbei', 'kiwi', 'mango', 'ananas', 'fruit', 'groente'],
                'kaas, vleeswaren' => ['kaas', 'ham', 'worst', 'salami', 'vleeswaren', 'spek', 'biefstuk'],
                'zuivel, plantaardig en eieren' => ['melk', 'yoghurt', 'boter', 'room', 'kwark', 'karnemelk', 'zuivel', 'eieren', 'ei'],
                'bakkerij en banket' => ['brood', 'croissant', 'cake', 'taart', 'koek', 'banket', 'volkoren'],
                'frisdrank, sappen, koffie en thee' => ['sap', 'water', 'frisdrank', 'thee', 'koffie', 'bier', 'wijn', 'drank', 'appelsap'],
                'pasta, rijst en wereldkeuken' => ['pasta', 'rijst', 'muesli', 'havermout', 'couscous', 'quinoa', 'graan', 'spaghetti'],
                'soepen, sauzen, kruiden en olie' => ['soep', 'saus', 'kruiden', 'olie', 'azijn', 'tomatensoep'],
                'snoep, koek, chips en chocolade' => ['snoep', 'chocolade', 'chips', 'koek', 'koekjes'],
                'baby, verzorging en hygiëne' => ['baby', 'luier', 'verzorging', 'shampoo']
            ];

            // Controleer of de gekozen categorie logisch is
            if (isset($categoryKeywords[$categorieNaamLower])) {
                $keywords = $categoryKeywords[$categorieNaamLower];
                $isLogical = false;
                
                foreach ($keywords as $keyword) {
                    if (strpos($productNaamLower, $keyword) !== false) {
                        $isLogical = true;
                        break;
                    }
                }
                
                if (!$isLogical) {
                    // Zoek betere categorieën
                    $suggestedCategories = [];
                    foreach ($categoryKeywords as $catName => $keywords) {
                        foreach ($keywords as $keyword) {
                            if (strpos($productNaamLower, $keyword) !== false) {
                                $suggestedCategories[] = $catName;
                                break;
                            }
                        }
                    }
                    
                    if (!empty($suggestedCategories)) {
                        return [
                            'isValid' => false, 
                            'message' => "Product '{$productnaam}' past niet bij categorie '{$categorie->Naam}'. Kies een van deze categorieën: " . implode(', ', $suggestedCategories)
                        ];
                    } else {
                        return [
                            'isValid' => false, 
                            'message' => "Product '{$productnaam}' past niet bij categorie '{$categorie->Naam}'. Controleer de categorie keuze."
                        ];
                    }
                }
            }

            return ['isValid' => true, 'message' => ''];
            
        } catch (Exception $e) {
            error_log("Fout bij categorie validatie: " . $e->getMessage());
            return ['isValid' => true, 'message' => '']; // Bij fout, laat gewoon door
        }
    }

    // ... rest van de methods blijven hetzelfde ...


    public function zoekProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
            $productId = $_POST['product_id'];
            
            try {
                $product = $this->magazijnvoorraadModel->zoekProductOpID($productId);
                $alleProducten = $this->magazijnvoorraadModel->getAlleProducten();
                
                // Fallback voor alleProducten
                if (empty($alleProducten)) {
                    $alleProducten = $this->magazijnvoorraadModel->getVoorraadOverzichtGesorteerd();
                }
                
                $data = [
                    'title' => 'Zoekresultaat Magazijnvoorraad',
                    'voorraadGegevens' => $product ? [$product] : [],
                    'alleProducten' => $alleProducten,
                    'heeftGegevens' => !empty($product),
                    'geselecteerdProduct' => $productId
                ];

                $this->view('magazijnvoorraad/index', $data);

            } catch (Exception $e) {
                $data = [
                    'title' => 'Zoekresultaat Magazijnvoorraad',
                    'voorraadGegevens' => [],
                    'alleProducten' => [],
                    'heeftGegevens' => false,
                    'error' => 'Er is een fout opgetreden bij het zoeken: ' . $e->getMessage(),
                    'geselecteerdProduct' => $productId
                ];

                $this->view('magazijnvoorraad/index', $data);
            }
        } else {
            header('Location: ' . URLROOT . '/magazijnvoorraad');
            exit;
        }
    }

    public function zoekEAN()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ean'])) {
            $ean = trim($_POST['ean']);
            
            // Valideer EAN-code (13 cijfers)
            if (!preg_match('/^[0-9]{13}$/', $ean)) {
                // Haal alle producten op voor de dropdown (ook bij fout)
                $alleProducten = $this->magazijnvoorraadModel->getAlleProducten();
                if (empty($alleProducten)) {
                    $alleProducten = $this->magazijnvoorraadModel->getVoorraadOverzichtGesorteerd();
                }
                
                $data = [
                    'title' => 'Zoekresultaat Magazijnvoorraad',
                    'voorraadGegevens' => [],
                    'alleProducten' => $alleProducten,
                    'heeftGegevens' => false,
                    'error' => 'Ongeldige EAN-code. Een EAN-code moet precies 13 cijfers zijn.',
                    'zoekterm' => $ean
                ];

                $this->view('magazijnvoorraad/index', $data);
                return;
            }
            
            try {
                $product = $this->magazijnvoorraadModel->zoekProductOpEAN($ean);
                $alleProducten = $this->magazijnvoorraadModel->getAlleProducten();
                
                // Fallback voor alleProducten
                if (empty($alleProducten)) {
                    $alleProducten = $this->magazijnvoorraadModel->getVoorraadOverzichtGesorteerd();
                }

                $data = [
                    'title' => 'Zoekresultaat Magazijnvoorraad',
                    'voorraadGegevens' => $product ? [$product] : [],
                    'alleProducten' => $alleProducten,
                    'heeftGegevens' => !empty($product),
                    'zoekterm' => $ean
                ];

                $this->view('magazijnvoorraad/index', $data);

            } catch (Exception $e) {
                $data = [
                    'title' => 'Zoekresultaat Magazijnvoorraad',
                    'voorraadGegevens' => [],
                    'alleProducten' => [],
                    'heeftGegevens' => false,
                    'error' => 'Er is een fout opgetreden bij het zoeken: ' . $e->getMessage(),
                    'zoekterm' => $ean
                ];

                $this->view('magazijnvoorraad/index', $data);
            }
        } else {
            // Check voor succesbericht ook in de index
            $success = $_GET['success'] ?? null;
            if ($success) {
                // Laad normale index met succesbericht
                try {
                    $voorraadGegevens = $this->magazijnvoorraadModel->getVoorraadOverzicht();
                    if (empty($voorraadGegevens)) {
                        $voorraadGegevens = $this->magazijnvoorraadModel->getVoorraadOverzichtGesorteerd();
                    }
                    $alleProducten = $this->magazijnvoorraadModel->getAlleProducten();
                    if (empty($alleProducten) && !empty($voorraadGegevens)) {
                        $alleProducten = $voorraadGegevens;
                    }

                    $data = [
                        'title' => 'Overzicht Magazijnvoorraad',
                        'voorraadGegevens' => $voorraadGegevens,
                        'alleProducten' => $alleProducten,
                        'heeftGegevens' => !empty($voorraadGegevens),
                        'success' => $success
                    ];

                    $this->view('magazijnvoorraad/index', $data);
                } catch (Exception $e) {
                    header('Location: ' . URLROOT . '/magazijnvoorraad');
                    exit;
                }
            } else {
                header('Location: ' . URLROOT . '/magazijnvoorraad');
                exit;
            }
        }
    }

 
    public function wijzigProduct($productId = null)
    {
        if (!$productId) {
            header('Location: ' . URLROOT . '/magazijnvoorraad');
            exit;
        }

        try {
            // Haal product gegevens op
            $product = $this->magazijnvoorraadModel->getProductVoorWijziging($productId);
            
            if (!$product) {
                header('Location: ' . URLROOT . '/magazijnvoorraad?error=' . urlencode('Product niet gevonden'));
                exit;
            }

            // Haal alle categorieën, leveranciers en allergieën op
            $categorieën = $this->magazijnvoorraadModel->getAlleCategorieën();
            $leveranciers = $this->magazijnvoorraadModel->getAlleLeveranciers();
            $allergieën = $this->magazijnvoorraadModel->getAlleAllergieën();

            $data = [
                'title' => 'Product Wijzigen',
                'product' => $product,
                'categorieën' => $categorieën,
                'leveranciers' => $leveranciers,
                'allergieën' => $allergieën
            ];

            $this->view('magazijnvoorraad/wijzigproduct', $data);

        } catch (Exception $e) {
            header('Location: ' . URLROOT . '/magazijnvoorraad?error=' . urlencode('Fout bij laden product: ' . $e->getMessage()));
            exit;
        }
    }

    public function updateProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Valideer en sanitize input
            $productId = (int) ($_POST['product_id'] ?? 0);
            $productnaam = trim($_POST['productnaam'] ?? '');
            $categorie_id = (int) ($_POST['categorie_id'] ?? 0);
            $leverancier_id = (int) ($_POST['leverancier_id'] ?? 0);
            $aantal_voorraad = (int) ($_POST['aantal_voorraad'] ?? 0);
            
            // Fix allergie_id handling - lege string wordt null
            $allergie_id = null;
            if (isset($_POST['allergie_id']) && $_POST['allergie_id'] !== '' && $_POST['allergie_id'] !== '0') {
                $allergie_id = (int) $_POST['allergie_id'];
            }

            // Haal originele product op voor fallback
            $originalProduct = $this->magazijnvoorraadModel->getProductVoorWijziging($productId);
            if (!$originalProduct) {
                header('Location: ' . URLROOT . '/magazijnvoorraad?error=' . urlencode('Product niet gevonden'));
                exit;
            }

            // Bewaar formulierdata voor bij fout
            $formData = [
                'productnaam' => $productnaam,
                'categorie_id' => $categorie_id,
                'leverancier_id' => $leverancier_id,
                'aantal_voorraad' => $aantal_voorraad,
                'allergie_id' => $allergie_id
            ];

            // Validatie
            $errors = [];

            if ($productId <= 0) {
                $errors[] = 'Ongeldig product ID';
            }

            if (empty($productnaam)) {
                $errors[] = 'Productnaam is verplicht';
            }

            if ($categorie_id <= 0) {
                $errors[] = 'Categorie is verplicht';
            }

            if ($leverancier_id <= 0) {
                $errors[] = 'Leverancier is verplicht';
            }

            if ($aantal_voorraad < 0) {
                $errors[] = 'Aantal in voorraad kan niet negatief zijn';
            }

            // Slimme categorie validatie (alleen als productnaam is gewijzigd)
            if ($categorie_id > 0 && !empty($productnaam) && $productnaam !== $originalProduct->ProductNaam) {
                $categorieValidatie = $this->validateProductCategory($productnaam, $categorie_id);
                if (!$categorieValidatie['isValid']) {
                    $errors[] = $categorieValidatie['message'];
                }
            }

            // Als er validatiefouten zijn
            if (!empty($errors)) {
                $data = [
                    'title' => 'Product Wijzigen',
                    'product' => $originalProduct,
                    'categorieën' => $this->magazijnvoorraadModel->getAlleCategorieën(),
                    'leveranciers' => $this->magazijnvoorraadModel->getAlleLeveranciers(),
                    'allergieën' => $this->magazijnvoorraadModel->getAlleAllergieën(),
                    'formData' => $formData,
                    'error' => implode('<br>', $errors)
                ];

                $this->view('magazijnvoorraad/wijzigproduct', $data);
                return;
            }

            try {
                // Controleer of productnaam al bestaat voor ander product
                if ($this->magazijnvoorraadModel->productnaamBestaatVoorAnderProduct($productnaam, $productId)) {
                    $data = [
                        'title' => 'Product Wijzigen',
                        'product' => $originalProduct,
                        'categorieën' => $this->magazijnvoorraadModel->getAlleCategorieën(),
                        'leveranciers' => $this->magazijnvoorraadModel->getAlleLeveranciers(),
                        'allergieën' => $this->magazijnvoorraadModel->getAlleAllergieën(),
                        'formData' => $formData,
                        'error' => 'Product niet succesvol gewijzigd: Er bestaat al een product met deze naam'
                    ];

                    $this->view('magazijnvoorraad/wijzigproduct', $data);
                    return;
                }

                // Debug logging
                error_log("Controller: Wijzigen product ID $productId - Naam: $productnaam, Cat: $categorie_id, Lev: $leverancier_id, All: " . ($allergie_id ?? 'NULL') . ", Voorr: $aantal_voorraad");

                // Wijzig product
                $success = $this->magazijnvoorraadModel->wijzigProduct(
                    $productId,
                    $leverancier_id, 
                    $allergie_id, 
                    $categorie_id, 
                    $productnaam, 
                    $aantal_voorraad
                );

                if ($success) {
                    // Redirect naar overzicht met succesbericht
                    header('Location: ' . URLROOT . '/magazijnvoorraad?success=' . urlencode('Product "' . $productnaam . '" succesvol gewijzigd'));
                    exit;
                } else {
                    throw new Exception('Product kon niet worden gewijzigd in de database');
                }

            } catch (Exception $e) {
                error_log("Controller error bij wijzigen product: " . $e->getMessage());
                
                $data = [
                    'title' => 'Product Wijzigen',
                    'product' => $originalProduct,
                    'categorieën' => $this->magazijnvoorraadModel->getAlleCategorieën(),
                    'leveranciers' => $this->magazijnvoorraadModel->getAlleLeveranciers(),
                    'allergieën' => $this->magazijnvoorraadModel->getAlleAllergieën(),
                    'formData' => $formData,
                    'error' => 'Product niet succesvol gewijzigd: ' . $e->getMessage()
                ];

                $this->view('magazijnvoorraad/wijzigproduct', $data);
            }
        } else {
            // Redirect als niet POST
            header('Location: ' . URLROOT . '/magazijnvoorraad');
            exit;
        }
    }

    public function verwijderProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
            $productId = (int) $_POST['product_id'];
            
            if ($productId <= 0) {
                header('Location: ' . URLROOT . '/magazijnvoorraad?error=' . urlencode('Ongeldig product ID'));
                exit;
            }
            
            try {
                // Haal product gegevens op voor logging en foutmeldingen
                $product = $this->magazijnvoorraadModel->getProductVoorVerwijdering($productId);
                if (!$product) {
                    header('Location: ' . URLROOT . '/magazijnvoorraad?error=' . urlencode('Product niet gevonden'));
                    exit;
                }
                
                error_log("Poging tot verwijderen product: ID=$productId, Naam=" . $product->ProductNaam);
                
                // Controleer of product gekoppeld is aan voedselpakketten
                if ($this->magazijnvoorraadModel->isProductGekoppeldAanVoedselpakket($productId)) {
                    $aantalPakketten = $this->magazijnvoorraadModel->getAantalVoedselpakkettenVoorProduct($productId);
                    $errorMsg = 'Product is al gekoppeld aan een voedselpakket en kan niet worden verwijderd';
                    if ($aantalPakketten > 0) {
                        $errorMsg .= " (gebruikt in $aantalPakketten voedselpakket" . ($aantalPakketten > 1 ? 'ten' : '') . ")";
                    }
                    
                    error_log("Product kan niet verwijderd worden - gekoppeld aan voedselpakketten: " . $product->ProductNaam);
                    header('Location: ' . URLROOT . '/magazijnvoorraad?error=' . urlencode($errorMsg));
                    exit;
                }
                
                // Probeer product te verwijderen
                $success = $this->magazijnvoorraadModel->verwijderProduct($productId);
                
                if ($success) {
                    $successMsg = 'Product "' . $product->ProductNaam . '" succesvol verwijderd';
                    error_log("Product succesvol verwijderd: " . $product->ProductNaam);
                    header('Location: ' . URLROOT . '/magazijnvoorraad?success=' . urlencode($successMsg));
                    exit;
                } else {
                    throw new Exception('Product kon niet worden verwijderd');
                }
                
            } catch (Exception $e) {
                error_log("Controller error bij verwijderen product: " . $e->getMessage());
                header('Location: ' . URLROOT . '/magazijnvoorraad?error=' . urlencode($e->getMessage()));
                exit;
            }
            
        } else {
            // Redirect als niet POST of geen product_id
            header('Location: ' . URLROOT . '/magazijnvoorraad?error=' . urlencode('Ongeldige verwijder aanvraag'));
            exit;
        }
    }

}