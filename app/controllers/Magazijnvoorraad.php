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

          
            // In de index() method, voeg dit toe na try { 
            // Check voor succesbericht in URL
            $success = $_GET['success'] ?? null;

            $data = [
            'title' => 'Overzicht Magazijnvoorraad',
            'voorraadGegevens' => $voorraadGegevens,
            'alleProducten' => $alleProducten,
            'heeftGegevens' => !empty($voorraadGegevens),
            'success' => $success  // Voeg deze regel toe
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
            $allergie_id = !empty($_POST['allergie_id']) ? (int) $_POST['allergie_id'] : null;

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
                        'error' => 'Product met deze EAN-code bestaat al'
                    ];

                    $this->view('magazijnvoorraad/nieuwproduct', $data);
                    return;
                }

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
                    header('Location: ' . URLROOT . '/magazijnvoorraad?success=' . urlencode('Product succesvol toegevoegd'));
                    exit;
                } else {
                    throw new Exception('Onbekende fout bij toevoegen product');
                }

            } catch (Exception $e) {
                $data = [
                    'title' => 'Nieuw Product Toevoegen',
                    'categorieën' => $this->magazijnvoorraadModel->getAlleCategorieën(),
                    'leveranciers' => $this->magazijnvoorraadModel->getAlleLeveranciers(),
                    'allergieën' => $this->magazijnvoorraadModel->getAlleAllergieën(),
                    'formData' => $formData,
                    'error' => 'Er is een fout opgetreden: ' . $e->getMessage()
                ];

                $this->view('magazijnvoorraad/nieuwproduct', $data);
            }
        } else {
            // Redirect als niet POST
            header('Location: ' . URLROOT . '/magazijnvoorraad/nieuwProduct');
            exit;
        }
    }

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

                // Check voor succesbericht in URL
                $success = $_GET['success'] ?? null;
                
                $data = [
                    'title' => 'Zoekresultaat Magazijnvoorraad',
                    'voorraadGegevens' => $product ? [$product] : [],
                    'alleProducten' => $alleProducten,
                    'heeftGegevens' => !empty($product),
                    'zoekterm' => $ean,
                    'success' => $success
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
}