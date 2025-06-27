<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <!-- Header sectie -->
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-dark mb-1">
                        <i class="bi bi-boxes me-2 text-primary"></i><?php echo $data['title']; ?>
                    </h2>
                    <p class="subtitle mb-0">Beheer en monitor de magazijnvoorraad</p>
                </div>
                <div>
                    <a href="<?= URLROOT; ?>/magazijnvoorraad/nieuwProduct" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i>Nieuw Product
                    </a>
                </div>
            </div>

            <!-- Zoek panel -->
            <div class="card mb-3">
                <div class="card-body">
                    <!-- Tab navigatie -->
                    <ul class="nav nav-tabs" id="zoekTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="ean-tab" data-bs-toggle="tab" data-bs-target="#ean-pane" type="button" role="tab">
                                <i class="bi bi-search me-1"></i>Zoek op EAN-code
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="dropdown-tab" data-bs-toggle="tab" data-bs-target="#dropdown-pane" type="button" role="tab">
                                <i class="bi bi-list me-1"></i>Kies Product
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content mt-3" id="zoekTabContent">
                        <!-- EAN zoeken (nu eerst en actief) -->
                        <div class="tab-pane fade show active" id="ean-pane" role="tabpanel">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <form action="<?= URLROOT; ?>/magazijnvoorraad/zoekEAN" method="POST">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input type="text" name="ean" class="form-control" 
                                                   placeholder="Voer EAN-code in (bijv. 8711200012345)..." 
                                                   pattern="[0-9]{13}" 
                                                   title="EAN-code moet 13 cijfers zijn"
                                                   value="<?= isset($data['zoekterm']) ? htmlspecialchars($data['zoekterm']) : ''; ?>" 
                                                   required>
                                            <button type="submit" class="btn btn-primary">Zoeken</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4 text-end mt-2 mt-md-0">
                                    <small class="text-muted">Voer een 13-cijferige EAN-code in</small>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown zoeken (nu tweede) -->
                        <div class="tab-pane fade" id="dropdown-pane" role="tabpanel">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <form action="<?= URLROOT; ?>/magazijnvoorraad/zoekProduct" method="POST">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-list"></i>
                                            </span>
                                            <select name="product_id" class="form-select" required>
                                                <option value="">Kies een product...</option>
                                                <?php if (isset($data['alleProducten']) && !empty($data['alleProducten'])): ?>
                                                    <?php foreach ($data['alleProducten'] as $product): ?>
                                                        <option value="<?= $product->ProductID; ?>" 
                                                                <?= (isset($data['geselecteerdProduct']) && $data['geselecteerdProduct'] == $product->ProductID) ? 'selected' : ''; ?>>
                                                            <?= htmlspecialchars($product->ProductNaam); ?> (EAN: <?= htmlspecialchars($product->EAN); ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <button type="submit" class="btn btn-primary">Zoeken</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4 text-end mt-2 mt-md-0">
                                    <small class="text-muted">Kies een product uit de lijst</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alleen terug knop als er gezocht is -->
                    <?php if (isset($data['geselecteerdProduct']) || isset($data['zoekterm'])): ?>
                        <div class="row mt-3">
                            <div class="col-12 text-end">
                                <a href="<?= URLROOT; ?>/magazijnvoorraad" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Alle Producten
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
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

            <?php if (isset($data['geselecteerdProduct'])): ?>
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle me-2"></i>Resultaat voor geselecteerd product
                </div>
            <?php endif; ?>

            <?php if (isset($data['zoekterm'])): ?>
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle me-2"></i>Zoekresultaat voor EAN-code: <code><?= htmlspecialchars($data['zoekterm']); ?></code>
                </div>
            <?php endif; ?>

            <!-- Rest van je bestaande code blijft hetzelfde... -->
            <!-- Voorraad overzicht -->
            <?php if ($data['heeftGegevens']): ?>
                <!-- Compacte statistieken -->
                <div class="row mb-3">
                    <?php 
                    $totaalProducten = count($data['voorraadGegevens']);
                    $laagVoorraad = 0;
                    $uitverkocht = 0;
                    $totaalVoorraad = 0;
                    
                    foreach ($data['voorraadGegevens'] as $product) {
                        $totaalVoorraad += $product->AantalInVoorraad;
                        if ($product->AantalInVoorraad <= 0) $uitverkocht++;
                        elseif ($product->AantalInVoorraad <= 5) $laagVoorraad++;
                    }
                    ?>
                    
                    <div class="col-md-3 mb-2">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <div class="stats-label">Totaal Producten</div>
                                <div class="stats-number"><?= $totaalProducten; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-2">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <div class="stats-label">Totale Voorraad</div>
                                <div class="stats-number"><?= number_format($totaalVoorraad); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-2">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <div class="stats-label">Lage Voorraad</div>
                                <div class="stats-number text-warning"><?= $laagVoorraad; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-2">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <div class="stats-label">Uitverkocht</div>
                                <div class="stats-number text-danger"><?= $uitverkocht; ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Producten tabel -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-table me-2"></i>Voorraad Details
                            </h6>
                            <span class="badge bg-primary">
                                <?= $totaalProducten; ?> product<?= $totaalProducten !== 1 ? 'en' : ''; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>EAN-Code</th>
                                        <th>Categorie</th>
                                        <th class="text-center">Voorraad</th>
                                        <th>Leverancier</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['voorraadGegevens'] as $product): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="product-icon rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="bi bi-box text-muted"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium"><?= htmlspecialchars($product->ProductNaam); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code class="bg-light px-2 py-1 rounded"><?= htmlspecialchars($product->EAN); ?></code>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?= htmlspecialchars($product->Categorie); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold <?= $product->AantalInVoorraad <= 0 ? 'text-danger' : ($product->AantalInVoorraad <= 5 ? 'text-warning' : 'text-success'); ?>">
                                                    <?= number_format($product->AantalInVoorraad); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="bi bi-building me-1"></i><?= htmlspecialchars($product->Leverancier); ?>
                                                </small>
                            </td>
                            <td class="text-center">
                                                <?php if ($product->AantalInVoorraad <= 0): ?>
                                                    <span class="badge bg-danger status-badge">
                                                        Uitverkocht
                                                    </span>
                                                <?php elseif ($product->AantalInVoorraad <= 5): ?>
                                                    <span class="badge bg-warning status-badge">
                                                        Kritiek
                                                    </span>
                                                <?php elseif ($product->AantalInVoorraad <= 10): ?>
                                                    <span class="badge bg-info status-badge">
                                                        Laag
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-success status-badge">
                                                        Goed
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Geen data beschikbaar -->
                <div class="card">
                    <div class="card-body text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                        </div>
                        <h5 class="text-muted mb-2">Geen voorraadgegevens beschikbaar</h5>
                        <p class="text-muted small mb-3">
                            <?php if (isset($data['geselecteerdProduct'])): ?>
                                Er zijn geen gegevens gevonden voor het geselecteerde product.
                            <?php elseif (isset($data['zoekterm'])): ?>
                                Er zijn geen producten gevonden met EAN-code: <code><?= htmlspecialchars($data['zoekterm']); ?></code>
                            <?php else: ?>
                                Er zijn momenteel geen producten in de voorraad geregistreerd.
                            <?php endif; ?>
                        </p>
                        <?php if (isset($data['geselecteerdProduct']) || isset($data['zoekterm'])): ?>
                            <a href="<?= URLROOT; ?>/magazijnvoorraad" class="btn btn-primary">
                                <i class="bi bi-arrow-left me-1"></i>Terug naar overzicht
                            </a>
                        <?php else: ?>
                            <a href="<?= URLROOT; ?>/homepages" class="btn btn-secondary">
                                <i class="bi bi-house me-1"></i>Terug naar hoofdmenu
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>

<style>
/* Compacte en subtiele styling */
.container-fluid {
    max-width: 1400px;
}

.card {
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.6em;
}

.btn {
    border-radius: 6px;
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.btn-lg {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}

.table th {
    font-size: 0.875rem;
    padding: 0.75rem;
    background-color: #495057;
    color: white;
    border: none;
}

.table td {
    padding: 0.75rem;
    font-size: 0.875rem;
    vertical-align: middle;
}

.stats-card {
    background: white;
    border: 1px solid #dee2e6;
    color: #495057;
}

.stats-card .card-body {
    padding: 1rem;
}

.stats-number {
    font-size: 1.5rem;
    font-weight: 600;
    color: #212529;
}

.stats-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.product-icon {
    width: 32px;
    height: 32px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

.status-badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.alert {
    font-size: 0.875rem;
    padding: 0.75rem 1rem;
}

h2 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.subtitle {
    font-size: 0.875rem;
    color: #6c757d;
}

.nav-tabs .nav-link {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
}
</style>