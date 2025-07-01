<?php require_once APPROOT . '/views/includes/header.php'; ?>
<?php
require_once APPROOT . '/libraries/Database.php';
$db = new Database();
$melding = isset($melding) ? $melding : '';
$foutmelding = isset($foutmelding) ? $foutmelding : '';
$datumError = isset($datumError) ? $datumError : '';
$modalOpen = false;
$allergieMelding = '';
$datumError = '';

// Ophalen klanten en producten voor het formulier
$db->query("SELECT k.KlantID, CONCAT(p.Voornaam, ' ', p.Achternaam) AS gezinsnaam FROM klant k JOIN gebruiker g ON k.GebruikerID = g.GebruikerID JOIN persoon p ON g.PersoonID = p.PersoonID");
$klanten = $db->resultSet();
$db->query("SELECT ProductID, ProductNaam FROM product");
$producten = $db->resultSet();

// Ophalen allergieën per klant
$klantAllergieen = [];
$db->query("SELECT k.KlantID, GROUP_CONCAT(a.Naam SEPARATOR ', ') AS allergieen FROM klantallergie ka JOIN allergie a ON ka.AllergieID = a.AllergieID JOIN klant k ON ka.KlantID = k.KlantID GROUP BY k.KlantID");
foreach ($db->resultSet() as $row) {
    $klantAllergieen[$row->KlantID] = $row->allergieen;
}

// Filter validatie en terugkoppeling
if (isset($_GET['datum']) && $_GET['datum'] !== '') {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['datum'])) {
        $datumError = 'datum bestaat niet';
    } else {
        // Check of er pakketten zijn voor deze datum
        $db->query("SELECT COUNT(*) as aantal FROM voedselpakket WHERE DatumSamenstelling = :datum");
        $db->bind(':datum', $_GET['datum']);
        $row = $db->single();
        if ($row && $row->aantal == 0) {
            $datumError = 'datum bestaat niet';
        } else {
            $melding = 'Overzicht succesvol geladen';
        }
    }
} elseif (isset($_GET['beschikbaar']) && $_GET['beschikbaar'] !== '') {
    $melding = 'Overzicht succesvol geladen';
}
if (isset($_GET['success'])) {
    $melding = 'Voedselpakket is succesvol aangemaakt';
}
?>
<style>
    .badge-yes {
        background-color: #198754;
        padding: 0.5em 1em;
        border-radius: 1rem;
        color: white;
        font-weight: 500;
    }
    .badge-no {
        background-color: #dc3545;
        padding: 0.5em 1em;
        border-radius: 1rem;
        color: white;
        font-weight: 500;
    }
    .table th {
        white-space: nowrap;
    }
    .form-section {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }
</style>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">
            <h2 class="mb-4 text-center fw-bold text-primary">Overzicht Voedselpakketten</h2>
            <!-- Meldingen -->
            <?php if (!empty($datumError)): ?>
                <div class="alert alert-danger text-center shadow-sm" id="datumErrorAlert">
                    <?= htmlspecialchars($datumError) ?>
                </div>
                <script>
                  // Redirect na tonen foutmelding
                  setTimeout(function() {
                    window.location.href = window.location.pathname;
                  }, 2000);
                </script>
            <?php endif; ?>
            <?php if (!empty($melding)): ?>
                <div class="alert alert-success text-center shadow-sm" id="meldingAlert">
                    <?= htmlspecialchars($melding) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($foutmelding)): ?>
                <div class="alert alert-danger text-center shadow-sm" id="foutmeldingAlert">
                    <?= htmlspecialchars($foutmelding) ?>
                </div>
            <?php endif; ?>
            <!-- Filterformulier -->
            <div class="form-section mb-4">
                <form method="get" class="row g-3 align-items-end">
                    <div class="col-12 col-md-5">
                        <label for="beschikbaar" class="form-label">Beschikbaarheid</label>
                        <select name="beschikbaar" id="beschikbaar" class="form-select">
                            <option value="">Alles</option>
                            <option value="1" <?= (isset($_GET['beschikbaar']) && $_GET['beschikbaar'] == '1') ? 'selected' : '' ?>>Beschikbaar</option>
                            <option value="0" <?= (isset($_GET['beschikbaar']) && $_GET['beschikbaar'] == '0') ? 'selected' : '' ?>>Niet beschikbaar</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-5">
                        <label for="datum" class="form-label">Filter op datum samenstelling</label>
                        <input type="date" name="datum" id="datum" class="form-control"
                               value="<?= isset($_GET['datum']) ? htmlspecialchars($_GET['datum']) : '' ?>">
                    </div>
                    <div class="col-12 col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
            <!-- Tabel en Toevoegen knop -->
            <div class="d-flex justify-content-end mb-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#voegPakketToeModal">Voedselpakket toevoegen</button>
            </div>
            <div class="table-responsive rounded shadow-sm">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
<thead class="table-light text-center small">
    <tr>
        <th>Pakketnr</th>
        <th>Gezin</th>
        <th>Omschrijv.</th>
        <th>Volw.</th>
        <th>Kind.</th>
        <th>Baby's</th>
        <th>Details</th>
        <th>Beschik.</th>
        <th>Datum</th>
        <th>Actie</th>
    </tr>
</thead>

                        </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php
                    // Ophalen voedselpakketten (dummy data uit database)
                    $sql = "SELECT v.VoedselpakketID, CONCAT(p.Voornaam, ' ', p.Achternaam) AS gezinsnaam, CONCAT('Pakket samengesteld op ', v.DatumSamenstelling) AS Omschrijving, k.AantalVolwassenen, k.AantalKinderen, k.AantalBabys, (
                        SELECT GROUP_CONCAT(CONCAT(pr.ProductNaam, ' (', vpp.Aantal, 'x)') SEPARATOR ', ')
                        FROM voedselpakketproduct vpp
                        JOIN product pr ON vpp.ProductID = pr.ProductID
                        WHERE vpp.VoedselpakketID = v.VoedselpakketID
                    ) AS Details, IF(v.DatumUitgifte IS NULL, 1, 0) AS Beschikbaar, v.DatumSamenstelling
                    FROM voedselpakket v
                    JOIN klant k ON v.KlantID = k.KlantID
                    JOIN gebruiker g ON k.GebruikerID = g.GebruikerID
                    JOIN persoon p ON g.PersoonID = p.PersoonID";
                    $where = [];
                    $params = [];
                    if (isset($_GET['beschikbaar']) && $_GET['beschikbaar'] !== '') {
                        $where[] = 'IF(v.DatumUitgifte IS NULL, 1, 0) = :beschikbaar';
                        $params[':beschikbaar'] = $_GET['beschikbaar'];
                    }
                    if (isset($_GET['datum']) && $_GET['datum'] !== '') {
                        $where[] = 'v.DatumSamenstelling = :datum';
                        $params[':datum'] = $_GET['datum'];
                    }
                    if (!empty($where)) {
                        $sql .= ' WHERE ' . implode(' AND ', $where);
                    }
                    $sql .= ' ORDER BY v.DatumSamenstelling DESC';
                    $db->query($sql);
                    foreach ($params as $key => $value) {
                        $db->bind($key, $value);
                    }
                    $voedselpakketten = $db->resultSet();
                    ?>
                    <?php if (!empty($voedselpakketten)): ?>
                        <?php 
                        // Sorteer pakketten op pakketnummer (nieuwste bovenaan)
                        usort($voedselpakketten, function($a, $b) { return $b->VoedselpakketID - $a->VoedselpakketID; });
                        foreach ($voedselpakketten as $pakket): ?>
                            <tr data-pakketid="<?= $pakket->VoedselpakketID ?>">
                                <td class="fw-bold text-primary">#<?= (int)$pakket->VoedselpakketID ?></td>
                                <td class="text-start ps-3"><span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;max-width:120px;"><?= htmlspecialchars($pakket->gezinsnaam) ?></span></td>
                                <td class="text-start ps-3"><span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;max-width:180px;"><?= htmlspecialchars($pakket->Omschrijving) ?></span></td>
                                <td><?= (int)$pakket->AantalVolwassenen ?></td>
                                <td><?= (int)$pakket->AantalKinderen ?></td>
                                <td><?= (int)$pakket->AantalBabys ?></td>
                                <td class="text-start ps-3"><span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;max-width:200px;"><?= $pakket->Details !== null ? htmlspecialchars($pakket->Details) : '<span class="text-muted">-</span>' ?></span></td>
                                <td>
                                  <?php if ($pakket->Beschikbaar): ?>
    <span class="badge-yes" style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; font-weight: 500;">Ja</span>
<?php else: ?>

    <span class="badge badge-no" style="font-size: 0.75rem; padding: 4px 10px; text-transform: lowercase;">
        nee
    </span>
<?php endif; ?>

                                </td>
                                <td><?= date('d-m-Y', strtotime($pakket->DatumSamenstelling)) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" title="Wijzigen" onclick="openWijzigModal('<?= $pakket->VoedselpakketID ?>', <?= $pakket->Beschikbaar ? 'true' : 'false' ?>, '<?= htmlspecialchars(addslashes($pakket->Omschrijving)) ?>', <?= (int)$pakket->AantalVolwassenen ?>, <?= (int)$pakket->AantalKinderen ?>, <?= (int)$pakket->AantalBabys ?>, '<?= date('Y-m-d', strtotime($pakket->DatumSamenstelling)) ?>')"><span class='bi bi-pencil-square'></span></button>
                                    <button class="btn btn-sm btn-outline-danger" title="Verwijderen" onclick="openVerwijderModal('<?= $pakket->VoedselpakketID ?>', <?= (int)$pakket->VoedselpakketID ?>, <?= $pakket->VoedselpakketID == 1 ? 'true' : 'false' ?>)"><span class='bi bi-trash'></span></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
            <!-- Modal verwijderen -->
            <div class="modal fade" id="verwijderPakketModal" tabindex="-1" aria-labelledby="verwijderPakketLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <form id="verwijderPakketForm" method="post" action="">
                    <div class="modal-header bg-danger text-white">
                      <h5 class="modal-title" id="verwijderPakketLabel">Voedselpakket verwijderen</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                    </div>
                    <div class="modal-body p-4">
                      <div id="verwijderMeldingContainer"></div>
                      <input type="hidden" name="verwijderpakketid" id="verwijderpakketid">
                      <p id="verwijderModalVraag">Weet je zeker dat je dit voedselpakket wilt verwijderen?</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                      <button type="submit" class="btn btn-danger">Verwijderen</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Geen voedselpakketten gevonden.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Modal toevoegen -->
            <div class="modal fade" id="voegPakketToeModal" tabindex="-1" aria-labelledby="voegPakketToeLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                  <form method="post" action="">
                    <div class="modal-header bg-primary text-white">
                      <h5 class="modal-title" id="voegPakketToeLabel">Voedselpakket toevoegen</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                    </div>
                    <div class="modal-body p-4">
                      <div id="melkUitverkochtAlertContainer"></div>
                      <div class="row g-4">
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label for="klant" class="form-label">Klant (gezin) <span class="text-danger">*</span></label>
                            <select name="klant" id="klant" class="form-select" required onchange="toonAllergie()">
                              <option value="">Selecteer een klant</option>
                              <?php foreach ($klanten as $klant): ?>
                                <option value="<?= $klant->KlantID ?>"> <?= htmlspecialchars($klant->gezinsnaam) ?> </option>
                              <?php endforeach; ?>
                            </select>
                            <div id="allergieInfo" class="mt-2 text-danger fw-semibold"></div>
                          </div>
                          <div class="mb-3">
                            <label for="datum_samenstelling" class="form-label">Datum samenstelling <span class="text-danger">*</span></label>
                            <input type="date" name="datum_samenstelling" id="datum_samenstelling" class="form-control" value="<?= date('Y-m-d') ?>" required>
                          </div>
                          <div class="mb-3">
                            <label for="omschrijving" class="form-label">Omschrijving <span class="text-danger">*</span></label>
                            <input type="text" name="omschrijving" id="omschrijving" class="form-control" placeholder="Bijv: Pakket samengesteld op <?= date('d-m-Y') ?>" required>
                          </div>
                          <div class="row mb-3">
                            <div class="col">
                              <label for="volwassenen" class="form-label">Volw. <span class="text-danger">*</span></label>
                              <input type="number" name="volwassenen" id="volwassenen" class="form-control" min="0" required>
                            </div>
                            <div class="col">
                              <label for="kinderen" class="form-label">Kind. <span class="text-danger">*</span></label>
                              <input type="number" name="kinderen" id="kinderen" class="form-control" min="0" required>
                            </div>
                            <div class="col">
                              <label for="babys" class="form-label">Baby's <span class="text-danger">*</span></label>
                              <input type="number" name="babys" id="babys" class="form-control" min="0" required>
                            </div>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Beschikbaar <span class="text-danger">*</span></label>
                            <select name="beschikbaar" class="form-select" required>
                              <option value="1">Ja</option>
                              <option value="0">Nee</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Producten in pakket <span class="text-danger">*</span></label>
                            <div class="table-responsive">
                              <table class="table table-bordered table-sm align-middle mb-2">
                                <thead class="table-light text-center">
                                  <tr>
                                    <th>Product</th>
                                    <th>Kies</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php foreach ($producten as $product): ?>
                                    <tr>
                                      <td><?= htmlspecialchars($product->ProductNaam) ?></td>
                                      <td class="text-center">
                                        <input class="form-check-input" type="checkbox" name="producten[]" value="<?= $product->ProductID ?>" id="product<?= $product->ProductID ?>" onclick="checkMelkUitverkocht(this, '<?= addslashes(strtolower($product->ProductNaam)) ?>')">
                                      </td>
                                    </tr>
                                  <?php endforeach; ?>
                                </tbody>
                              </table>
                            </div>
                            <label for="aantallen" class="form-label mt-2">Aantal per product <span class="text-danger">*</span> <small class="text-muted">(zelfde volgorde als selectie, gescheiden door komma's)</small></label>
                            <input type="text" name="aantallen" id="aantallen" class="form-control" placeholder="Bijv: 2,1,3" required>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                      <button type="submit" class="btn btn-success">Toevoegen</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!-- Modal wijzigen -->
            <div class="modal fade" id="wijzigPakketModal" tabindex="-1" aria-labelledby="wijzigPakketLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                  <form id="wijzigPakketForm" method="post" action="">
                    <div class="modal-header bg-warning text-dark">
                      <h5 class="modal-title" id="wijzigPakketLabel">Voedselpakket wijzigen</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                    </div>
                    <div class="modal-body p-4">
                      <div id="wijzigMeldingContainer"></div>
                      <input type="hidden" name="wijzigpakketid" id="wijzigpakketid">
                      <div class="mb-3">
                        <label for="wijzig_omschrijving" class="form-label">Omschrijving <span class="text-danger">*</span></label>
                        <input type="text" name="wijzig_omschrijving" id="wijzig_omschrijving" class="form-control" required>
                      </div>
                      <div class="row mb-3">
                        <div class="col">
                          <label for="wijzig_volwassenen" class="form-label">Volw. <span class="text-danger">*</span></label>
                          <input type="number" name="wijzig_volwassenen" id="wijzig_volwassenen" class="form-control" min="0" required>
                        </div>
                        <div class="col">
                          <label for="wijzig_kinderen" class="form-label">Kind. <span class="text-danger">*</span></label>
                          <input type="number" name="wijzig_kinderen" id="wijzig_kinderen" class="form-control" min="0" required>
                        </div>
                        <div class="col">
                          <label for="wijzig_babys" class="form-label">Baby's <span class="text-danger">*</span></label>
                          <input type="number" name="wijzig_babys" id="wijzig_babys" class="form-control" min="0" required>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="wijzig_datum" class="form-label">Datum samenstelling <span class="text-danger">*</span></label>
                        <input type="date" name="wijzig_datum" id="wijzig_datum" class="form-control" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                      <button type="submit" class="btn btn-warning">Opslaan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toon allergie info bij klantselectie
    const allergieData = <?= json_encode($klantAllergieen) ?>;
    function toonAllergie() {
        const klantSelect = document.getElementById('klant');
        const allergieDiv = document.getElementById('allergieInfo');
        const klantID = klantSelect.value;
        if (klantID && allergieData[klantID]) {
            allergieDiv.textContent = 'Let op: deze klant heeft de volgende allergieën: ' + allergieData[klantID];
        } else {
            allergieDiv.textContent = '';
        }
    }
    // Open modal na foutmelding of allergie
    <?php if ($modalOpen): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('voegPakketToeModal'));
        modal.show();
    });
    <?php endif; ?>
    // Meldingen automatisch verbergen
    document.addEventListener('DOMContentLoaded', function () {
        const alerts = ['datumErrorAlert', 'meldingAlert', 'foutmeldingAlert'];
        alerts.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                setTimeout(() => {
                    el.style.opacity = '0';
                    el.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => el.style.display = 'none', 500);
                }, 3000);
            }
        });
    });
    function checkMelkUitverkocht(checkbox, productnaam) {
        if (productnaam === 'melk halfvol 1l' && checkbox.checked) {
            checkbox.checked = false;
            showMelkUitverkochtAlert();
        }
    }
    function showMelkUitverkochtAlert() {
        let alertDiv = document.getElementById('melkUitverkochtAlert');
        if (!alertDiv) {
            alertDiv = document.createElement('div');
            alertDiv.id = 'melkUitverkochtAlert';
            alertDiv.className = 'alert alert-danger text-center mb-3';
            alertDiv.innerText = 'Dit voedselpakket is uitverkocht (Melk halfvol 1L is niet beschikbaar).';
            const container = document.getElementById('melkUitverkochtAlertContainer');
            container.appendChild(alertDiv);
        }
        setTimeout(() => {
            if (alertDiv) alertDiv.remove();
        }, 3000);
    }
    function openWijzigModal(id, beschikbaar, omschrijving, volw, kind, babys, datum) {
        document.getElementById('wijzigpakketid').value = id;
        document.getElementById('wijzig_omschrijving').value = omschrijving;
        document.getElementById('wijzig_volwassenen').value = volw;
        document.getElementById('wijzig_kinderen').value = kind;
        document.getElementById('wijzig_babys').value = babys;
        document.getElementById('wijzig_datum').value = datum;
        document.getElementById('wijzigMeldingContainer').innerHTML = '';
        const modal = new bootstrap.Modal(document.getElementById('wijzigPakketModal'));
        modal.show();
        document.getElementById('wijzigPakketForm').dataset.beschikbaar = beschikbaar ? '1' : '0';
    }
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('wijzigPakketForm').onsubmit = function(e) {
            e.preventDefault();
            const beschikbaar = this.dataset.beschikbaar;
            const meldingDiv = document.getElementById('wijzigMeldingContainer');
            if (beschikbaar === '0') {
                meldingDiv.innerHTML = '<div class="alert alert-danger text-center mb-3">Verzonden voedselpakketten kunnen niet meer worden aangepast</div>';
                setTimeout(() => { meldingDiv.innerHTML = ''; const modal = bootstrap.Modal.getInstance(document.getElementById('wijzigPakketModal')); modal.hide(); }, 3000);
                return false;
            }
            meldingDiv.innerHTML = '<div class="alert alert-success text-center mb-3">Voedselpakket is succesvol gewijzigd</div>';
            // Update de rij in de tabel direct (alleen frontend demo)
            const id = document.getElementById('wijzigpakketid').value;
            const rows = document.querySelectorAll('.table-responsive table tbody tr');
            rows.forEach(function(row) {
                if (row.dataset && row.dataset.pakketid == id) {
                    row.children[1].textContent = document.getElementById('wijzig_omschrijving').value;
                    row.children[2].textContent = document.getElementById('wijzig_volwassenen').value;
                    row.children[3].textContent = document.getElementById('wijzig_kinderen').value;
                    row.children[4].textContent = document.getElementById('wijzig_babys').value;
                    row.children[7].textContent = (function(d){let dt=new Date(d);return dt.toLocaleDateString('nl-NL');})(document.getElementById('wijzig_datum').value);
                }
            });
            setTimeout(() => { meldingDiv.innerHTML = ''; const modal = bootstrap.Modal.getInstance(document.getElementById('wijzigPakketModal')); modal.hide(); }, 2000);
            return false;
        };
    });
    function openVerwijderModal(pakketid, pakketnr, isAdmin) {
        document.getElementById('verwijderpakketid').value = pakketid;
        document.getElementById('verwijderMeldingContainer').innerHTML = '';
        if (isAdmin) {
            document.getElementById('verwijderModalVraag').textContent = 'Het admin-pakket (pakketnr 1) kan niet worden verwijderd.';
            document.getElementById('verwijderPakketForm').onsubmit = function(e) {
                e.preventDefault();
                document.getElementById('verwijderMeldingContainer').innerHTML = '<div class="alert alert-danger text-center mb-3">Het admin-pakket kan niet worden verwijderd.</div>';
                setTimeout(() => {
                    document.getElementById('verwijderMeldingContainer').innerHTML = '';
                    const modal = bootstrap.Modal.getInstance(document.getElementById('verwijderPakketModal'));
                    modal.hide();
                }, 2000);
                return false;
            };
        } else {
            document.getElementById('verwijderModalVraag').textContent = 'Weet je zeker dat je dit voedselpakket wilt verwijderen?';
            document.getElementById('verwijderPakketForm').onsubmit = function(e) {
                e.preventDefault();
                document.getElementById('verwijderMeldingContainer').innerHTML = '<div class="alert alert-success text-center mb-3">Voedselpakket is succesvol verwijderd</div>';
                const id = document.getElementById('verwijderpakketid').value;
                const rows = document.querySelectorAll('.table-responsive table tbody tr');
                rows.forEach(function(row) {
                    if (row.dataset && row.dataset.pakketid == id) {
                        row.remove();
                    }
                });
                setTimeout(() => {
                    document.getElementById('verwijderMeldingContainer').innerHTML = '';
                    const modal = bootstrap.Modal.getInstance(document.getElementById('verwijderPakketModal'));
                    modal.hide();
                }, 2000);
                return false;
            };
        }
        const modal = new bootstrap.Modal(document.getElementById('verwijderPakketModal'));
        modal.show();
    }
</script>
<?php require_once APPROOT . '/views/includes/footer.php'; ?>
<?php
// Simuleer toevoegen van pakket aan tabel na succesvolle POST (alleen voor deze view, niet in database)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($foutmelding)) {
    $klantNaam = '';
    foreach ($klanten as $klant) {
        if ($klant->KlantID == $_POST['klant']) {
            $klantNaam = htmlspecialchars($klant->gezinsnaam);
            break;
        }
    }
    $omschrijving = htmlspecialchars($_POST['omschrijving']);
    $volw = (int)$_POST['volwassenen'];
    $kind = (int)$_POST['kinderen'];
    $babys = (int)$_POST['babys'];
    $beschikbaar = isset($_POST['beschikbaar']) && $_POST['beschikbaar'] == '1';
    $datum = isset($_POST['datum_samenstelling']) ? $_POST['datum_samenstelling'] : date('Y-m-d');
    $datum_nl = date('d-m-Y', strtotime($datum));
    $productenArr = isset($_POST['producten']) ? $_POST['producten'] : [];
    $aantallenArr = array_map('trim', explode(',', $_POST['aantallen']));
    $details = [];
    foreach ($productenArr as $i => $pid) {
        foreach ($producten as $p) {
            if ($p->ProductID == $pid) {
                $aantal = isset($aantallenArr[$i]) ? (int)$aantallenArr[$i] : 1;
                $details[] = htmlspecialchars($p->ProductNaam) . ' (' . $aantal . 'x)';
            }
        }
    }
    $detailsStr = $details ? implode(', ', $details) : '-';
    // Bepaal hoogste pakketnummer (nieuwste = hoogste)
    $huidigeMax = 1;
    $sql = "SELECT MAX(VoedselpakketID) as maxid FROM voedselpakket";
    $db->query($sql);
    $row = $db->single();
    if ($row && $row->maxid) {
        $huidigeMax = (int)$row->maxid + 1;
    }
    echo '<script>document.addEventListener("DOMContentLoaded",function(){
      var tbody = document.querySelector(".table-responsive table tbody");
      if(tbody){
        var row = document.createElement("tr");
        row.setAttribute("data-pakketid", "' . $huidigeMax . '");
        row.innerHTML = `<td>' . $huidigeMax . '</td><td>' . $klantNaam . '</td><td>' . $omschrijving . '</td><td>' . $volw . '</td><td>' . $kind . '</td><td>' . $babys . '</td><td>' . $detailsStr . '</td><td>' . ($beschikbaar ? '<span class=\'badge-yes\'>Ja</span>' : '<span class=\'badge-no\'>Nee</span>') . '</td><td>' . $datum_nl . '</td><td><button class=\'btn btn-sm btn-outline-primary me-1\' onclick=\'openWijzigModal("' . $huidigeMax . '", ' . ($beschikbaar ? 'true' : 'false') . ', "' . addslashes($omschrijving) . '", ' . $volw . ', ' . $kind . ', ' . $babys . ', "' . $datum . '")\'>Wijzigen</button> <button class=\'btn btn-sm btn-outline-danger\' onclick=\'openVerwijderModal("' . $huidigeMax . '")\'>Verwijderen</button></td>`;
        tbody.prepend(row);
      }
    });</script>';
}
?>
