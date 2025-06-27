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
            // Ophalen van sorteeroptie uit query string
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'eerstvolgende';

            // Haal alle leveranciers op met sorteeroptie
            $leveranciers = $this->leverancierModel->getAllLeveranciers($sort);

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
                'leveranciers' => $leveranciers,
                'sort' => $sort
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

    public function nieuw()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form submission
            $this->add();
        } else {
            // Show form
            $data = [
                'title' => 'Nieuwe Leverancier Toevoegen',
                'bedrijfsnaam' => '',
                'adres' => '',
                'contactnaam' => '',
                'contactemail' => '',
                'contacttelefoon' => '',
                'eerstvolgendelevering' => '',
                'bedrijfsnaam_err' => '',
                'adres_err' => '',
                'contactnaam_err' => '',
                'contactemail_err' => '',
                'contacttelefoon_err' => '',
                'eerstvolgendelevering_err' => ''
            ];

            $this->view('leveranciers/add', $data);
        }
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'title' => 'Nieuwe Leverancier Toevoegen',
                'bedrijfsnaam' => trim($_POST['bedrijfsnaam']),
                'adres' => trim($_POST['adres']),
                'contactnaam' => trim($_POST['contactnaam']),
                'contactemail' => trim($_POST['contactemail']),
                'contacttelefoon' => trim($_POST['contacttelefoon']),
                'eerstvolgendelevering' => trim($_POST['eerstvolgendelevering']),
                'bedrijfsnaam_err' => '',
                'adres_err' => '',
                'contactnaam_err' => '',
                'contactemail_err' => '',
                'contacttelefoon_err' => '',
                'eerstvolgendelevering_err' => ''
            ];

            // Validate data
            if (empty($data['bedrijfsnaam'])) {
                $data['bedrijfsnaam_err'] = 'Bedrijfsnaam is verplicht';
            }

            if (empty($data['adres'])) {
                $data['adres_err'] = 'Adres is verplicht';
            }

            if (empty($data['contactnaam'])) {
                $data['contactnaam_err'] = 'Contact naam is verplicht';
            }

            if (empty($data['contactemail'])) {
                $data['contactemail_err'] = 'Contact e-mail is verplicht';
            } elseif (!filter_var($data['contactemail'], FILTER_VALIDATE_EMAIL)) {
                $data['contactemail_err'] = 'Ongeldig e-mailadres';
            }

            if (empty($data['contacttelefoon'])) {
                $data['contacttelefoon_err'] = 'Contact telefoonnummer is verplicht';
            }

            if (empty($data['eerstvolgendelevering'])) {
                $data['eerstvolgendelevering_err'] = 'Eerstvolgende levering is verplicht';
            }

            // Make sure errors are empty
            if (empty($data['bedrijfsnaam_err']) && empty($data['adres_err']) && 
                empty($data['contactnaam_err']) && empty($data['contactemail_err']) && 
                empty($data['contacttelefoon_err']) && empty($data['eerstvolgendelevering_err'])) {
                
                try {
                    // Add leverancier
                    if ($this->leverancierModel->addLeverancier($data)) {
                        // Success - show success message in view, no redirect!
                        $data = [
                            'title' => 'Leverancier toevoegen',
                            'success' => true
                        ];
                        $this->view('leveranciers/add', $data);
                        return;
                    } else {
                        throw new Exception('Database error occurred');
                    }
                } catch (Exception $e) {
                    // Database error
                    $data['general_err'] = 'Er is een fout opgetreden bij het toevoegen van de leverancier';
                    $this->view('leveranciers/add', $data);
                }
            } else {
                // Load view with errors
                $this->view('leveranciers/add', $data);
            }
        } else {
            header('Location: ' . URLROOT . '/leveranciers/nieuw');
            exit();
        }
    }
}
