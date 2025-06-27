<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <!-- Welkom bericht of foutmelding -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <?php if (!empty($data['gebruikerError'])): ?>
                        <div class="alert alert-danger mb-0">
                            <?= htmlspecialchars($data['gebruikerError']) ?>
                        </div>
                    <?php elseif (!empty($data['gebruiker'])): ?>
                        <h2 class="card-title">Welkom <?= htmlspecialchars($data['gebruiker']->Voornaam . ' ' . $data['gebruiker']->Achternaam) ?></h2>
                        <p class="card-text">U bent ingelogd als <?= htmlspecialchars($data['gebruiker']->Rol) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Toegankelijke functies -->
    <?php if (empty($data['gebruikerError'])): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Beschikbare functies</h3>
                    <div class="row">
                        <?php foreach ($data['functies'] as $functie): ?>
                            <div class="col-md-4 mb-3">
                                <div class="d-grid">
                                    <a href="#" class="btn btn-primary btn-lg">
                                        <?= htmlspecialchars($functie->Rol) ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Voorraad overzicht -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Actuele Voorraad</h3>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Categorie</th>
                                    <th>Voorraad</th>
                                    <th>Leverancier</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['voorraad'] as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product->ProductNaam) ?></td>
                                        <td><?= htmlspecialchars($product->Categorie) ?></td>
                                        <td><?= htmlspecialchars($product->AantalInVoorraad) ?></td>
                                        <td><?= htmlspecialchars($product->Leverancier) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Voedselpakketten -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Voedselpakketten</h3>
                    <!-- Filter form -->
                    <form action="<?= URLROOT ?>/homepages/filterPakketten" method="post" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="date" name="filter" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Filter op datum</button>
                                <?php if (isset($_POST['filter'])): ?>
                                    <a href="<?= URLROOT ?>/homepages/index" class="btn btn-secondary">Reset filter</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                    <?php if (!empty($data['filterMessage'])): ?>
                        <div class="alert alert-info">
                            <?= htmlspecialchars($data['filterMessage']) ?>
                        </div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Pakket ID</th>
                                    <th>Klant</th>
                                    <th>Datum Samenstelling</th>
                                    <th>Datum Uitgifte</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['voedselpakketten'] as $pakket): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($pakket->VoedselpakketID) ?></td>
                                        <td><?= htmlspecialchars($pakket->KlantNaam) ?></td>
                                        <td><?= htmlspecialchars($pakket->DatumSamenstelling) ?></td>
                                        <td><?= htmlspecialchars($pakket->DatumUitgifte) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>