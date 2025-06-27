<?php require_once APPROOT . '/views/includes/header.php'; ?>

<style>
    .klanten-container {
        margin-top: 120px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(20,25,59,0.08);
        padding: 32px 32px 24px 32px;
        max-width: 600px;
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
    .form-label {
        color: #14193B;
        font-weight: 600;
        margin-bottom: 2px;
        display: block;
        text-align: left;
    }
    .form-group {
        margin-bottom: 18px;
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }
    .form-control {
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        font-size: 1rem;
        padding: 10px 12px;
        background: #fafafa;
        transition: border-color 0.2s;
        margin-top: 0;
    }
    .form-control:focus {
        border-color: #FF7F32;
        box-shadow: 0 0 0 0.2rem rgba(255,127,50,0.15);
        background: #fff;
    }
    .form-check-label {
        font-weight: 500;
        color: #333;
    }
    .form-check-input:checked {
        background-color: #FF7F32;
        border-color: #FF7F32;
    }
    .alert-danger {
        border-radius: 6px;
        font-size: 1rem;
        margin-bottom: 24px;
    }
    .btn-secondary {
        border-radius: 6px;
        font-weight: 600;
        padding: 10px 28px;
        margin-left: 8px;
    }
</style>

<div class="klanten-container">
    <div class="klanten-header-row">
        <h3>Klantgegevens wijzigen</h3>
        <a href="<?= URLROOT; ?>klanten/index" class="btn btn-orange">Terug</a>
    </div>
    <?php if (!empty($data['error'])): ?>
        <div class="alert alert-danger"><?= $data['error']; ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <div class="form-group">
            <label for="voornaam" class="form-label">Voornaam*</label>
            <input type="text" class="form-control" id="voornaam" name="voornaam" value="<?= htmlspecialchars($data['form']['voornaam'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="achternaam" class="form-label">Achternaam*</label>
            <input type="text" class="form-control" id="achternaam" name="achternaam" value="<?= htmlspecialchars($data['form']['achternaam'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="adres" class="form-label">Adres*</label>
            <input type="text" class="form-control" id="adres" name="adres" value="<?= htmlspecialchars($data['form']['adres'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="telefoon" class="form-label">Telefoonnummer*</label>
            <input type="text" class="form-control" id="telefoon" name="telefoon" value="<?= htmlspecialchars($data['form']['telefoon'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">E-mailadres*</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($data['form']['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="aantal_volwassenen" class="form-label">Aantal volwassenen*</label>
            <input type="number" class="form-control" id="aantal_volwassenen" name="aantal_volwassenen" min="0" value="<?= htmlspecialchars($data['form']['aantal_volwassenen'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="aantal_kinderen" class="form-label">Aantal kinderen*</label>
            <input type="number" class="form-control" id="aantal_kinderen" name="aantal_kinderen" min="0" value="<?= htmlspecialchars($data['form']['aantal_kinderen'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="aantal_babys" class="form-label">Aantal baby's*</label>
            <input type="number" class="form-control" id="aantal_babys" name="aantal_babys" min="0" value="<?= htmlspecialchars($data['form']['aantal_babys'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label class="form-label">Specifieke wensen</label>
            <div>
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
        </div>
        <button type="submit" class="btn btn-orange">Opslaan</button>
        <a href="<?= URLROOT; ?>klanten/index" class="btn btn-secondary">Annuleren</a>
    </form>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
