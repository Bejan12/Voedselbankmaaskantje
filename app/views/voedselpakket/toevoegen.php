<?php require_once APPROOT . '/views/includes/header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-lg mb-4">
                <div class="card-body">
                    <h2 class="mb-3" style="color:#EE7B00;">Voedselpakket toevoegen</h2>
                    <?php if($data['melding']): ?>
                        <div class="alert alert-<?= $data['success'] ? 'success' : 'danger' ?> text-center"> <?= htmlspecialchars($data['melding']) ?> </div>
                    <?php endif; ?>
                    <form method="post" action="" onsubmit="return validateForm()">
                        <div class="mb-3">
                            <label for="klantId" class="form-label">Klant</label>
                            <select name="klantId" id="klantId" class="form-select" required onchange="showAllergie(); showProducten();">
                                <option value="">-- Kies een klant --</option>
                                <?php foreach($data['klanten'] as $klant): ?>
                                    <option value="<?= $klant->KlantID ?>" data-allergie="<?= htmlspecialchars($klant->Allergieen ?: '-') ?>">
                                        <?= htmlspecialchars($klant->Naam) ?> (<?= htmlspecialchars($klant->Email) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3" id="allergieBox" style="display:none;">
                            <label class="form-label">Allergieën klant</label>
                            <div id="allergieText" class="alert alert-warning"></div>
                        </div>
                        <div class="mb-3">
                            <label for="datum" class="form-label">Datum samenstelling</label>
                            <input type="date" name="datum" id="datum" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div id="productenBox">
                            <!-- Hier komen de product-selects per categorie -->
                            <?php foreach($data['categorieen'] as $categorie): ?>
                                <div class="mb-3 product-select" data-categorie="<?= $categorie->CategorieID ?>">
                                    <label class="form-label"><?= htmlspecialchars($categorie->Naam) ?></label>
                                    <select name="producten[<?= $categorie->CategorieID ?>]" class="form-select product-dropdown" data-categorie="<?= $categorie->CategorieID ?>">
                                        <option value="">-- Kies een product --</option>
                                        <?php foreach($data['producten'][$categorie->CategorieID] as $product): ?>
                                            <option value="<?= $product->ProductID ?>" data-allergieen="<?= htmlspecialchars($product->Allergieen) ?>">
                                                <?= htmlspecialchars($product->ProductNaam) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="btn btn-primary" style="background:#EE7B00;border:none;">Voeg toe</button>
                        <a href="<?= URLROOT ?>/voedselpakket/index" class="btn btn-outline-secondary ms-2">Annuleer</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function validateForm() {
    const datum = document.getElementById('datum').value;
    const vandaag = new Date().toISOString().split('T')[0];
    if (datum < vandaag) {
        alert('Je kunt geen datum in het verleden kiezen.');
        return false;
    }
    return true;
}
function showAllergie() {
    const select = document.getElementById('klantId');
    const box = document.getElementById('allergieBox');
    const text = document.getElementById('allergieText');
    const option = select.options[select.selectedIndex];
    if (option.value && option.dataset.allergie) {
        box.style.display = 'block';
        text.textContent = option.dataset.allergie;
    } else {
        box.style.display = 'none';
        text.textContent = '';
    }
}
function showProducten() {
    // Filter producten per categorie op basis van allergie klant
    const klantSelect = document.getElementById('klantId');
    const klantOption = klantSelect.options[klantSelect.selectedIndex];
    const allergieString = klantOption.dataset.allergie || '';
    const allergieen = allergieString.split(',').map(a => a.trim().toLowerCase());
    document.querySelectorAll('.product-select').forEach(function(div) {
        const select = div.querySelector('select');
        Array.from(select.options).forEach(function(opt) {
            if (!opt.value) return; // skip placeholder
            const prodAllergie = (opt.dataset.allergieen || '').split(',').map(a => a.trim().toLowerCase());
            // Als klant allergie heeft en product bevat die allergie, verberg optie
            let hide = false;
            allergieen.forEach(function(klantAllergie) {
                if (klantAllergie && prodAllergie.includes(klantAllergie)) hide = true;
            });
            opt.style.display = hide ? 'none' : '';
        });
    });
}
document.addEventListener('DOMContentLoaded', function() {
    showProducten();
});
</script>
<?php require_once APPROOT . '/views/includes/footer.php'; ?>
