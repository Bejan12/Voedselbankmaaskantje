<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header sectie -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <a href="<?= URLROOT; ?>/magazijnvoorraad" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="text-dark mb-1">
                            <i class="bi bi-plus-circle me-2 text-success"></i><?php echo $data['title']; ?>
                        </h2>
                        <p class="subtitle mb-0">Voeg een nieuw product toe aan de voorraad</p>
                    </div>
                </div>
            </div>

            <!-- Status berichten -->
            <?php if (isset($data['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= $data['error']; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($data['success'])): ?>
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= $data['success']; ?>
                </div>
            <?php endif; ?>

            <!-- Formulier -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-box me-2"></i>Product Gegevens
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= URLROOT; ?>/magazijnvoorraad/voegProductToe" method="POST" id="productForm">
                        <div class="row">
                            <!-- Productnaam -->
                            <div class="col-md-6 mb-3">
                                <label for="productnaam" class="form-label">
                                    <i class="bi bi-tag me-1"></i>Productnaam *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="productnaam" 
                                       name="productnaam" 
                                       placeholder="Bijv. Aardappelen vastkokend 2kg"
                                       value="<?= isset($data['formData']['productnaam']) ? htmlspecialchars($data['formData']['productnaam']) : ''; ?>"
                                       required>
                            </div>

                            <!-- EAN-code (automatisch gegenereerd, niet aanpasbaar) -->
                            <div class="col-md-6 mb-3">
                                <label for="ean" class="form-label">
                                    <i class="bi bi-upc-scan me-1"></i>EAN-code *
                                </label>
                                <input type="text" 
                                       class="form-control bg-light" 
                                       id="ean" 
                                       name="ean" 
                                       placeholder="Wordt automatisch gegenereerd..."
                                       pattern="[0-9]{13}"
                                       title="EAN-code wordt automatisch gegenereerd"
                                       value="<?= isset($data['formData']['ean']) ? htmlspecialchars($data['formData']['ean']) : ''; ?>"
                                       readonly>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>EAN-code wordt automatisch gegenereerd
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Categorie met slimme validatie -->
                            <div class="col-md-6 mb-3">
                                <label for="categorie" class="form-label">
                                    <i class="bi bi-list me-1"></i>Categorie *
                                </label>
                                <select class="form-select" id="categorie" name="categorie_id" required>
                                    <option value="">Kies een categorie...</option>
                                    <?php if (isset($data['categorieën']) && !empty($data['categorieën'])): ?>
                                        <?php foreach ($data['categorieën'] as $categorie): ?>
                                            <option value="<?= $categorie->CategorieID; ?>" 
                                                    data-naam="<?= strtolower($categorie->Naam); ?>"
                                                    <?= (isset($data['formData']['categorie_id']) && $data['formData']['categorie_id'] == $categorie->CategorieID) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($categorie->Naam); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div id="categorieWarning" class="form-text text-warning" style="display: none;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <span id="categorieWarningText"></span>
                                </div>
                            </div>

                            <!-- Leverancier -->
                            <div class="col-md-6 mb-3">
                                <label for="leverancier" class="form-label">
                                    <i class="bi bi-building me-1"></i>Leverancier *
                                </label>
                                <select class="form-select" id="leverancier" name="leverancier_id" required>
                                    <option value="">Kies een leverancier...</option>
                                    <?php if (isset($data['leveranciers']) && !empty($data['leveranciers'])): ?>
                                        <?php foreach ($data['leveranciers'] as $leverancier): ?>
                                            <option value="<?= $leverancier->LeverancierID; ?>"
                                                    <?= (isset($data['formData']['leverancier_id']) && $data['formData']['leverancier_id'] == $leverancier->LeverancierID) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($leverancier->Bedrijfsnaam); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Aantal in voorraad -->
                            <div class="col-md-6 mb-3">
                                <label for="voorraad" class="form-label">
                                    <i class="bi bi-boxes me-1"></i>Aantal in voorraad *
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="voorraad" 
                                       name="aantal_voorraad" 
                                       placeholder="0"
                                       min="0"
                                       value="<?= isset($data['formData']['aantal_voorraad']) ? htmlspecialchars($data['formData']['aantal_voorraad']) : ''; ?>"
                                       required>
                            </div>

                            <!-- Allergie (optioneel) -->
                            <div class="col-md-6 mb-3">
                                <label for="allergie" class="form-label">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Allergie (optioneel)
                                </label>
                                <select class="form-select" id="allergie" name="allergie_id">
                                    <option value="">Geen allergie</option>
                                    <?php if (isset($data['allergieën']) && !empty($data['allergieën'])): ?>
                                        <?php foreach ($data['allergieën'] as $allergie): ?>
                                            <option value="<?= $allergie->AllergieID; ?>"
                                                    <?= (isset($data['formData']['allergie_id']) && $data['formData']['allergie_id'] == $allergie->AllergieID) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($allergie->Naam); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="<?= URLROOT; ?>/magazijnvoorraad" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Annuleren
                                    </a>
                                    <button type="submit" class="btn btn-success" id="submitBtn">
                                        <i class="bi bi-check-circle me-1"></i>Product Opslaan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help info -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="text-muted mb-2">
                        <i class="bi bi-info-circle me-1"></i>Hulp bij invullen
                    </h6>
                    <small class="text-muted">
                        <strong>EAN-code:</strong> Wordt automatisch gegenereerd - uniek en kan niet worden gewijzigd.<br>
                        <strong>Productnaam:</strong> Geef een duidelijke naam - het systeem controleert of de categorie past.<br>
                        <strong>Categorie:</strong> Kies de juiste categorie - verkeerde keuzes worden geweigerd.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>

<style>
.container-fluid {
    max-width: 1200px;
}

.card {
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.form-control, .form-select {
    border-radius: 6px;
}

.btn {
    border-radius: 6px;
}

.subtitle {
    font-size: 0.875rem;
    color: #6c757d;
}

#categorieWarning {
    font-size: 0.875rem;
}

/* Styling voor readonly EAN veld */
.form-control.bg-light[readonly] {
    background-color: #f8f9fa !important;
    border-color: #dee2e6;
    color: #6c757d;
    cursor: not-allowed;
}
</style>

<script>
// EAN Code Generator
function generateEANCode() {
    // Genereer 12 willekeurige cijfers
    let ean12 = '';
    for (let i = 0; i < 12; i++) {
        ean12 += Math.floor(Math.random() * 10);
    }
    
    // Bereken check digit
    let sum = 0;
    for (let i = 0; i < 12; i++) {
        sum += parseInt(ean12[i]) * (i % 2 === 0 ? 1 : 3);
    }
    let checkDigit = (10 - (sum % 10)) % 10;
    
    return ean12 + checkDigit;
}

// Categorie validatie op basis van productnaam (alleen visuele waarschuwing)
function validateCategory() {
    const productName = document.getElementById('productnaam').value.toLowerCase();
    const categorySelect = document.getElementById('categorie');
    const selectedCategory = categorySelect.options[categorySelect.selectedIndex];
    const warningDiv = document.getElementById('categorieWarning');
    const warningText = document.getElementById('categorieWarningText');
    
    if (!productName || !selectedCategory.value) {
        warningDiv.style.display = 'none';
        return;
    }
    
    const categoryName = selectedCategory.getAttribute('data-naam');
    
    // Definieer logische categorieën en hun trefwoorden
    const categoryKeywords = {
        'aardappelen, groente, fruit': ['aardappel', 'wortel', 'ui', 'tomaat', 'sla', 'paprika', 'courgette', 'broccoli', 'spinazie', 'appel', 'banaan', 'sinaasappel', 'peer', 'druif', 'aardbei', 'kiwi', 'mango', 'ananas', 'fruit', 'groente'],
        'kaas, vleeswaren': ['kaas', 'ham', 'worst', 'salami', 'vleeswaren'],
        'zuivel, plantaardig en eieren': ['melk', 'yoghurt', 'boter', 'room', 'kwark', 'karnemelk', 'zuivel', 'eieren'],
        'bakkerij en banket': ['brood', 'croissant', 'cake', 'taart', 'koek', 'banket'],
        'frisdrank, sappen, koffie en thee': ['sap', 'water', 'frisdrank', 'thee', 'koffie', 'bier', 'wijn', 'drank'],
        'pasta, rijst en wereldkeuken': ['pasta', 'rijst', 'muesli', 'havermout', 'couscous', 'quinoa', 'graan', 'spaghetti'],
        'soepen, sauzen, kruiden en olie': ['soep', 'saus', 'kruiden', 'olie', 'azijn'],
        'snoep, koek, chips en chocolade': ['snoep', 'chocolade', 'chips', 'koek'],
        'baby, verzorging en hygiëne': ['baby', 'luier', 'verzorging', 'shampoo']
    };
    
    // Controleer of productnaam past bij gekozen categorie
    let isCorrectCategory = false;
    let suggestedCategories = [];
    
    if (categoryKeywords[categoryName]) {
        isCorrectCategory = categoryKeywords[categoryName].some(keyword => 
            productName.includes(keyword)
        );
    }
    
    // Zoek betere categorieën
    for (const [cat, keywords] of Object.entries(categoryKeywords)) {
        if (keywords.some(keyword => productName.includes(keyword))) {
            suggestedCategories.push(cat);
        }
    }
    
    if (!isCorrectCategory && suggestedCategories.length > 0) {
        warningText.textContent = `Dit product lijkt beter te passen bij: ${suggestedCategories.join(', ')}`;
        warningDiv.style.display = 'block';
    } else {
        warningDiv.style.display = 'none';
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Genereer automatisch een EAN bij het laden (alleen als nog niet ingevuld)
    const eanField = document.getElementById('ean');
    if (!eanField.value) {
        eanField.value = generateEANCode();
    }
    
    // Categorie validatie bij wijzigen (alleen visueel)
    document.getElementById('productnaam').addEventListener('input', validateCategory);
    document.getElementById('categorie').addEventListener('change', validateCategory);
    
    // Form submit validatie
    document.getElementById('productForm').addEventListener('submit', function(e) {
        // Controleer of EAN is ingevuld
        if (!document.getElementById('ean').value) {
            alert('Er is geen EAN-code gegenereerd. Herlaad de pagina en probeer opnieuw.');
            e.preventDefault();
            return false;
        }
    });
});
</script>