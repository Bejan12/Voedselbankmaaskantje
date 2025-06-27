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

            if (
                empty($voornaam) || empty($achternaam) || empty($adres) ||
                empty($telefoon) || empty($email) ||
                $aantalVolwassenen === '' || $aantalKinderen === '' || $aantalBabys === ''
            ) {
                $data = [
                    'form' => $_POST,
                    'error' => 'Vul alle verplichte velden in.'
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

            if ($result) {
                $_SESSION['melding'] = "Klant succesvol toegevoegd.";
                header('Location: /klanten');
                exit;
            } else {
                $data = [
                    'form' => $_POST,
                    'error' => 'Toevoegen mislukt. Mogelijk bestaat deze klant al.'
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

    public function afgerondepakketten()
    {
        // Haal alle klanten op
        $klanten = $this->klantenModel->getKlanten();
        $data = [
            'title' => 'Afgeronde voedselpakketten per klant',
            'klanten' => $klanten
        ];
        $this->view('klanten/afgerondepakketten', $data);
    }

    public function pakketten($klantId)
    {
        // Haal pakketten op voor deze klant
        $pakketten = $this->klantenModel->getAfgerondeVoedselpakkettenByKlant($klantId);

        if (empty($pakketten)) {
            $data = [
                'melding' => "Deze klant heeft nog geen voedselpakketten ontvangen.",
                'pakketten' => [],
                'klantId' => $klantId
            ];
            $this->view('klanten/pakketten', $data);
            return;
        }

        $data = [
            'pakketten' => $pakketten,
            'melding' => null,
            'klantId' => $klantId
        ];
        $this->view('klanten/pakketten', $data);
    }
}

// EOF





