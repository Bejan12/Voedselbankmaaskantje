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
                    'error' => 'Vul alle verplichte velden in om de klant toe te voegen.'
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
                header('Location: ' . URLROOT . 'klanten');
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
                $aantalVolwassenen === '' || $aantalKinderen === '' || $aantalBabys === '' ||
                !filter_var($email, FILTER_VALIDATE_EMAIL)
            ) {
                $data = [
                    'form' => $_POST,
                    'error' => 'Vul alle verplichte velden in om de klant toe te voegen.'
                ];
                $this->view('klanten/edit', $data);
                return;
            }

            $updateData = [
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
            ];

            $result = $this->klantenModel->updateKlant($klantId, $updateData);

            if ($result) {
                $_SESSION['melding'] = "Klantgegevens succesvol bijgewerkt.";
                header('Location: ' . URLROOT . 'klanten');
                exit;
            } else {
                $data = [
                    'form' => $_POST,
                    'error' => 'Wijzigen mislukt. Klant bestaat mogelijk niet.'
                ];
                $this->view('klanten/edit', $data);
                return;
            }
        } else {
            $klant = $this->klantenModel->getKlantById($klantId);
            if (!$klant) {
                $_SESSION['foutmelding'] = "Klant niet gevonden.";
                header('Location: ' . URLROOT . 'klanten');
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
            // Toon altijd de melding "Klant succesvol verwijderd." als het gelukt is
            $_SESSION['melding'] = 'Klant succesvol verwijderd.';
        } else {
            // Specifieke foutmelding tonen als er actieve reserveringen/openstaande verplichtingen zijn
            if ($result['message'] === 'Klant kan niet worden verwijderd vanwege een probleem in het systeem') {
                $_SESSION['foutmelding'] = 'Klant kan niet worden verwijderd vanwege een probleem in het systeem';
            } else {
                $_SESSION['foutmelding'] = $result['message'];
            }
        }
        header('Location: ' . URLROOT . 'klanten');
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

        // Als er geen pakketten zijn, geef melding en redirect na 3 seconden
        if (empty($pakketten)) {
            $data = [
                'melding' => "Deze klant heeft nog geen voedselpakketten ontvangen.",
                'pakketten' => [],
                'klantId' => $klantId
            ];
            $this->view('klanten/pakketten', $data);
            return;
        }

        // Toon overzicht van alle afgenomen pakketten
        $data = [
            'pakketten' => $pakketten,
            'melding' => null,
            'klantId' => $klantId
        ];
        $this->view('klanten/pakketten', $data);
    }
}

// EOF





