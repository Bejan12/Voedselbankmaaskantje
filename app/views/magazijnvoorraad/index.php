<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container">
    <div class="row mt-3">
        <div class="col-12">
            <h3><i class="bi bi-boxes"></i> <?php echo $data['title']; ?></h3>
            <hr>

            <!-- Zoekformulier -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="<?= URLROOT; ?>/magazijnvoorraad/zoekProduct" method="GET" class="d-flex">
                        <input type="text" name="ean" class="form-control me-2" placeholder="Zoek op EAN-code..." 
                               value="<?= isset($data['zoekterm']) ? htmlspecialchars($data['zoekterm']) : ''; ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Zoeken
                        </button>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <a href="<?= URLROOT; ?>/magazijnvoorraad" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Alle producten tonen
                    </a>
                </div>
            </div>

            <?php if (isset($data['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= $data['error']; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($data['zoekterm'])): ?>
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle"></i> Zoekresultaat voor EAN-code: <?= htmlspecialchars($data['zoekterm']); ?>
                </div>
            <?php endif; ?>

            <?php if ($data['heeftGegevens']): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul"></i> Voorraadoverzicht 
                            <span class="badge bg-primary"><?= count($data['voorraadGegevens']); ?> producten</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Product ID</th>
                                        <th>Productnaam</th>
                                        <th>EAN-code</th>
                                        <th>Categorie</th>
                                        <th>Voorraad</th>
                                        <th>Leverancier</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['voorraadGegevens'] as $product): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($product->ProductID); ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($product->ProductNaam); ?></strong>
                                            </td>
                                            <td>
                                                <code><?= htmlspecialchars($product->EAN); ?></code>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= htmlspecialchars($product->Categorie); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold <?= $product->AantalInVoorraad <= 5 ? 'text-danger' : ($product->AantalInVoorraad <= 10 ? 'text-warning' : 'text-success'); ?>">
                                                    <?= htmlspecialchars($product->AantalInVoorraad); ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($product->Leverancier); ?></td>
                                            <td>
                                                <?php if ($product->AantalInVoorraad <= 0): ?>
                                                    <span class="badge bg-danger">Uitverkocht</span>
                                                <?php elseif ($product->AantalInVoorraad <= 5): ?>
                                                    <span class="badge bg-warning">Laag</span>
                                                <?php elseif ($product->AantalInVoorraad <= 10): ?>
                                                    <span class="badge bg-info">Beperkt</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Voldoende</span>
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
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Geen voorraadgegevens beschikbaar</h4>
                        <p class="text-muted">
                            <?php if (isset($data['zoekterm'])): ?>
                                Er zijn geen producten gevonden met EAN-code: <?= htmlspecialchars($data['zoekterm']); ?>
                            <?php else: ?>
                                Er zijn momenteel geen producten in de voorraad geregistreerd.
                            <?php endif; ?>
                        </p>
                        <?php if (isset($data['zoekterm'])): ?>
                            <a href="<?= URLROOT; ?>/magazijnvoorraad" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Terug naar overzicht
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>