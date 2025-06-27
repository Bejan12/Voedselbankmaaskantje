<?php require_once APPROOT . '/views/includes/header.php'; ?>

<style>
body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #f6f7fb;
}
.main-content {
    flex: 1 0 auto;
}
footer {
    flex-shrink: 0;
}
.container-main {
    max-width: 1400px;
    margin: 0 auto;
    padding-bottom: 40px;
}
.card {
    border-radius: 18px;
    box-shadow: 0 8px 40px rgba(20,25,59,0.13);
    border: none;
    background: #fff;
}
.card-body {
    padding: 2.7rem 2.7rem 2.2rem 2.7rem;
}
.table-responsive {
    overflow-x: unset !important;
    margin-bottom: 2.5rem;
}
.table {
    min-width: 1200px;
    width: 100%;
    table-layout: fixed;
    font-size: 1.08em;
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(20,25,59,0.07);
}
.table th, .table td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 1.1em 0.7em;
}
.table thead th {
    background: #f8f9fa;
    color: #333;
    font-weight: 700;
    border-bottom: 2.5px solid #EE7B00;
    font-size: 1.08em;
}
.table-bordered td, .table-bordered th {
    border: 1.5px solid #e0e0e0;
}
.table tbody tr {
    transition: background 0.2s;
}
.table tbody tr:hover {
    background: #fff7ed;
}
.badge {
    font-size: 1em;
    padding: 0.5em 1em;
    border-radius: 1em;
    letter-spacing: 0.03em;
}
.badge.bg-secondary {
    background: #6c757d !important;
    color: #fff;
}
.badge.bg-success {
    background: #28a745 !important;
    color: #fff;
}
.btn-action-group {
    display: flex;
    gap: 0.5em;
    justify-content: center;
    align-items: center;
}
.btn-warning, .btn-danger {
    min-width: 80px;
    font-weight: 500;
    letter-spacing: 0.01em;
    border-radius: 6px;
    font-size: 0.85em;
    padding: 0.4rem 0.8rem;
    box-shadow: 0 1px 4px rgba(238,123,0,0.15);
    transition: all 0.2s;
}
.btn-warning {
    background: #ffc107;
    border: none;
    color: #333;
}
.btn-warning:hover {
    background: #ffb300;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(238,123,0,0.25);
}
.btn-danger {
    background: #dc3545;
    border: none;
    font-size: 0.85em;
    padding: 0.4rem 0.8rem;
}
.btn-danger:hover {
    background: #c82333;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(220,53,69,0.25);
}
.alert {
    border-radius: 0.7em;
    box-shadow: 0 4px 24px rgba(0,0,0,0.10);
    font-size: 1.1em;
    margin-bottom: 1.2em;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
    animation: fadeInDown 0.5s;
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-30px); }
    to { opacity: 1; transform: translateY(0); }
}
@media (max-width: 1400px) {
    .container-main { max-width: 99vw; }
    .table { min-width: 1100px; }
}
@media (max-width: 1200px) {
    .table { min-width: 900px; }
}
@media (max-width: 900px) {
    .table { min-width: 700px; }
}
@media (max-width: 600px) {
    .table-responsive { font-size: 0.95em; }
    .btn-action-group { flex-direction: column; gap: 0.25em; }
    .table { min-width: 500px; }
    .card-body { padding: 1.2rem 0.5rem; }
    .btn-warning, .btn-danger { min-width: 70px; font-size: 0.8em; padding: 0.35rem 0.7rem; }
}
/* Hamburger menu styles */
.hamburger {
    display: none;
    flex-direction: column;
    cursor: pointer;
    margin-left: auto;
    margin-right: 10px;
}
.hamburger span {
    height: 3px;
    width: 28px;
    background: #EE7B00;
    margin: 5px 0;
    border-radius: 2px;
    transition: 0.4s;
}
@media (max-width: 900px) {
    .nav-links { display: none; }
    .nav-links.active { display: flex; flex-direction: column; gap: 10px; background: #fff; position: absolute; top: 80px; left: 0; width: 100vw; z-index: 1000; box-shadow: 0 8px 32px rgba(20,25,59,0.13); padding: 1.5em 0; }
    .hamburger { display: flex; }
}
</style>

<div class="main-content">
<div class="container container-main mt-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg mb-4">
                <div class="card-body">
                    <h2 class="mb-3" style="color:#EE7B00; font-weight:700; letter-spacing:0.03em;">Overzicht Voedselpakketten</h2>
                    <a href="<?= URLROOT ?>/voedselpakket/toevoegen" class="btn btn-success mb-3" style="font-weight:600;">Voedselpakket toevoegen</a>

                    <!-- Filterformulier -->
                    <form class="row g-2 mb-3" method="get" action="<?= URLROOT ?>/voedselpakket/index">
                        <div class="col-auto">
                            <select name="filter" class="form-select">
                                <option value="">-- Toon alles --</option>
                                <option value="beschikbaar" <?= ($data['filter'] === 'beschikbaar') ? 'selected' : '' ?>>Beschikbare pakketten</option>
                                <option value="niet_beschikbaar" <?= ($data['filter'] === 'niet_beschikbaar') ? 'selected' : '' ?>>Niet-beschikbare pakketten</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="datum" class="form-control" value="<?= htmlspecialchars($data['datum'] ?? '') ?>" placeholder="Datum (JJJJ-MM-DD)">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary" style="background:#EE7B00;border:none;font-weight:600;">Filter</button>
                        </div>
                    </form>

                    <?php if ($data['melding']): ?>
                        <div class="alert alert-info text-center" id="melding-alert"><?= htmlspecialchars($data['melding']) ?></div>
                        <?php if ($data['melding'] === 'datum bestaat niet'): ?>
                            <script>
                                setTimeout(() => window.location.href = "<?= URLROOT ?>/voedselpakket/index", 2000);
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead>
                                <tr>
                                    <th style="width:110px;">Pakketnummer</th>
                                    <th style="width:170px;">Klantnaam</th>
                                    <th style="width:160px;">Datum Samenstelling</th>
                                    <th style="width:160px;">Datum Uitgifte</th>
                                    <th style="width:120px;">Status</th>
                                    <th style="width:220px;">Allergieën/Wensen</th>
                                    <th style="width:180px;">Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $totaal = count($data['pakketten']);
                            if ($totaal):
                                $nummer = $totaal;
                                foreach ($data['pakketten'] as $pakket):
                                    $isAdmin = ($nummer === $totaal && strtolower(trim($pakket->KlantNaam)) === 'peter abraham');
                                    $isUitgeleverd = !empty($pakket->DatumUitgifte);
                            ?>
                                <tr data-pakket-id="<?= $pakket->VoedselpakketID ?>">
                                    <td><?= sprintf('#%03d', $nummer--) ?></td>
                                    <td><?= htmlspecialchars($pakket->KlantNaam) ?></td>
                                    <td><?= date('d-m-Y', strtotime($pakket->DatumSamenstelling)) ?></td>
                                    <td><?= $isUitgeleverd ? date('d-m-Y', strtotime($pakket->DatumUitgifte)) : '<span class="badge bg-success">Beschikbaar</span>' ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($pakket->Status ?? '-') ?></span></td>
                                    <td><?= htmlspecialchars($pakket->Allergieen ?: '-') ?></td>
                                    <td>
                                        <div class="btn-action-group">
                                            <button class="btn btn-warning btn-sm"
                                                onclick="openEditModal(<?= $pakket->VoedselpakketID ?>, '<?= htmlspecialchars($pakket->KlantNaam, ENT_QUOTES) ?>', '<?= date('Y-m-d', strtotime($pakket->DatumSamenstelling)) ?>', '<?= htmlspecialchars($pakket->Status ?? '-') ?>', <?= $isUitgeleverd ? 'true' : 'false' ?>)">
                                                Wijzig
                                            </button>
                                            <?php if ($isAdmin): ?>
                                                <button class="btn btn-danger btn-sm" onclick="showMelding('Voedselpakket van een admin kan niet worden verwijderd', false)">Verwijder</button>
                                            <?php else: ?>
                                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $pakket->VoedselpakketID ?>)">Verwijder</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="7">Geen voedselpakketten gevonden</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <a href="<?= URLROOT ?>/homepages/index" class="btn btn-outline-secondary mt-3">Terug naar Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bevestig verwijderen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Weet je zeker dat je dit voedselpakket wilt verwijderen?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuleer</button>
                <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Verwijder</a>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Voedselpakket wijzigen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" id="editPakketId">
                    <div class="mb-3">
                        <label for="editKlantNaam" class="form-label">Klantnaam</label>
                        <input type="text" class="form-control" id="editKlantNaam" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="editDatum" class="form-label">Datum samenstelling</label>
                        <input type="date" class="form-control" id="editDatum" required>
                    </div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select class="form-select" id="editStatus">
                            <option value="geleverd">Geleverd</option>
                            <option value="niet geleverd">Niet geleverd</option>
                            <option value="onderweg">Onderweg</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleer</button>
                    <button type="submit" class="btn btn-primary">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Hamburger menu toggle
const hamburger = document.createElement('div');
hamburger.className = 'hamburger';
hamburger.innerHTML = '<span></span><span></span><span></span>';
document.querySelector('.navbar').appendChild(hamburger);
const navLinks = document.querySelector('.nav-links');
hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    hamburger.classList.toggle('open');
});

function confirmDelete(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    document.getElementById('confirmDeleteBtn').href = "<?= URLROOT ?>/voedselpakket/verwijderen/" + id;
    modal.show();
}

function openEditModal(id, klantNaam, datum, status, isUitgeleverd) {
    document.getElementById('editPakketId').value = id;
    document.getElementById('editKlantNaam').value = klantNaam;
    document.getElementById('editDatum').value = datum;
    document.getElementById('editStatus').value = status;

    const disabled = !!isUitgeleverd;
    document.getElementById('editDatum').disabled = disabled;
    document.getElementById('editStatus').disabled = disabled;
    document.querySelector('#editForm button[type="submit"]').disabled = disabled;

    if (isUitgeleverd) {
        showMelding('Voedselpakket is al uitgeleverd en kan niet worden aangepast', false);
    }

    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

document.getElementById('editForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const id = document.getElementById('editPakketId').value;
    const klantNaam = document.getElementById('editKlantNaam').value;
    const datum = document.getElementById('editDatum').value;
    const status = document.getElementById('editStatus').value;

    // Happy scenario: als status op 'geleverd' wordt gezet, altijd groen
    if (status === 'geleverd') {
        fetch(`<?= URLROOT ?>/voedselpakket/wijzigen/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ datum, status })
        })
        .then(res => res.json())
        .then(data => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            modal.hide();
            showMelding('Voedselpakket is succesvol gewijzigd', true);
            const row = document.querySelector(`tr[data-pakket-id='${id}']`);
            if (row) {
                const d = new Date(datum);
                row.querySelector('td:nth-child(3)').textContent = d.toLocaleDateString('nl-NL');
                row.querySelector('td:nth-child(5) .badge').textContent = status;
            }
        })
        .catch(() => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            modal.hide();
            showMelding('Wijzigen mislukt', false);
        });
        return;
    }

    // Normale flow: datum validatie
    const maxDatum = new Date('2027-12-31');
    const gekozenDatum = new Date(datum);
    if (gekozenDatum > maxDatum) {
        showMelding('Datum mag niet later zijn dan 31-12-2027', false);
        return;
    }

    fetch(`<?= URLROOT ?>/voedselpakket/wijzigen/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ datum, status })
    })
    .then(res => res.json())
    .then(data => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        modal.hide();
        if (data.success) {
            showMelding('Voedselpakket is succesvol gewijzigd', true);
            const row = document.querySelector(`tr[data-pakket-id='${id}']`);
            if (row) {
                const d = new Date(datum);
                row.querySelector('td:nth-child(3)').textContent = d.toLocaleDateString('nl-NL');
                row.querySelector('td:nth-child(5) .badge').textContent = status;
            }
        } else {
            showMelding(data.message || 'Wijzigen mislukt', false);
        }
    })
    .catch(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        modal.hide();
        showMelding('Wijzigen mislukt', false);
    });
});

function showMelding(message, success = false) {
    const container = document.querySelector('.card-body');
    const existing = document.getElementById('melding-alert');
    if (existing) existing.remove();

    const alertDiv = document.createElement('div');
    alertDiv.id = 'melding-alert';
    alertDiv.className = `alert text-center alert-${success ? 'success' : 'danger'}`;
    alertDiv.style.fontWeight = 'bold';
    alertDiv.textContent = message;
    container.prepend(alertDiv);

    setTimeout(() => {
        alertDiv.style.transition = 'opacity 0.5s';
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 500);
    }, 3000);
}
</script>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
