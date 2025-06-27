<?php

class Homepages extends BaseController
{
    private $homepageModel;

    public function __construct()
    {
        $this->homepageModel = $this->model('HomepageModel');
    }

    public function index($gebruikerID = 1) // temporary hardcoded user ID for demo
    {
        // Get user information
        $gebruikerInfo = $this->homepageModel->getGebruikerInfo($gebruikerID);
        
        // Check if user exists
        $gebruikerError = null;
        if (!$gebruikerInfo) {
            $gebruikerError = 'Gebruiker niet gevonden.';
        }
        
        // Get available functions based on user role
        $toegankelijkeFuncties = $this->homepageModel->getToegankelijkeFuncties($gebruikerID);
        
        // Get inventory overview
        $voorraadOverzicht = $this->homepageModel->getProductVoorraadOverzicht();

        // Get food packages (without filter initially)
        $voedselpakketten = $this->homepageModel->getVoedselpakkettenMetFilter();

        $data = [
            'title' => 'Dashboard Voedselbank',
            'gebruiker' => $gebruikerInfo,
            'gebruikerError' => $gebruikerError,
            'functies' => $toegankelijkeFuncties,
            'voorraad' => $voorraadOverzicht,
            'voedselpakketten' => $voedselpakketten,
            'filterMessage' => ''
        ];

        $this->view('homepages/index', $data);
    }

    public function filterPakketten()
    {
        $gebruikerID = 1; // temporary hardcoded user ID for demo
        $filter = isset($_POST['filter']) ? $_POST['filter'] : null;
        
        // Get filtered packages
        $voedselpakketten = $this->homepageModel->getVoedselpakkettenMetFilter($filter);
        
        $gebruikerInfo = $this->homepageModel->getGebruikerInfo($gebruikerID);
        $gebruikerError = null;
        if (!$gebruikerInfo) {
            $gebruikerError = 'Gebruiker niet gevonden.';
        }
        $data = [
            'title' => 'Dashboard Voedselbank',
            'gebruiker' => $gebruikerInfo,
            'gebruikerError' => $gebruikerError,
            'functies' => $this->homepageModel->getToegankelijkeFuncties($gebruikerID),
            'voorraad' => $this->homepageModel->getProductVoorraadOverzicht(),
            'voedselpakketten' => $voedselpakketten,
            'filterMessage' => empty($voedselpakketten) ? 'Geen voedselpakketten gevonden voor deze filter' : ''
        ];

        $this->view('homepages/index', $data);
    }
}