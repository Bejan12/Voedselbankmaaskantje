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
        // Haal alle leveranciers op
        $leveranciers = $this->leverancierModel->getAllLeveranciers();

        $data = [
            'title' => 'Overzicht Leveranciers',
            'leveranciers' => $leveranciers
        ];

        $this->view('leveranciers/index', $data);
    }
}
