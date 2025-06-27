<?php


class Magazijnvoorraad extends BaseController
{
    private $magazijnvoorraadModel;

    public function __construct()
    {
        $this->magazijnvoorraadModel = $this->model('Magazijnvoorraad');
    }

    // ...existing code...
    public function index()
    {
        try {
            // Haal alle voorraadgegevens op via de model
            $voorraadGegevens = $this->magazijnvoorraadModel->getVoorraadOverzicht();

            $data = [
                'title' => 'Overzicht Magazijnvoorraad',
                'voorraadGegevens' => $voorraadGegevens,
                'heeftGegevens' => !empty($voorraadGegevens)
            ];

            $this->view('magazijnvoorraad/index', $data);

        } catch (Exception $e) {
            $data = [
                'title' => 'Overzicht Magazijnvoorraad',
                'voorraadGegevens' => [],
                'heeftGegevens' => false,
                'error' => 'Er is een fout opgetreden bij het laden van de voorraadgegevens.'
            ];

            $this->view('magazijnvoorraad/index', $data);
        }
    }

    public function zoekProduct($ean = null)
    {
        if (!$ean) {
            header('Location: ' . URLROOT . '/magazijnvoorraad');
            exit;
        }

        try {
            $product = $this->magazijnvoorraadModel->zoekProductOpEAN($ean);
            
            $data = [
                'title' => 'Zoekresultaat Magazijnvoorraad',
                'voorraadGegevens' => $product ? [$product] : [],
                'heeftGegevens' => !empty($product),
                'zoekterm' => $ean
            ];

            $this->view('magazijnvoorraad/index', $data);

        } catch (Exception $e) {
            $data = [
                'title' => 'Zoekresultaat Magazijnvoorraad',
                'voorraadGegevens' => [],
                'heeftGegevens' => false,
                'error' => 'Er is een fout opgetreden bij het zoeken.',
                'zoekterm' => $ean
            ];

            $this->view('magazijnvoorraad/index', $data);
        }
    }
}