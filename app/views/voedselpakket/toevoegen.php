<?php require_once APPROOT . '/views/includes/header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-lg mb-4">
                <div class="card-body">
                    <h2 class="mb-3" style="color:#EE7B00;">Voedselpakket toevoegen</h2>
                    <?php if(isset($data['melding']) && $data['melding']): ?>
                        <div class="alert alert-<?= !empty($data['success']) ? 'success' : 'danger' ?> text-center"> <?= htmlspecialchars($data['melding']) ?> </div>
                    <?php endif; ?>
                    <form method="post" action="" onsubmit="return validateForm()">
                        <div class="mb-3">
                            <label for="klantId" class="form-label">Klant</label>
                            <select name="klantId" id="klantId" class="form-select" required onchange="showAllergie(); showKeuzeBox();">
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
                        <!-- Pakketkeuze of samenstellen -->
                        <div class="mb-3" id="pakketKeuzeBox">
                            <label for="pakketCategorieId" class="form-label">Pakketkeuze</label>
                            <select name="pakketCategorieId" id="pakketCategorieId" class="form-select">
                                <option value="">-- Kies een standaardpakket --</option>
                                <?php foreach($data['categorieen'] as $cat): ?>
                                    <option value="<?= $cat->CategorieID ?>"><?= htmlspecialchars($cat->Naam) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="samenstelBox" style="display:none;">
                            <h5 class="mt-4 mb-2" style="color:#EE7B00;">Stel een allergievrij pakket samen</h5>
                            <div class="alert alert-info">Vink hieronder de producten aan die géén allergenen bevatten voor deze klant.</div>
                            <?php if(isset($data['productenPerCategorie']) && is_array($data['productenPerCategorie'])): ?>
                                <?php foreach($data['productenPerCategorie'] as $catId => $producten): ?>
                                    <div class="mb-2"><b><?= htmlspecialchars($data['categorieNamen'][$catId] ?? '') ?></b></div>
                                    <div class="row mb-3">
                                        <?php foreach($producten as $product): ?>
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="samenstel[<?= $catId ?>][]" value="<?= $product->ProductID ?>" id="prod<?= $product->ProductID ?>">
                                                    <label class="form-check-label" for="prod<?= $product->ProductID ?>">
                                                        <?= htmlspecialchars($product->ProductNaam) ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3" id="opmerkingBox">
                            <label for="opmerking" class="form-label">Opmerking (optioneel)</label>
                            <textarea name="opmerking" id="opmerking" class="form-control" rows="2"></textarea>
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
    if (option.value && option.dataset.allergie && option.dataset.allergie !== '-') {
        box.style.display = 'block';
        text.textContent = option.dataset.allergie;
    } else {
        box.style.display = 'none';
        text.textContent = '';
    }
}
function showKeuzeBox() {
    const klantSelect = document.getElementById('klantId');
    const klantOption = klantSelect.options[klantSelect.selectedIndex];
    const allergieString = klantOption.dataset.allergie || '';
    const pakketKeuzeBox = document.getElementById('pakketKeuzeBox');
    const samenstelBox = document.getElementById('samenstelBox');
    if (allergieString && allergieString !== '-') {
        pakketKeuzeBox.style.display = 'none';
        samenstelBox.style.display = 'block';
    } else {
        pakketKeuzeBox.style.display = 'block';
        samenstelBox.style.display = 'none';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    showKeuzeBox();
});
</script>
<?php require_once APPROOT . '/views/includes/footer.php'; ?>
