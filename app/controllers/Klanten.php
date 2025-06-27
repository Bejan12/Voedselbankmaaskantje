<?php

class Klanten extends BaseController
{
    private $klantenModel;

    public function __construct()
    {
        $this->klantenModel = $this->model('klantenModels');
    }

    public function index()
    {
        $klanten = $this->klantenModel->getKlanten();
        $data = [
            'title' => 'Overzicht Klanten',
            'klanten' => $klanten
        ];

        if (empty($klanten)) {
            $data['error'] = 'Er zijn nog geen klanten beschikbaar.';
        }

        $this->view('klanten/index', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Formulier is verzonden, valideer invoer
            $voornaam = trim($_POST['voornaam'] ?? '');
            $achternaam = trim($_POST['achternaam'] ?? '');
            $adres = trim($_POST['adres'] ?? '');
            $telefoon = trim($_POST['telefoon'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $aantalVolwassenen = trim($_POST['aantal_volwassenen'] ?? '');
            $aantalKinderen = trim($_POST['aantal_kinderen'] ?? '');
            $aantalBabys = trim($_POST['aantal_babys'] ?? '');
            $geenVarkensvlees = isset($_POST['geen_varkensvlees']) ? 1 : 0;
            $veganistisch = isset($_POST['veganistisch']) ? 1 : 0;
            $vegetarisch = isset($_POST['vegetarisch']) ? 1 : 0;

            // Controleer verplichte velden
            if (
                empty($voornaam) || empty($achternaam) || empty($adres) ||
                empty($telefoon) || empty($email) ||
                $aantalVolwassenen === '' || $aantalKinderen === '' || $aantalBabys === ''
            ) {
                $data = [
                    'error' => 'Vul alle verplichte velden in om de klant toe te voegen.',
                    'form' => $_POST
                ];
                $this->view('klanten/add', $data);
                return;
            }

            // Probeer klant toe te voegen via model
            $result = $this->klantenModel->addKlant([
                'voornaam' => $voornaam,
                'achternaam' => $achternaam,
                'adres' => $adres,
                'telefoon' => $telefoon,
                'email' => $email,
                'aantal_volwassenen' => $aantalVolwassenen,
                'aantal_kinderen' => $aantalKinderen,
                'aantal_babys' => $aantalBabys,
                'geen_varkensvlees' => $geenVarkensvlees,
                'veganistisch' => $veganistisch,
                'vegetarisch' => $vegetarisch
            ]);

            if ($result) {
                // Succesvol toegevoegd
                $data = [
                    'success' => 'Klant succesvol toegevoegd.'
                ];
                // Toon opnieuw de klantenlijst met succesmelding
                $klanten = $this->klantenModel->getKlanten();
                $data['title'] = 'Overzicht Klanten';
                $data['klanten'] = $klanten;
                $this->view('klanten/index', $data);
            } else {
                $data = [
                    'error' => 'Er is iets misgegaan bij het toevoegen van de klant.',
                    'form' => $_POST
                ];
                $this->view('klanten/add', $data);
            }
        } else {
            // Toon het formulier
            $this->view('klanten/add');
        }
    }
}
