<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <h1 class="text-center fw-bold mb-4">Overzicht Klanten</h1>

    <?php 
    // Toon en verwijder flash-berichten direct uit de sessie
    if (isset($_SESSION['melding'])): ?>
        <div class="alert alert-success" id="success-alert">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_SESSION['melding']); ?>
        </div>
        <?php unset($_SESSION['melding']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['foutmelding'])): ?>
        <div class="alert alert-danger" id="error-alert">
            <?= htmlspecialchars($_SESSION['foutmelding']); ?>
        </div>
        <?php unset($_SESSION['foutmelding']); ?>
    <?php endif; ?>

    <div class="table-container">
        <div class="header-container">
            <a href="<?= URLROOT; ?>klanten/add" class="btn-add">Klant toevoegen</a>
        </div>

        <?php if (empty($data['klanten'])): ?>
            <div class="alert alert-warning">Er zijn nog geen klanten beschikbaar.</div>
        <?php else: ?>
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
        <?php endif; ?>
    </div>
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
