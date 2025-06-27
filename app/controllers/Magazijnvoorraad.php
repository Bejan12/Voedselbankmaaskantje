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

            $data = [
                'title' => 'Overzicht Magazijnvoorraad',
                'voorraadGegevens' => $voorraadGegevens,
                'alleProducten' => $alleProducten,
                'heeftGegevens' => !empty($voorraadGegevens)
            ];

            $this->view('magazijnvoorraad/index', $data);

        } catch (Exception $e) {
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
}