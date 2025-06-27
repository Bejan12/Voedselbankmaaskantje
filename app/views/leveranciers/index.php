<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <h1 class="title"><?php echo $data['title']; ?></h1>

    <?php SessionHelper::flash('leverancier_message'); ?>

    <a href="/leveranciers/nieuw" class="btn-add">Leverancier toevoegen</a>

    <div class="table-filter-bar" style="text-align: right; margin-bottom: 10px;">
        <form method="get" class="filter-form" style="display: inline-block;">
            <label for="sort">Sorteer op:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="eerstvolgende" <?php echo (isset($data['sort']) && $data['sort'] === 'eerstvolgende') ? 'selected' : ''; ?>>Eerstvolgende levering</option>
                <option value="recent" <?php echo (isset($data['sort']) && $data['sort'] === 'recent') ? 'selected' : ''; ?>>Meest recent eerst</option>
            </select>
        </form>
    </div>

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
                    <th style="width: 90px;">Acties</th>
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
                            <td>
                                <div class="action-icons">
                                    <a href="<?php echo URLROOT; ?>/leveranciers/edit/<?php echo $leverancier->LeverancierID; ?>" title="Wijzig" class="icon-link">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?php echo URLROOT; ?>/leveranciers/delete/<?php echo $leverancier->LeverancierID; ?>" title="Verwijder" class="icon-link text-danger" onclick="return confirm('Weet je zeker dat je deze leverancier wilt verwijderen?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<br><br><br>

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
        vertical-align: middle;
    }

    .styled-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .text-center {
        text-align: center;
    }

    .action-icons {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: flex-start;
        height: 100%;
    }

    .icon-link {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        font-size: 1.2em;
        color: inherit;
        text-decoration: none;
        border-radius: 4px;
        height: 100%;
        min-width: 30px;
    }

    .icon-link:hover {
        background-color: #f0f0f0;
        text-decoration: none;
        color: #495057;
    }

    .bi {
        font-size: 1.2em;
    }
</style>

<!-- Zorg dat je dit hebt in je <head> als het nog niet in je layout/header zit -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"> -->

<?php require APPROOT . '/views/includes/footer.php'; ?>
