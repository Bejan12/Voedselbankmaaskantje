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

class Voedselpakketoverzicht extends BaseController
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

            $melding = (!$datumError && (isset($_GET['success']) || isset($_GET['beschikbaar']) || isset($_GET['datum']))) ? 'Overzicht succesvol geladen' : '';
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
}