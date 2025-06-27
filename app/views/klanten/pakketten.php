<?php require_once APPROOT . '/views/includes/header.php'; ?>

<style>
    .klanten-container {
        margin-top: 120px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(20,25,59,0.08);
        padding: 32px 32px 24px 32px;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
    }
    .pakket-card {
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(20,25,59,0.08);
        margin-bottom: 24px;
        border: 1px solid #FF7F32;
    }
    .pakket-card .card-header {
        background-color: #FF7F32;
        color: #fff;
        font-weight: 600;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        font-size: 1.1rem;
    }
    .pakket-card .card-body {
        background: #fafafa;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
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
    .alert-warning, .alert-danger, .alert-success, .alert-info {
        border-radius: 6px;
        font-size: 1rem;
        margin-bottom: 24px;
    }
</style>

<div class="klanten-container">
    <h2>Afgenomen voedselpakketten</h2>
    <?php if (!empty($melding)): ?>
        <div class="alert alert-warning"><?= $melding ?></div>
        <script>
            setTimeout(function() {
                window.location.href = "<?= URLROOT; ?>klanten/afgerondepakketten";
            }, 3000);
        </script>
    <?php elseif (empty($pakketten)): ?>
        <div class="alert alert-info">Deze klant heeft nog geen voedselpakketten ontvangen.</div>
        <script>
            setTimeout(function() {
                window.location.href = "<?= URLROOT; ?>klanten/afgerondepakketten";
            }, 3000);
        </script>
    <?php else: ?>
        <?php foreach ($pakketten as $pakket): ?>
            <div class="card pakket-card">
                <div class="card-header">
                    Datum uitgifte: <?= htmlspecialchars($pakket->DatumUitgifte) ?>
                </div>
                <div class="card-body">
                    <strong>Producten:</strong>
                    <ul>
                        <?php foreach (explode(',', $pakket->producten) as $product): ?>
                            <li><?= htmlspecialchars(trim($product)) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
        <a href="<?= URLROOT; ?>klanten/afgerondepakketten" class="btn btn-orange mt-3">Terug naar overzicht</a>
    <?php endif; ?>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
