<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container" style="margin-top: 80px; max-width: 1000px;">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h2 class="mb-4 text-primary">Afgeronde voedselpakketten per klant</h2>
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Naam</th>
                        <th>E-mailadres</th>
                        <th>Telefoonnummer</th>
                        <th style="width: 180px;">Actie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['klanten'] as $klant): ?>
                        <tr>
                            <td><?= htmlspecialchars($klant->Voornaam . ' ' . $klant->Achternaam); ?></td>
                            <td><?= htmlspecialchars($klant->Email); ?></td>
                            <td><?= htmlspecialchars($klant->Telefoon); ?></td>
                            <td>
                                <a href="<?= URLROOT; ?>klanten/pakketten/<?= $klant->KlantID; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box-seam"></i> Bekijk pakketten
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
