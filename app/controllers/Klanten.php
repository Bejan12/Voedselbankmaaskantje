<?php
// Het is niet nodig om BaseController hier te includen, dit gebeurt al in require.php
// require_once '../bibliotheken/Controller.php'; // Verwijderd

class Klanten extends BaseController
{
    private $klantenModel;

    public function __construct()
    {
        // Start de sessie om meldingen te kunnen gebruiken
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->klantenModel = $this->model('klantenModels');
    }

    public function index()
    {
        $klanten = $this->klantenModel->getKlanten();
        
        $data = [
            'title' => 'Overzicht Klanten',
            'klanten' => $klanten
        ];

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
                SessionHelper::setFlash('melding', "Klant succesvol toegevoegd.");
                header('Location: ' . URLROOT . 'klanten');
                exit;
            } else {
                $data = [
                    'form' => $_POST,
                    'error' => 'Toevoegen mislukt. De gebruikersnaam (voornaam.achternaam) bestaat mogelijk al.'
                ];
                $this->view('klanten/add', $data);
            }
        } else {
            $this->view('klanten/add');
        }
    }

    private function validateKlantData($data)
    {
        if (
            empty($data['voornaam']) || empty($data['achternaam']) || empty($data['adres']) ||
            empty($data['telefoon']) || empty($data['email']) ||
            $data['aantal_volwassenen'] === '' || $data['aantal_kinderen'] === '' || $data['aantal_babys'] === ''
        ) {
            return 'Vul alle verplichte velden in.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Voer een geldig e-mailadres in.';
        }
        return '';
    }

    public function edit($klantId)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $updateData = [
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

            $errors = [];
            // Algemene validatie voor lege velden
            if (empty($updateData['voornaam']) || empty($updateData['achternaam']) || empty($updateData['adres']) || empty($updateData['telefoon']) || empty($updateData['email']) || $updateData['aantal_volwassenen'] === '' || $updateData['aantal_kinderen'] === '' || $updateData['aantal_babys'] === '') {
                $data = [
                    'klantId' => $klantId,
                    'form' => $updateData,
                    'errors' => [], // Leegmaken om specifieke veld-errors te vermijden
                    'error' => 'vul alle verplichte velden in om de klant toe te voegen'
                ];
                $this->view('klanten/edit', $data);
                return;
            }

            if (empty($updateData['voornaam'])) {
                $errors['voornaam'] = 'Voornaam is verplicht.';
            }
            if (empty($updateData['achternaam'])) {
                $errors['achternaam'] = 'Achternaam is verplicht.';
            }
            // Check voor bestaande gebruikersnaam
            if ($this->klantenModel->checkBestaandeGebruikersnaam($updateData['voornaam'], $updateData['achternaam'], $klantId)) {
                $errors['voornaam'] = 'De combinatie van voor- en achternaam is al in gebruik.';
                $errors['achternaam'] = 'De combinatie van voor- en achternaam is al in gebruik.';
            }
            if (empty($updateData['adres'])) {
                $errors['adres'] = 'Adres is verplicht.';
            }
            if (empty($updateData['telefoon'])) {
                $errors['telefoon'] = 'Telefoonnummer is verplicht.';
            }
            if (empty($updateData['email'])) {
                $errors['email'] = 'E-mailadres is verplicht.';
            } elseif (!filter_var($updateData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Voer een geldig e-mailadres in.';
            } elseif ($this->klantenModel->checkBestaandeEmail($updateData['email'], $klantId)) {
                $errors['email'] = 'Dit e-mailadres is al in gebruik.';
            }
            if ($updateData['aantal_volwassenen'] === '' || !is_numeric($updateData['aantal_volwassenen'])) {
                $errors['aantal_volwassenen'] = 'Aantal volwassenen is verplicht.';
            }
            if ($updateData['aantal_kinderen'] === '' || !is_numeric($updateData['aantal_kinderen'])) {
                $errors['aantal_kinderen'] = 'Aantal kinderen is verplicht.';
            }
            if ($updateData['aantal_babys'] === '' || !is_numeric($updateData['aantal_babys'])) {
                $errors['aantal_babys'] = 'Aantal baby\'s is verplicht.';
            }

            if (!empty($errors)) {
                $data = [
                    'klantId' => $klantId,
                    'form' => $updateData,
                    'errors' => $errors
                ];
                $this->view('klanten/edit', $data);
                return;
            }

            $result = $this->klantenModel->updateKlant($klantId, $updateData);

            if ($result) {
                SessionHelper::setFlash('melding', "Klantgegevens succesvol bijgewerkt.");
                header('Location: ' . URLROOT . 'klanten');
                exit;
            } else {
                $data = [
                    'klantId' => $klantId,
                    'form' => $updateData,
                    'errors' => $errors, // Stuur de bestaande errors mee
                    'error' => 'Wijzigen mislukt. Controleer de ingevulde velden.'
                ];
                $this->view('klanten/edit', $data);
                return;
            }
        } else {
            $klant = $this->klantenModel->getKlantById($klantId);
            if (!$klant) {
                SessionHelper::setFlash('foutmelding', "Klant niet gevonden.", 'danger');
                header('Location: ' . URLROOT . 'klanten');
                exit;
            }
            $data = [
                'klantId' => $klantId, // Voeg klantId toe
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
            SessionHelper::setFlash('melding', $result['message']);
        } else {
            SessionHelper::setFlash('foutmelding', $result['message'], 'danger');
        }
        header('Location: ' . URLROOT . 'klanten');
        exit;
    }

    public function afgerondePakketten()
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






