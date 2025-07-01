<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <h1 class="title"><?php echo $data['title']; ?></h1>

    <?php 
    // Replace the simple flash with conditional styling
    if (isset($_SESSION['leverancier_message'])) {
        $messageClass = 'alert-success'; // Default to success
        
        // Check if the message contains typical error phrases
        if (strpos($_SESSION['leverancier_message'], 'kan niet') !== false || 
            strpos($_SESSION['leverancier_message'], 'fout') !== false || 
            strpos($_SESSION['leverancier_message'], 'error') !== false || 
            strpos($_SESSION['leverancier_message'], 'niet verwijderd') !== false ||
            strpos($_SESSION['leverancier_message'], 'mislukt') !== false) {
            $messageClass = 'alert-danger';
        }
        
        echo '<div class="alert ' . $messageClass . '">' . $_SESSION['leverancier_message'] . '</div>';
        unset($_SESSION['leverancier_message']);
    }
    ?>

    <a href="/leveranciers/nieuw" class="btn-add">Leverancier toevoegen</a>

    <div class="table-filter-bar" style="text-align: right; margin-bottom: 10px;">
        <form method="get" class="filter-form" style="display: inline-block;">
            <label for="sort">Sorteer op:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="eerstvolgende" <?php echo (isset($data['sort']) && $data['sort'] === 'eerstvolgende') ? 'selected' : ''; ?>>Eerstvolgende levering</option>
                <option value="recent" <?php echo (isset($data['sort']) && $data['sort'] === 'recent') ? 'selected' : ''; ?>>Meest recent toegevoegd</option>
            </select>
        </form>
    </div>

    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Leveranciernummer</th>
                    <th>Bedrijfsnaam</th>
                    <th>Type</th>
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
                            <td><?php echo htmlspecialchars($leverancier->LeverancierNummer ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($leverancier->Bedrijfsnaam ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($leverancier->LeverancierType ?? ''); ?></td>
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

<br><br><br><br><br><br><br>

<style>
    .title {
        text-align: center;
        font-weight: bold;
        margin-bottom: 30px;
    }

    /* Alert styling for flash messages */
    .alert {
        padding: 12px 20px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        font-weight: 500;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
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
        margin: 0 auto;
    }

    .styled-table {
        width: 100%;
        min-width: 1100px;
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
        padding: 8px;
        border: 1px solid #dddddd;
        text-align: left;
        vertical-align: middle;
    }

    .styled-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
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

    .disabled-link {
        cursor: not-allowed;
        opacity: 0.5;
    }

    .disabled-link:hover {
        background-color: transparent !important;
    }
</style>

<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"> -->
<!-- Vergeet niet deze in de layout/head op te nemen -->

<?php require APPROOT . '/views/includes/footer.php'; ?>
