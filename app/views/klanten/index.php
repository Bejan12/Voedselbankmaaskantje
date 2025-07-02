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

    .alert-warning {
        color: #856404;
        background-color: #fff3cd;
        border-color: #ffeaa7;
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
        min-width: 1000px;
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

    .text-danger {
        color: #dc3545 !important;
    }

    .bi {
        font-size: 1.2em;
    }
</style>

<div class="container mt-4">
    <h1 class="title"><?= $data['title']; ?></h1>

    <?php if (!empty($data['melding'])): ?>
        <div class="alert alert-success" id="success-alert"><?= $data['melding']; ?></div>
    <?php endif; ?>
    <?php if (!empty($data['foutmelding'])): ?>
        <div class="alert alert-danger" id="error-alert"><?= $data['foutmelding']; ?></div>
    <?php endif; ?>

    <a href="<?= URLROOT; ?>/klanten/add" class="btn-add">Klant toevoegen</a>

    <?php if (empty($data['klanten'])): ?>
        <div class="alert alert-warning">Er zijn nog geen klanten beschikbaar.</div>
        <script>
            setTimeout(function() {
                window.location.href = "<?= URLROOT; ?>homepages/index";
            }, 3000);
        </script>
    <?php else: ?>
        <div class="table-container">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>E-mailadres</th>
                        <th>Telefoonnummer</th>
                        <th>Aantal Volwassenen</th>
                        <th>Aantal Kinderen</th>
                        <th>Aantal Baby's</th>
                        <th style="width: 150px;">Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['klanten'] as $klant): ?>
                        <tr>
                            <td><?= htmlspecialchars($klant->Voornaam . ' ' . $klant->Achternaam); ?></td>
                            <td><?= htmlspecialchars($klant->Email); ?></td>
                            <td><?= htmlspecialchars($klant->Telefoon); ?></td>
                            <td><?= htmlspecialchars($klant->AantalVolwassenen); ?></td>
                            <td><?= htmlspecialchars($klant->AantalKinderen); ?></td>
                            <td><?= htmlspecialchars($klant->AantalBabys); ?></td>
                            <td>
                                <div class="action-icons">
                                    <a href="<?= URLROOT; ?>klanten/edit/<?= $klant->KlantID; ?>" title="Wijzig" class="icon-link">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?= URLROOT; ?>klanten/pakketten/<?= $klant->KlantID; ?>" title="Bekijk pakketten" class="icon-link" style="color:#7386FF;">
                                        <i class="bi bi-box-seam"></i>
                                    </a>
                                    <a href="<?= URLROOT; ?>klanten/verwijder/<?= $klant->KlantID; ?>" title="Verwijder" class="icon-link text-danger" onclick="return bevestigDelete();">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    // Auto-hide success and error messages after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const successAlert = document.getElementById('success-alert');
        const errorAlert = document.getElementById('error-alert');
        
        if (successAlert) {
            setTimeout(function() {
                successAlert.style.transition = 'opacity 0.5s ease-out';
                successAlert.style.opacity = '0';
                setTimeout(function() {
                    successAlert.style.display = 'none';
                }, 500);
            }, 3000);
        }
        
        if (errorAlert) {
            setTimeout(function() {
                errorAlert.style.transition = 'opacity 0.5s ease-out';
                errorAlert.style.opacity = '0';
                setTimeout(function() {
                    errorAlert.style.display = 'none';
                }, 500);
            }, 3000);
        }
    });

    function bevestigDelete() {
        return confirm('Weet je zeker dat je deze klant wilt verwijderen?');
    }
</script>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
