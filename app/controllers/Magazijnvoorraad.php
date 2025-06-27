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
            $alleProducten = $this->magazijnvoorraadModel->getAlleProducten();

            // Debug: log wat we krijgen
            error_log("Aantal voorraad gegevens: " . count($voorraadGegevens));
            error_log("Aantal alle producten: " . count($alleProducten));

            $data = [
                'title' => 'Overzicht Magazijnvoorraad',
                'voorraadGegevens' => $voorraadGegevens,
                'alleProducten' => $alleProducten,
                'heeftGegevens' => !empty($voorraadGegevens)
            ];

            $this->view('magazijnvoorraad/index', $data);

        } catch (Exception $e) {
            error_log("Fout in index: " . $e->getMessage());
            $data = [
                'title' => 'Overzicht Magazijnvoorraad',
                'voorraadGegevens' => [],
                'alleProducten' => [],
                'heeftGegevens' => false,
                'error' => 'Er is een fout opgetreden bij het laden van de voorraadgegevens.'
            ];

            $this->view('magazijnvoorraad/index', $data);
        }
    }

    public function zoekProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
            $productId = $_POST['product_id'];
            
            try {
                $product = $this->magazijnvoorraadModel->zoekProductOpID($productId);
                $alleProducten = $this->magazijnvoorraadModel->getAlleProducten();
                
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
                    'error' => 'Er is een fout opgetreden bij het zoeken.',
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
                $data = [
                    'title' => 'Zoekresultaat Magazijnvoorraad',
                    'voorraadGegevens' => [],
                    'alleProducten' => $this->magazijnvoorraadModel->getAlleProducten(),
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
                    'error' => 'Er is een fout opgetreden bij het zoeken.',
                    'zoekterm' => $ean
                ];

                $this->view('magazijnvoorraad/index', $data);
            }
        } else {
            header('Location: ' . URLROOT . '/magazijnvoorraad');
            exit;
        }
    }
}