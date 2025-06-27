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
            'klanten' => $klanten,
            'melding' => $_SESSION['melding'] ?? null,
            'foutmelding' => $_SESSION['foutmelding'] ?? null
        ];
        unset($_SESSION['melding'], $_SESSION['foutmelding']);
        $this->view('klanten/index', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

            // Geen foutmelding tonen, alleen formulier opnieuw tonen bij fout
            if (
                empty($voornaam) || empty($achternaam) || empty($adres) ||
                empty($telefoon) || empty($email) ||
                $aantalVolwassenen === '' || $aantalKinderen === '' || $aantalBabys === ''
            ) {
                $data = [
                    'form' => $_POST
                ];
                $this->view('klanten/add', $data);
                return;
            }

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

            // Geen foutmelding tonen, alleen terug naar overzicht of opnieuw formulier
            if ($result) {
                $klanten = $this->klantenModel->getKlanten();
                $data = [
                    'title' => 'Overzicht Klanten',
                    'klanten' => $klanten
                ];
                $this->view('klanten/index', $data);
            } else {
                $data = [
                    'form' => $_POST
                ];
                $this->view('klanten/add', $data);
            }
        } else {
            $this->view('klanten/add');
        }
    }

    public function edit($klantId)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verzamel en valideer POST-data
            $data = [
                'voornaam' => trim($_POST['voornaam'] ?? ''),
                'achternaam' => trim($_POST['achternaam'] ?? ''),
                'adres' => trim($_POST['adres'] ?? ''),
                'telefoon' => trim($_POST['telefoon'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'aantal_volwassenen' => trim($_POST['aantal_volwassenen'] ?? ''),
                'aantal_kinderen' => trim($_POST['aantal_kinderen'] ?? ''),
                'aantal_babys' => trim($_POST['aantal_babys'] ?? ''),
                'geen_varkensvlees' => isset($_POST['geen_varkensvlees']) ? 1 : 0,
                'veganistisch' => isset($_POST['veganistisch']) ? 1 : 0,
                'vegetarisch' => isset($_POST['vegetarisch']) ? 1 : 0
            ];

            $result = $this->klantenModel->updateKlant($klantId, $data);

            if ($result) {
                $_SESSION['melding'] = "Klant succesvol gewijzigd.";
            } else {
                $_SESSION['foutmelding'] = "Wijzigen mislukt. Klant bestaat mogelijk niet.";
            }
            header('Location: /klanten');
            exit;
        } else {
            $klant = $this->klantenModel->getKlantById($klantId);
            if (!$klant) {
                $_SESSION['foutmelding'] = "Klant niet gevonden.";
                header('Location: /klanten');
                exit;
            }
            $data = [
                'form' => [
                    'voornaam' => $klant->Voornaam,
                    'achternaam' => $klant->Achternaam,
                    'adres' => $klant->Adres,
                    'telefoon' => $klant->Telefoon,
                    'email' => $klant->Email,
                    'aantal_volwassenen' => $klant->AantalVolwassenen,
                    'aantal_kinderen' => $klant->AantalKinderen,
                    'aantal_babys' => $klant->AantalBabys,
                    'geen_varkensvlees' => $klant->GeenVarkensvlees,
                    'veganistisch' => $klant->Veganistisch,
                    'vegetarisch' => $klant->Vegetarisch
                ]
            ];
            $this->view('klanten/edit', $data);
        }
    }

    public function verwijder($klantId)
    {
        $result = $this->klantenModel->deleteKlant($klantId);

        if ($result['success']) {
            $_SESSION['melding'] = $result['message'];
        } else {
            $_SESSION['foutmelding'] = $result['message'];
        }
        header('Location: /klanten');
        exit;
    }
}

// EOF





