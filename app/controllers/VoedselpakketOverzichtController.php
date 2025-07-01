<?php
/**
 * Controller voor het overzicht van voedselpakketten
 * Voldoet aan PSR-12, gebruikt try-catch, server-side validatie en terugkoppeling.
 */
class VoedselpakketOverzichtController extends BaseController
{
    /**
     * Toon het overzicht van voedselpakketten
     * @return void
     */
    public function index(): void
    {
        try {
            $model = $this->model('VoedselpakketOverzichtModel');
            $beschikbaar = isset($_GET['beschikbaar']) ? (int)$_GET['beschikbaar'] : null;
            $voedselpakketten = $model->getVoedselpakketten($beschikbaar);
            $melding = isset($_GET['success']) ? 'Overzicht succesvol geladen' : '';
            $data = [
                'voedselpakketten' => $voedselpakketten,
                'melding' => $melding
            ];
            $this->view('voedselpakketoverzicht/index', $data);
        } catch (Exception $e) {
            error_log(date('[Y-m-d H:i:s]') . ' Fout in VoedselpakketOverzichtController::index: ' . $e->getMessage());
            $data = [
                'voedselpakketten' => [],
                'melding' => 'Er is een fout opgetreden bij het laden van het overzicht.'
            ];
            $this->view('voedselpakketoverzicht/index', $data);
        }
    }
}
