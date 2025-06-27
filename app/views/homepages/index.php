<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container">
    <div class="row mt-3">
        <div class="col-2"></div>
        <div class="col-8">
            <h3><?php echo $data['title']; ?></h3>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="bi bi-house"></i> Voedselbank Maaskantje</h5>
                </div>
                <div class="card-body">
                    <p>Welkom bij het beheerssysteem van Voedselbank Maaskantje.</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-boxes display-4 text-primary"></i>
                                    <h5 class="card-title mt-2">Magazijnvoorraad</h5>
                                    <p class="card-text">Bekijk het overzicht van alle producten in voorraad</p>
                                    <a href="<?= URLROOT; ?>/magazijnvoorraad" class="btn btn-primary">
                                        <i class="bi bi-eye"></i> Overzicht Magazijnvoorraad
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-people display-4 text-success"></i>
                                    <h5 class="card-title mt-2">Klanten</h5>
                                    <p class="card-text">Beheer klantgegevens en hun specifieke wensen</p>
                                    <a href="#" class="btn btn-success disabled">
                                        <i class="bi bi-person-plus"></i> Klanten beheren
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-2"></div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>