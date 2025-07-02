<?php require_once APPROOT . '/views/includes/header.php'; ?>

<style>
    .container {
        margin-top: 20px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        padding: 20px;
    }

    .title {
        text-align: center;
        font-weight: bold;
        margin-bottom: 30px;
        color: #14193B;
    }

    .form-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        color: #14193B;
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        background: #fff;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: #7386FF;
        outline: none;
        box-shadow: 0 0 0 2px rgba(115,134,255,0.2);
    }

    .form-check-container {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-check-input {
        margin: 0;
    }

    .form-check-label {
        font-weight: 500;
        color: #333;
        margin: 0;
    }

    .btn-container {
        margin-top: 30px;
        display: flex;
        gap: 10px;
        justify-content: flex-start;
    }

    .btn-primary {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: bold;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #218838;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: bold;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        text-decoration: none;
        color: white;
    }

    .alert {
        padding: 12px 20px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        font-weight: 500;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .back-link {
        display: inline-block;
        color: #007bff;
        text-decoration: none;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .back-link:hover {
        text-decoration: underline;
    }
</style>

<div class="container">
    <a href="<?= URLROOT; ?>klanten/index" class="back-link">← Terug naar overzicht</a>
    
    <h1 class="title">Klant toevoegen</h1>
    
    <div class="form-container">
        <?php if (!empty($data['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($data['error']); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="voornaam" class="form-label">Voornaam*</label>
                <input type="text" class="form-control" id="voornaam" name="voornaam" 
                       value="<?= htmlspecialchars($data['form']['voornaam'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="achternaam" class="form-label">Achternaam*</label>
                <input type="text" class="form-control" id="achternaam" name="achternaam" 
                       value="<?= htmlspecialchars($data['form']['achternaam'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="adres" class="form-label">Adres*</label>
                <input type="text" class="form-control" id="adres" name="adres" 
                       value="<?= htmlspecialchars($data['form']['adres'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="telefoon" class="form-label">Telefoonnummer*</label>
                <input type="text" class="form-control" id="telefoon" name="telefoon" 
                       value="<?= htmlspecialchars($data['form']['telefoon'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">E-mailadres*</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($data['form']['email'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="aantal_volwassenen" class="form-label">Aantal volwassenen*</label>
                <input type="number" class="form-control" id="aantal_volwassenen" name="aantal_volwassenen" 
                       min="0" value="<?= htmlspecialchars($data['form']['aantal_volwassenen'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="aantal_kinderen" class="form-label">Aantal kinderen*</label>
                <input type="number" class="form-control" id="aantal_kinderen" name="aantal_kinderen" 
                       min="0" value="<?= htmlspecialchars($data['form']['aantal_kinderen'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="aantal_babys" class="form-label">Aantal baby's*</label>
                <input type="number" class="form-control" id="aantal_babys" name="aantal_babys" 
                       min="0" value="<?= htmlspecialchars($data['form']['aantal_babys'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Specifieke wensen</label>
                <div class="form-check-container">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="geen_varkensvlees" 
                               name="geen_varkensvlees" value="1" 
                               <?= !empty($data['form']['geen_varkensvlees']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="geen_varkensvlees">Geen varkensvlees</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="veganistisch" 
                               name="veganistisch" value="1" 
                               <?= !empty($data['form']['veganistisch']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="veganistisch">Veganistisch</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="vegetarisch" 
                               name="vegetarisch" value="1" 
                               <?= !empty($data['form']['vegetarisch']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="vegetarisch">Vegetarisch</label>
                    </div>
                </div>
            </div>
            
            <div class="btn-container">
                <button type="submit" class="btn-primary">Opslaan</button>
                <a href="<?= URLROOT; ?>klanten/index" class="btn-secondary">Annuleren</a>
            </div>
        </form>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
