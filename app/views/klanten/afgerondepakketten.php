<?php require_once APPROOT . '/views/includes/header.php'; ?>

<style>
    /* Zorg ervoor dat de pagina altijd de volledige hoogte heeft */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    
    /* Container voor de hele pagina inhoud */
    .container {
        min-height: calc(100vh - 200px); /* Trek navbar en footer hoogte af */
        padding-bottom: 40px; /* Extra ruimte voor de footer */
        flex: 1; /* Neemt beschikbare ruimte in */
        margin-top: 20px;
    }

    .title {
        text-align: center;
        font-weight: bold;
        margin-bottom: 30px;
        color: #14193B;
    }

    .table-container {
        overflow-x: auto;
        margin: 0 auto;
    }

    .styled-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
        font-size: 14px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        background: #fff;
    }

    .styled-table thead {
        background-color: #7386FF;
        color: white;
    }

    .styled-table th,
    .styled-table td {
        padding: 12px;
        border: 1px solid #dddddd;
        text-align: left;
        vertical-align: middle;
    }

    .styled-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        text-decoration: none;
        color: white;
    }

    .action-column {
        width: 180px;
        text-align: center;
    }
</style>

<div class="container mt-4">
    <h1 class="title">Afgeronde voedselpakketten per klant</h1>
    
    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>E-mailadres</th>
                    <th>Telefoonnummer</th>
                    <th class="action-column">Actie</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['klanten'] as $klant): ?>
                    <tr>
                        <td><?= htmlspecialchars($klant->Voornaam . ' ' . $klant->Achternaam); ?></td>
                        <td><?= htmlspecialchars($klant->Email); ?></td>
                        <td><?= htmlspecialchars($klant->Telefoon); ?></td>
                        <td class="action-column">
                            <a href="<?= URLROOT; ?>klanten/pakketten/<?= $klant->KlantID; ?>" class="btn-primary">
                                📦 Bekijk pakketten
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
