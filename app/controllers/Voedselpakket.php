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
        $filter = isset($_GET['filter']) ? $_GET['filter'] : null;
        $pakketten = $this->model->getAll($filter);
        $melding = '';
        if ($filter && empty($pakketten)) {
            $melding = 'Geen voedselpakketten gevonden';
            error_log(date('[Y-m-d H:i:s]') . ' Geen voedselpakketten gevonden voor filter: ' . $filter);
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
        $aantalProducten = 0;
        $status = 'In voorbereiding';
        $opmerking = null;
        $productenPerCategorie = [];
        $categorieNamen = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $klantId = isset($_POST['klantId']) ? (int)$_POST['klantId'] : 0;
            $datum = isset($_POST['datum']) ? $_POST['datum'] : '';
            $pakketCategorieId = isset($_POST['pakketCategorieId']) ? (int)$_POST['pakketCategorieId'] : 0;
            $opmerking = isset($_POST['opmerking']) ? trim($_POST['opmerking']) : null;
            $samenstel = isset($_POST['samenstel']) ? $_POST['samenstel'] : [];
            // Validatie: datum mag niet in het verleden
            if (empty($klantId) || empty($datum) || ($pakketCategorieId === 0 && empty($samenstel))) {
                $melding = 'Ongeldige invoer. Controleer klant, datum en pakketkeuze.';
                error_log(date('[Y-m-d H:i:s]') . ' Ongeldige invoer bij toevoegen voedselpakket.');
            } elseif (strtotime($datum) < strtotime(date('Y-m-d'))) {
                $melding = 'Je kunt geen datum in het verleden kiezen.';
                error_log(date('[Y-m-d H:i:s]') . ' Poging tot invoer van datum in het verleden: ' . $datum);
            } else {
                // Bepaal aantal producten
                if (!empty($samenstel)) {
                    foreach($samenstel as $cat) {
                        $aantalProducten += count($cat);
                    }
                } else {
                    $aantalProducten = 5; // Standaard aantal voor standaardpakket, evt. aanpassen
                }
                // Voeg toe via model
                $result = $this->model->addPakket($klantId, $datum, $pakketCategorieId, $aantalProducten, $status, $opmerking);
                $melding = $result['message'];
                $success = $result['success'];
                if ($success) {
                    // Happy flow: pakket succesvol toegevoegd
                    $melding = 'Voedselpakket is succesvol aangemaakt.';
                    error_log(date('[Y-m-d H:i:s]') . ' Voedselpakket succesvol toegevoegd voor klant ' . $klantId);
                    // Direct overzicht updaten
                    header('Refresh:2; url=' . URLROOT . '/voedselpakket/index');
                } else {
                    // Unhappy flow: niet gelukt
                    if (strpos($melding, 'bestaat al') !== false) {
                        $melding = 'Dit voedselpakket bestaat al.';
                    } elseif (strpos($melding, 'datum') !== false) {
                        $melding = 'Je kunt geen datum in het verleden kiezen.';
                    } else {
                        $melding = 'Pakket kan niet worden toegevoegd vanwege een technische fout.';
                    }
                    error_log(date('[Y-m-d H:i:s]') . ' Fout bij toevoegen voedselpakket voor klant ' . $klantId . ': ' . $melding);
                }
            }
        }
        // Ophalen van alle klanten met allergieën en categorieën voor de dropdown
        $klanten = $this->model->getAllKlantenMetAllergieen();
        $categorieen = $this->model->getAllCategorieen();
        // Ophalen van producten per categorie voor samenstellen (optioneel, alleen als klant allergie heeft)
        $productenPerCategorie = [];
        $categorieNamen = [];
        if (!empty($categorieen)) {
            foreach($categorieen as $cat) {
                $categorieNamen[$cat->CategorieID] = $cat->Naam;
                if (method_exists($this->model, 'getProductenPerCategorie')) {
                    $productenPerCategorie[$cat->CategorieID] = $this->model->getProductenPerCategorie($cat->CategorieID);
                } else {
                    $productenPerCategorie[$cat->CategorieID] = [];
                }
            }
        }
        $data = [
            'title' => 'Voedselpakket toevoegen',
            'melding' => $melding,
            'success' => $success,
            'klanten' => $klanten,
            'categorieen' => $categorieen,
            'productenPerCategorie' => $productenPerCategorie,
            'categorieNamen' => $categorieNamen
        ];
        $this->view('voedselpakket/toevoegen', $data);
    }
}
