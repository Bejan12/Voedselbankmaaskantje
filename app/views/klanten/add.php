<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container" style="margin-top: 120px; max-width: 600px;">
    <h3>Klant toevoegen</h3>
    <?php if (!empty($data['error'])): ?>
        <div class="alert alert-danger"><?= $data['error']; ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <div class="mb-3">
            <label for="voornaam" class="form-label">Voornaam*</label>
            <input type="text" class="form-control" id="voornaam" name="voornaam" value="<?= htmlspecialchars($data['form']['voornaam'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="achternaam" class="form-label">Achternaam*</label>
            <input type="text" class="form-control" id="achternaam" name="achternaam" value="<?= htmlspecialchars($data['form']['achternaam'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="adres" class="form-label">Adres*</label>
            <input type="text" class="form-control" id="adres" name="adres" value="<?= htmlspecialchars($data['form']['adres'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="telefoon" class="form-label">Telefoonnummer*</label>
            <input type="text" class="form-control" id="telefoon" name="telefoon" value="<?= htmlspecialchars($data['form']['telefoon'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mailadres*</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($data['form']['email'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="aantal_volwassenen" class="form-label">Aantal volwassenen*</label>
            <input type="number" class="form-control" id="aantal_volwassenen" name="aantal_volwassenen" min="0" value="<?= htmlspecialchars($data['form']['aantal_volwassenen'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="aantal_kinderen" class="form-label">Aantal kinderen*</label>
            <input type="number" class="form-control" id="aantal_kinderen" name="aantal_kinderen" min="0" value="<?= htmlspecialchars($data['form']['aantal_kinderen'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="aantal_babys" class="form-label">Aantal baby's*</label>
            <input type="number" class="form-control" id="aantal_babys" name="aantal_babys" min="0" value="<?= htmlspecialchars($data['form']['aantal_babys'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Specifieke wensen</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="geen_varkensvlees" name="geen_varkensvlees" value="1" <?= !empty($data['form']['geen_varkensvlees']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="geen_varkensvlees">Geen varkensvlees</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="veganistisch" name="veganistisch" value="1" <?= !empty($data['form']['veganistisch']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="veganistisch">Veganistisch</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="vegetarisch" name="vegetarisch" value="1" <?= !empty($data['form']['vegetarisch']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="vegetarisch">Vegetarisch</label>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Opslaan</button>
        <a href="<?= URLROOT; ?>klanten/index" class="btn btn-secondary">Annuleren</a>
    </form>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
