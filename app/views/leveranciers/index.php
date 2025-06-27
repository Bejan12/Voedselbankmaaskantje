<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <h1 class="title"><?php echo $data['title']; ?></h1>

    <a href="/leveranciers/nieuw" class="btn-add">Leverancier toevoegen</a>

    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Leveranciernummer</th>
                    <th>Bedrijfsnaam</th>
                    <th>Adres</th>
                    <th>Naam</th>
                    <th>E-mail</th>
                    <th>Telefoonnummer</th>
                    <th>Eerstvolgende levering (DD/MM/JJJJ)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['leveranciers'])): ?>
                    <?php foreach ($data['leveranciers'] as $leverancier): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($leverancier->LeverancierID ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($leverancier->Bedrijfsnaam ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($leverancier->Adres ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($leverancier->ContactNaam ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($leverancier->ContactEmail ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($leverancier->ContactTelefoon ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($leverancier->EerstvolgendeLevering ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .title {
        text-align: center;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .btn-add {
        display: inline-block;
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-decoration: none;
        font-weight: bold;
    }

    .btn-add:hover {
        background-color: #218838;
        text-decoration: none;
        color: white;
    }

    .table-container {
        overflow-x: auto;
    }

    .styled-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 16px;
        min-width: 800px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .styled-table thead {
        background-color: #7386FF;
        color: white;
    }

    .styled-table th,
    .styled-table td {
        padding: 12px 15px;
        border: 1px solid #dddddd;
        text-align: left;
    }

    .styled-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .text-center {
        text-align: center;
    }
</style>

<?php require APPROOT . '/views/includes/footer.php'; ?>
    }
</style>

<?php require APPROOT . '/views/includes/footer.php'; ?>
