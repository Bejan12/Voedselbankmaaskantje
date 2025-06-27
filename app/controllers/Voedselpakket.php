<?php
class Voedselpakket extends BaseController
{
    private $model;
    public function __construct()
    {
        $this->model = $this->model('VoedselpakketModel');
    }
    public function index()
    {
        $filter = isset($_GET['filter']) ? $_GET['filter'] : null;
        $pakketten = $this->model->getAll($filter);
        $melding = '';
        if ($filter && empty($pakketten)) {
            $melding = 'Geen voedselpakketten gevonden';
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
}
