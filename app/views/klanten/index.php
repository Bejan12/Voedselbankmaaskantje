<?php require_once APPROOT . '/views/includes/header.php'; ?>

<style>
    .klanten-container {
        margin-top: 120px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(20,25,59,0.08);
        padding: 32px 32px 24px 32px;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
    }
    .klanten-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }
    .klanten-header-row h3 {
        margin: 0;
        font-size: 2rem;
        color: #14193B;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .btn-orange {
        background-color: #FF7F32;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 10px 28px;
        font-weight: 600;
        font-size: 1rem;
        transition: background 0.2s;
        box-shadow: 0 2px 8px rgba(255,127,50,0.08);
    }
    .btn-orange:hover, .btn-orange:focus {
        background-color: #e86c1a;
        color: #fff;
    }
    .table-orange th {
        background-color: #FF7F32;
        color: #fff;
        text-align: center;
        font-weight: 600;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
        border-bottom: 2px solid #e86c1a;
    }
    .table-orange td {
        text-align: center;
        vertical-align: middle;
        background: #fafafa;
    }
    .btn-primary {
        background-color: #fff;
        color: #FF7F32;
        border: 2px solid #FF7F32;
        border-radius: 6px;
        font-weight: 600;
        padding: 6px 18px;
        transition: background 0.2s, color 0.2s;
    }
    .btn-primary:hover, .btn-primary:focus {
        background-color: #FF7F32;
        color: #fff;
    }
    .btn-danger {
        background-color: #ff3b3b;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        padding: 6px 18px;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(255,59,59,0.08);
    }
    .btn-danger:hover, .btn-danger:focus {
        background-color: #d32f2f;
        color: #fff;
        box-shadow: 0 4px 16px rgba(255,59,59,0.16);
    }
    .alert-warning, .alert-danger, .alert-success {
        border-radius: 6px;
        font-size: 1rem;
        margin-bottom: 24px;
    }
</style>

<div class="klanten-container">
    <div class="klanten-header-row">
        <h3><?= $data['title']; ?></h3>
        <a href="<?= URLROOT; ?>/klanten/add" class="btn btn-orange">Klant toevoegen</a>
    </div>

    <?php if (!empty($data['success'])): ?>
        <div class="alert alert-success"><?= $data['success']; ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="alert alert-warning">
            <?= $data['error']; ?>
        </div>
        <script>
            setTimeout(function() {
                window.location.href = "<?= URLROOT; ?>homepages/index";
            }, 3000);
        </script>
    <?php endif; ?>

    <?php if (empty($data['error'])): ?>
        <?php if (!empty($data['melding'])): ?>
            <div class="alert alert-success"><?= $data['melding']; ?></div>
        <?php endif; ?>
        <?php if (!empty($data['foutmelding'])): ?>
            <div class="alert alert-danger"><?= $data['foutmelding']; ?></div>
        <?php endif; ?>
        <table class="table table-bordered table-orange mt-3">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>E-mailadres</th>
                    <th>Telefoonnummer</th>
                    <th>Aantal Volwassenen</th>
                    <th>Aantal Kinderen</th>
                    <th>Aantal Baby's</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['klanten'] as $klant): ?>
                    <tr>
                        <td><?= htmlspecialchars($klant->Voornaam . ' ' . $klant->Achternaam); ?></td>
                        <td><?= htmlspecialchars($klant->Email); ?></td>
                        <td><?= htmlspecialchars($klant->Telefoon); ?></td>
                        <td><?= htmlspecialchars($klant->AantalVolwassenen); ?></td>
                        <td><?= htmlspecialchars($klant->AantalKinderen); ?></td>
                        <td><?= htmlspecialchars($klant->AantalBabys); ?></td>
                        <td>
                            <a href="<?= URLROOT; ?>klanten/edit/<?= $klant->KlantID; ?>" class="btn btn-primary btn-sm">Wijzig</a>
                            <a href="<?= URLROOT; ?>klanten/verwijder/<?= $klant->KlantID; ?>" class="btn btn-danger btn-sm" onclick="return bevestigDelete();">Verwijder</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
