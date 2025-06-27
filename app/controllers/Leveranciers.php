<?php

class Leveranciers extends BaseController
{
    private $leverancierModel;

    public function __construct()
    {
        $this->leverancierModel = $this->model('LeverancierModel');
    }

    public function index()
    {
        try {
            // Haal alle leveranciers op
            $leveranciers = $this->leverancierModel->getAllLeveranciers();

            if (empty($leveranciers)) {
                // Geen leveranciers gevonden, toon error view
                $data = [
                    'title' => 'Geen Leveranciers',
                    'error_message' => 'Er zijn nog geen leveranciers, probeer het later opnieuw!',
                    'redirect_url' => URLROOT . '/homepages/index'
                ];
                $this->view('leveranciers/error', $data);
                return;
            }

            $data = [
                'title' => 'Overzicht Leveranciers',
                'leveranciers' => $leveranciers
            ];

            $this->view('leveranciers/index', $data);
        } catch (Exception $e) {
            // Database error occurred
            $data = [
                'title' => 'Database Error',
                'error_message' => 'Er zijn nog geen leveranciers, probeer het later opnieuw!',
                'redirect_url' => URLROOT . '/homepages/index'
            ];

            $this->view('leveranciers/error', $data);
        }
    }
}
