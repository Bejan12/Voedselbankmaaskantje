<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <h1><?php echo $data['title']; ?></h1>
    
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Bedrijfsnaam</th>
                            <th>Adres</th>
                            <th>Contactpersoon</th>
                            <th>E-mail</th>
                            <th>Telefoon</th>
                            <th>Eerstvolgende levering</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['leveranciers'])): ?>
                            <?php foreach($data['leveranciers'] as $leverancier): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($leverancier->Bedrijfsnaam); ?></td>
                                    <td><?php echo htmlspecialchars($leverancier->Adres ?? 'Niet opgegeven'); ?></td>
                                    <td><?php echo htmlspecialchars($leverancier->ContactNaam ?? 'Niet opgegeven'); ?></td>
                                    <td><?php echo htmlspecialchars($leverancier->ContactEmail ?? 'Niet opgegeven'); ?></td>
                                    <td><?php echo htmlspecialchars($leverancier->ContactTelefoon ?? 'Niet opgegeven'); ?></td>
                                    <td><?php echo $leverancier->EerstvolgendeLevering ?? 'Niet gepland'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Geen leveranciers gevonden</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
