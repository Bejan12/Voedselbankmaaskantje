<?php

class Leveranciers extends BaseController
{
    private $leverancierModel;

    public function __construct()
    {
        // Start output buffering and session early
        if (!ob_get_level()) {
            ob_start();
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
                    'redirect_url' => URLROOT . 'homepages/index'
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
                'redirect_url' => URLROOT . 'homepages/index'
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
            try {
                $leverancierTypes = $this->leverancierModel->getAllLeverancierTypes();
                
                $data = [
                    'title' => 'Nieuwe Leverancier Toevoegen',
                    'leverancier_types' => $leverancierTypes,
                    'leverancier_type_id' => '',
                    'bedrijfsnaam' => '',
                    'adres' => '',
                    'contactnaam' => '',
                    'contactemail' => '',
                    'contacttelefoon' => '',
                    'eerstvolgendelevering' => '',
                    'leverancier_type_id_err' => '',
                    'bedrijfsnaam_err' => '',
                    'adres_err' => '',
                    'contactnaam_err' => '',
                    'contactemail_err' => '',
                    'contacttelefoon_err' => '',
                    'eerstvolgendelevering_err' => ''
                ];

                $this->view('leveranciers/add', $data);
            } catch (Exception $e) {
                header('Location: ' . URLROOT . 'leveranciers');
                exit();
            }
        }
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            try {
                $leverancierTypes = $this->leverancierModel->getAllLeverancierTypes();
                
                $data = [
                    'title' => 'Nieuwe Leverancier Toevoegen',
                    'leverancier_types' => $leverancierTypes,
                    'leverancier_type_id' => trim($_POST['leverancier_type_id']),
                    'bedrijfsnaam' => trim($_POST['bedrijfsnaam']),
                    'adres' => trim($_POST['adres']),
                    'contactnaam' => trim($_POST['contactnaam']),
                    'contactemail' => trim($_POST['contactemail']),
                    'contacttelefoon' => trim($_POST['contacttelefoon']),
                    'eerstvolgendelevering' => trim($_POST['eerstvolgendelevering']),
                    'leverancier_type_id_err' => '',
                    'bedrijfsnaam_err' => '',
                    'adres_err' => '',
                    'contactnaam_err' => '',
                    'contactemail_err' => '',
                    'contacttelefoon_err' => '',
                    'eerstvolgendelevering_err' => '',
                    'general_err' => ''
                ];

                // Validate data
                if (empty($data['leverancier_type_id'])) {
                    $data['leverancier_type_id_err'] = 'Leverancier type is verplicht';
                }

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
                } elseif ($this->leverancierModel->emailExists($data['contactemail'])) {
                    $data['general_err'] = 'E-mail adres is al in gebruik, probeer het opnieuw.';
                }

                if (empty($data['contacttelefoon'])) {
                    $data['contacttelefoon_err'] = 'Contact telefoonnummer is verplicht';
                }

                if (empty($data['eerstvolgendelevering'])) {
                    $data['eerstvolgendelevering_err'] = 'Eerstvolgende levering is verplicht';
                } else {
                    // Valideer datum
                    $leveringDatum = DateTime::createFromFormat('Y-m-d\TH:i', $data['eerstvolgendelevering']);
                    $vandaag = new DateTime();
                    $maxDatum = new DateTime('2026-12-31');
                    
                    if (!$leveringDatum) {
                        $data['eerstvolgendelevering_err'] = 'Ongeldige datum/tijd format';
                    } elseif ($leveringDatum < $vandaag) {
                        $data['eerstvolgendelevering_err'] = 'De leveringsdatum kan niet in het verleden liggen';
                    } elseif ($leveringDatum > $maxDatum) {
                        $data['eerstvolgendelevering_err'] = 'De leveringsdatum kan niet verder dan eind 2026 liggen';
                    }
                }

                // Make sure errors are empty
                if (empty($data['leverancier_type_id_err']) && empty($data['bedrijfsnaam_err']) && empty($data['adres_err']) &&
                    empty($data['contactnaam_err']) && empty($data['contactemail_err']) &&
                    empty($data['contacttelefoon_err']) && empty($data['eerstvolgendelevering_err']) &&
                    empty($data['general_err'])) {

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
                        $data['general_err'] = 'Er is een onverwachte fout opgetreden bij het toevoegen van de leverancier. Probeer het opnieuw.';
                        $this->view('leveranciers/add', $data);
                    }
                } else {
                    // Load view with errors
                    $this->view('leveranciers/add', $data);
                }
            } catch (Exception $e) {
                header('Location: ' . URLROOT . 'leveranciers');
                exit();
            }
        } else {
            header('Location: ' . URLROOT . 'leveranciers/nieuw');
            exit();
        }
    }

    public function edit($id = null)
    {
        if (!is_numeric($id)) {
            if (ob_get_level()) ob_end_clean();
            header('Location: ' . URLROOT . 'leveranciers');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->update($id);
        } else {
            try {
                $leverancier = $this->leverancierModel->getLeverancierById($id);
                
                if (!$leverancier) {
                    if (ob_get_level()) ob_end_clean();
                    header('Location: ' . URLROOT . 'leveranciers');
                    exit();
                }

                // Convert MySQL datetime to datetime-local format
                $leveringDatum = '';
                if ($leverancier->EerstvolgendeLevering) {
                    $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $leverancier->EerstvolgendeLevering);
                    if ($datetime) {
                        $leveringDatum = $datetime->format('Y-m-d\TH:i');
                    }
                }

                $data = [
                    'title' => 'Leverancier Wijzigen',
                    'leverancier_id' => $id,
                    'bedrijfsnaam' => $leverancier->Bedrijfsnaam,
                    'adres' => $leverancier->Adres,
                    'contactnaam' => $leverancier->ContactNaam,
                    'contactemail' => $leverancier->ContactEmail,
                    'contacttelefoon' => $leverancier->ContactTelefoon,
                    'eerstvolgendelevering' => $leveringDatum,
                    'status' => isset($leverancier->Status) ? $leverancier->Status : 'actief',
                    'bedrijfsnaam_err' => '',
                    'adres_err' => '',
                    'contactnaam_err' => '',
                    'contactemail_err' => '',
                    'contacttelefoon_err' => '',
                    'eerstvolgendelevering_err' => '',
                    'status_err' => '',
                    'general_err' => ''
                ];

                $this->view('leveranciers/edit', $data);
            } catch (Exception $e) {
                if (ob_get_level()) ob_end_clean();
                header('Location: ' . URLROOT . 'leveranciers');
                exit();
            }
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'title' => 'Leverancier Wijzigen',
                'leverancier_id' => $id,
                'bedrijfsnaam' => isset($_POST['bedrijfsnaam']) ? trim($_POST['bedrijfsnaam']) : '',
                'adres' => isset($_POST['adres']) ? trim($_POST['adres']) : '',
                'contactnaam' => isset($_POST['contactnaam']) ? trim($_POST['contactnaam']) : '',
                'contactemail' => isset($_POST['contactemail']) ? trim($_POST['contactemail']) : '',
                'contacttelefoon' => isset($_POST['contacttelefoon']) ? trim($_POST['contacttelefoon']) : '',
                'eerstvolgendelevering' => isset($_POST['eerstvolgendelevering']) ? trim($_POST['eerstvolgendelevering']) : '',
                'status' => isset($_POST['status']) ? trim($_POST['status']) : 'actief',
                'bedrijfsnaam_err' => '',
                'adres_err' => '',
                'contactnaam_err' => '',
                'contactemail_err' => '',
                'contacttelefoon_err' => '',
                'eerstvolgendelevering_err' => '',
                'status_err' => '',
                'general_err' => ''
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
            } elseif ($this->leverancierModel->emailExistsForOtherLeverancier($data['contactemail'], $id)) {
                $data['general_err'] = 'E-mail adres is al in gebruik bij een andere leverancier.';
            }

            if (empty($data['contacttelefoon'])) {
                $data['contacttelefoon_err'] = 'Contact telefoonnummer is verplicht';
            }

            if (empty($data['eerstvolgendelevering'])) {
                $data['eerstvolgendelevering_err'] = 'Eerstvolgende levering is verplicht';
            } else {
                $leveringDatum = DateTime::createFromFormat('Y-m-d\TH:i', $data['eerstvolgendelevering']);
                $vandaag = new DateTime();
                $maxDatum = new DateTime('2026-12-31');
                
                if (!$leveringDatum) {
                    $data['eerstvolgendelevering_err'] = 'Ongeldige datum/tijd format';
                } elseif ($leveringDatum < $vandaag) {
                    $data['eerstvolgendelevering_err'] = 'De leveringsdatum kan niet in het verleden liggen';
                } elseif ($leveringDatum > $maxDatum) {
                    $data['eerstvolgendelevering_err'] = 'De leveringsdatum kan niet verder dan eind 2026 liggen';
                }
            }

            if (empty($data['status'])) {
                $data['status_err'] = 'Status is verplicht';
            } elseif (!in_array($data['status'], ['actief', 'inactief', 'onderweg'])) {
                $data['status_err'] = 'Ongeldige status waarde';
            }

            // Make sure errors are empty
            if (empty($data['bedrijfsnaam_err']) && empty($data['adres_err']) &&
                empty($data['contactnaam_err']) && empty($data['contactemail_err']) &&
                empty($data['contacttelefoon_err']) && empty($data['eerstvolgendelevering_err']) &&
                empty($data['status_err']) && empty($data['general_err'])) {

                try {
                    if ($this->leverancierModel->updateLeverancier($id, $data)) {
                        $data = [
                            'title' => 'Leverancier wijzigen',
                            'leverancier_id' => $id,
                            'success' => true
                        ];
                        $this->view('leveranciers/edit', $data);
                        return;
                    } else {
                        throw new Exception('Database error occurred');
                    }
                } catch (Exception $e) {
                    $data['general_err'] = 'Er is een onverwachte fout opgetreden bij het wijzigen van de leverancier. Probeer het opnieuw.';
                    $this->view('leveranciers/edit', $data);
                }
            } else {
                $this->view('leveranciers/edit', $data);
            }
        }
    }

    public function delete($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && is_numeric($id)) {
            try {
                // Check if leverancier exists and get its status
                $leverancier = $this->leverancierModel->getLeverancierById($id);
                
                if (!$leverancier) {
                    SessionHelper::setFlash('leverancier_message', 'Leverancier niet gevonden.', 'danger');
                    if (ob_get_level()) ob_end_clean();
                    header('Location: ' . URLROOT . 'leveranciers');
                    exit();
                }

                // Check if leverancier can be deleted
                if (!$this->leverancierModel->canDeleteLeverancier($id)) {
                    SessionHelper::setFlash('leverancier_message', 'Leverancier kan niet verwijderd worden omdat deze al onderweg is!', 'danger');
                    if (ob_get_level()) ob_end_clean();
                    header('Location: ' . URLROOT . 'leveranciers');
                    exit();
                }

                if ($this->leverancierModel->deleteLeverancierById($id)) {
                    SessionHelper::setFlash('leverancier_message', 'Leverancier succesvol verwijderd.', 'success');
                } else {
                    SessionHelper::setFlash('leverancier_message', 'Fout bij verwijderen leverancier.', 'danger');
                }
            } catch (Exception $e) {
                SessionHelper::setFlash('leverancier_message', 'Fout bij verwijderen leverancier.', 'danger');
            }
        }
        if (ob_get_level()) ob_end_clean();
        header('Location: ' . URLROOT . 'leveranciers');
        exit();
    }
}
