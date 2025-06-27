<?php
/**
 * Controller voor Voedselpakketten
 * Bevat methodes voor het tonen en toevoegen van pakketten, met validatie, logging en meldingen.
 * @author Zakaria
 */
class Voedselpakket extends BaseController
{
    /**
     * @var VoedselpakketModel
     */
    private $model;

    public function __construct()
    {
        $this->model = $this->model('VoedselpakketModel');
    }

    /**
     * Toon het overzicht van voedselpakketten met filter en meldingen
     * @return void
     */
    public function index()
    {
        // Filter ophalen uit GET
        $filter = isset($_GET['filter']) ? $_GET['filter'] : null;
        // Ophalen van pakketten
        $pakketten = $this->model->getAll($filter);
        $melding = '';
        if ($filter && empty($pakketten)) {
            $melding = 'Geen voedselpakketten gevonden';
            // Technische log
            error_log(date('[Y-m-d H:i:s]') . ' Geen voedselpakketten gevonden voor filter: ' . $filter);
            // Terugsturen na 2 seconden
            header('Refresh:2; url=' . URLROOT . '/voedselpakket/index');
        } elseif ($filter) {
            $melding = 'Overzicht succesvol geladen';
        }
        $data = [
            'title' => 'Overzicht Voedselpakketten',
            'pakketten' => $pakketten,
            'melding' => $melding,
            'filter' => $filter
        ];
        $this->view('voedselpakket/index', $data);
    }

    /**
     * Voeg een nieuw voedselpakket toe met validatie en logging
     * @return void
     */
    public function toevoegen()
    {
        $melding = '';
        $success = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Server-side validatie: klantId en datum
            $klantId = isset($_POST['klantId']) ? (int)$_POST['klantId'] : 0;
            $datum = isset($_POST['datum']) ? $_POST['datum'] : '';
            if ($klantId > 0 && preg_match('/^\d{4}-\d{2}-\d{2}$/', $datum)) {
                $result = $this->model->addPakket($klantId, $datum);
                $melding = $result['message'];
                $success = $result['success'];
                if ($success) {
                    // Na 2 seconden terug naar overzicht
                    header('Refresh:2; url=' . URLROOT . '/voedselpakket/index');
                }
            } else {
                $melding = 'Ongeldige invoer. Controleer klant en datum.';
                error_log(date('[Y-m-d H:i:s]') . ' Ongeldige invoer bij toevoegen voedselpakket.');
            }
        }
        // Ophalen van alle klanten met allergieën voor de dropdown
        $klanten = $this->model->getAllKlantenMetAllergieen();
        $data = [
            'title' => 'Voedselpakket toevoegen',
            'melding' => $melding,
            'success' => $success,
            'klanten' => $klanten
        ];
        $this->view('voedselpakket/toevoegen', $data);
    }
}
