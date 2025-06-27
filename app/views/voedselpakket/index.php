<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-lg mb-4">
                <div class="card-body">
                    <h2 class="mb-3" style="color:#EE7B00;">Overzicht Voedselpakketten</h2>
                    <a href="<?= URLROOT ?>/voedselpakket/toevoegen" class="btn btn-success mb-3">Voedselpakket toevoegen</a>
                    <form class="row g-2 mb-3" method="get" action="<?= URLROOT ?>/voedselpakket/index">
                        <div class="col-auto">
                            <select name="filter" class="form-select">
                                <option value="">-- Toon alles --</option>
                                <option value="beschikbaar" <?= ($data['filter']==='beschikbaar')?'selected':''; ?>>Beschikbare pakketten</option>
                                <option value="niet_beschikbaar" <?= ($data['filter']==='niet_beschikbaar')?'selected':''; ?>>Niet-beschikbare pakketten</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary" style="background:#EE7B00;border:none;">Filter</button>
                        </div>
                    </form>
                    <?php if($data['melding']): ?>
                        <div class="alert alert-info text-center"> <?= htmlspecialchars($data['melding']) ?> </div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead style="background:#DDD7D7;">
                                <tr>
                                    <th>Pakketnummer</th>
                                    <th>Klantnaam</th>
                                    <th>Email</th>
                                    <th>Datum Samenstelling</th>
                                    <th>Datum Uitgifte</th>
                                    <th>Allergieën/Wensen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totaal = count($data['pakketten']);
                                if($totaal): 
                                    $nummer = $totaal;
                                    foreach($data['pakketten'] as $pakket): ?>
                                    <tr>
                                        <td><?= sprintf('#%03d', $nummer--) ?></td>
                                        <td><?= htmlspecialchars($pakket->KlantNaam) ?></td>
                                        <td><?= htmlspecialchars($pakket->Email) ?></td>
                                        <td><?= date('d-m-Y', strtotime($pakket->DatumSamenstelling)) ?></td>
                                        <td><?= $pakket->DatumUitgifte ? date('d-m-Y', strtotime($pakket->DatumUitgifte)) : '<span class="badge bg-success">Beschikbaar</span>' ?></td>
                                        <td><?= htmlspecialchars($pakket->Allergieen ?: '-') ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="6">Geen voedselpakketten gevonden</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="<?= URLROOT ?>/homepages/index" class="btn btn-outline-secondary mt-3">Terug naar Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
