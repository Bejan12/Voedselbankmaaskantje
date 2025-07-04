<?php require_once APPROOT . '/views/includes/header.php'; ?>

<style>
    .container {
        margin-top: 20px;
        max-width: 900px;
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

    .alert {
        padding: 12px 20px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        font-weight: 500;
    }

    .alert-warning {
        color: #856404;
        background-color: #fff3cd;
        border-color: #ffeaa7;
    }

    .alert-info {
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }

    .pakket-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        border: 1px solid #7386FF;
        overflow: hidden;
    }

    .pakket-header {
        background-color: #7386FF;
        color: white;
        padding: 15px 20px;
        font-weight: 600;
        font-size: 16px;
    }

    .pakket-body {
        padding: 20px;
        background: #fafafa;
    }

    .pakket-body strong {
        color: #14193B;
        display: block;
        margin-bottom: 10px;
    }

    .pakket-body ul {
        margin: 0;
        padding-left: 20px;
    }

    .pakket-body li {
        margin-bottom: 5px;
        color: #333;
    }

    .btn-primary {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
    }

    .btn-primary:hover {
        background-color: #218838;
        text-decoration: none;
        color: white;
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
    <a href="<?= URLROOT; ?>klanten" class="back-link">← Terug naar overzicht</a>
    
    <h1 class="title">Voedselpakketten voor deze klant</h1>
    
    <?php if (!empty($melding)): ?>
        <div class="alert alert-warning"><?= $melding ?></div>
        <script>
            setTimeout(function() {
                window.location.href = "<?= URLROOT; ?>klanten";
            }, 3000);
        </script>
    <?php elseif (empty($pakketten)): ?>
        <div class="alert alert-info">Deze klant heeft nog geen voedselpakketten toegewezen gekregen.</div>
        <script>
            setTimeout(function() {
                window.location.href = "<?= URLROOT; ?>klanten";
            }, 3000);
        </script>
    <?php else: ?>
        <?php foreach ($pakketten as $pakket): ?>
            <div class="pakket-card">
                <div class="pakket-header">
                    <?php if ($pakket->DatumUitgifte): ?>
                        Datum uitgifte: <?= htmlspecialchars($pakket->DatumUitgifte) ?> | Status: <?= htmlspecialchars($pakket->Status) ?>
                    <?php else: ?>
                        Samengesteld op: <?= htmlspecialchars($pakket->DatumSamenstelling) ?> | Status: <?= htmlspecialchars($pakket->Status) ?>
                    <?php endif; ?>
                </div>
                <div class="pakket-body">
                    <strong>Producten:</strong>
                    <ul>
                        <?php if ($pakket->producten): ?>
                            <?php foreach (explode(',', $pakket->producten) as $product): ?>
                                <li><?= htmlspecialchars(trim($product)) ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Geen producten toegevoegd</li>
                        <?php endif; ?>
                    </ul>
                    <?php if (!$pakket->DatumUitgifte): ?>
                        <p><em>Dit pakket is nog niet uitgegeven.</em></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <a href="<?= URLROOT; ?>klanten" class="btn-primary">Terug naar overzicht</a>
    <?php endif; ?>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
