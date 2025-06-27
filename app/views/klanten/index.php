<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container" style="margin-top: 120px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h3><?= $data['title']; ?></h3>
        <a href="<?= URLROOT; ?>klanten/add" class="btn btn-success" style="min-width: 180px;">Klanten toevoegen</a>
    </div>

    <?php if (!empty($data['error'])): ?>
        <div class="alert alert-warning mt-3">
            <?= $data['error']; ?>
        </div>
        <script>
            setTimeout(function() {
                window.location.href = "<?= URLROOT; ?>homepages/index";
            }, 3000);
        </script>
    <?php else: ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>E-mailadres</th>
                    <th>Telefoonnummer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['klanten'] as $klant): ?>
                    <tr>
                        <td><?= htmlspecialchars($klant->Voornaam . ' ' . $klant->Achternaam); ?></td>
                        <td><?= htmlspecialchars($klant->Email); ?></td>
                        <td><?= htmlspecialchars($klant->Telefoon); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
