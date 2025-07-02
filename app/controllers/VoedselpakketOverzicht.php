<?php
/**
 * Controller voor het overzicht van voedselpakketten
 * Voldoet aan PSR-12
 */



/**
 * Controller voor het overzicht van voedselpakketten
 * Voldoet aan PSR-12, gebruikt try-catch, server-side validatie en terugkoppeling.
 */
require_once APPROOT . '/models/VoedselpakketOverzichtModel.php';

class VoedselpakketOverzicht extends BaseController
{
    /**
     * Toon het overzicht van voedselpakketten
     * @return void
     */
    public function index(): void
    {
        try {   
            $model = new VoedselpakketOverzichtModel();
            $beschikbaar = isset($_GET['beschikbaar']) ? (int)$_GET['beschikbaar'] : null;
            $datum = isset($_GET['datum']) ? $_GET['datum'] : null;
            $datumError = '';
            $voedselpakketten = [];

            if ($datum && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $datum)) {
                $datumError = 'datum bestaat niet';
            } else {
                $voedselpakketten = $model->getVoedselpakketten($beschikbaar, $datum);
                if ($datum && empty($voedselpakketten)) {
                    $datumError = 'datum bestaat niet';
                }
            }

            $melding = '';
            if (!$datumError) {
                if (isset($_GET['success'])) {
                    if ($_GET['success'] === 'voedselpakket_toegevoegd') {
                        $melding = 'Voedselpakket is succesvol aangemaakt';
                    } else {
                        $melding = 'Overzicht succesvol geladen';
                    }
                } elseif (isset($_GET['beschikbaar']) || isset($_GET['datum'])) {
                    $melding = 'Overzicht succesvol geladen';
                }
            }
            $data = [
                'voedselpakketten' => $voedselpakketten,
                'melding' => $melding,
                'datumError' => $datumError
            ];
            $this->view('voedselpakketoverzicht/index', $data);
        } catch (Exception $e) {
            error_log(date('[Y-m-d H:i:s]') . ' Fout in VoedselpakketOverzicht::index: ' . $e->getMessage());
            $data = [
                'voedselpakketten' => [],
                'melding' => '',
                'datumError' => 'Er is een fout opgetreden bij het laden van het overzicht.'
            ];
            $this->view('voedselpakketoverzicht/index', $data);
        }
    }

    /**
     * Toon formulier voor het toevoegen van een voedselpakket
     * @return void
     */
    public function toevoegen(): void
    {
        try {
            $model = new VoedselpakketOverzichtModel();
            
            if ($_POST) {
                // Valideer input
                $klantId = (int)($_POST['klant'] ?? 0);
                $datum = trim($_POST['datum_samenstelling'] ?? '');
                $producten = $_POST['producten'] ?? [];
                $aantallen = trim($_POST['aantallen'] ?? '');
                
                // Validatie datum
                $vandaag = date('Y-m-d');
                $maxDatum = '2027-12-31';
                
                $errors = [];
                
                if (empty($datum)) {
                    $errors[] = 'Datum is verplicht';
                } elseif ($datum < $vandaag) {
                    $errors[] = 'Datum kan niet in het verleden liggen';
                } elseif ($datum > $maxDatum) {
                    $errors[] = 'Datum kan niet later zijn dan 2027';
                }
                
                if ($klantId <= 0) {
                    $errors[] = 'Selecteer een geldige klant';
                }
                
                if (empty($producten)) {
                    $errors[] = 'Selecteer minimaal één product';
                }
                
                if (empty($aantallen)) {
                    $errors[] = 'Vul aantallen in voor de producten';
                } else {
                    // Verwerk aantallen array
                    $aantallenArray = array_map('trim', explode(',', $aantallen));
                    
                    if (count($aantallenArray) !== count($producten)) {
                        $errors[] = 'Aantal aantallen moet gelijk zijn aan aantal geselecteerde producten';
                    } else {
                        // Valideer dat alle aantallen numeriek en positief zijn
                        foreach ($aantallenArray as $aantal) {
                            if (!is_numeric($aantal) || $aantal <= 0) {
                                $errors[] = 'Alle aantallen moeten positieve getallen zijn';
                                break;
                            }
                        }
                    }
                }
                
                if (empty($errors)) {
                    // Voeg voedselpakket toe
                    $result = $model->voegVoedselpakketToe($klantId, $datum, $producten, $aantallenArray);
                    
                    if ($result) {
                        header('Location: ' . URLROOT . '/voedselpakketoverzicht?success=voedselpakket_toegevoegd');
                        exit;
                    } else {
                        $errors[] = 'Er is een fout opgetreden bij het toevoegen van het voedselpakket';
                    }
                }
                
                // Als er errors zijn, toon formulier opnieuw met errors
                if (!empty($errors)) {
                    $data = [
                        'klanten' => $model->getKlanten(),
                        'producten' => $model->getProducten(),
                        'errors' => $errors,
                        'form_data' => $_POST
                    ];
                    $this->view('voedselpakketoverzicht/toevoegen', $data);
                    return;
                }
            }
            
            // Haal data op voor formulier
            $data = [
                'klanten' => $model->getKlanten(),
                'producten' => $model->getProducten()
            ];
            
            $this->view('voedselpakketoverzicht/toevoegen', $data);
        } catch (Exception $e) {
            error_log(date('[Y-m-d H:i:s]') . ' Fout in VoedselpakketOverzicht::toevoegen: ' . $e->getMessage());
            header('Location: ' . URLROOT . '/voedselpakketoverzicht?error=1');
            exit;
        }
    }
}