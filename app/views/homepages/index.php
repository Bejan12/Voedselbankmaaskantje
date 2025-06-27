<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="display-4 text-primary mb-3"><?php echo $data['title']; ?></h1>
                <p class="lead text-muted">Beheerssysteem Voedselbank Maaskantje</p>
            </div>
            
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-4">
                    <h3 class="mb-0 text-center">
                        <i class="bi bi-house-heart me-2"></i>Welkom bij Voedselbank Maaskantje
                    </h3>
                </div>
                <div class="card-body p-5">
                    <p class="text-center text-muted mb-5">
                        Kies een van de onderstaande opties om het systeem te gebruiken
                    </p>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="mb-4">
                                        <i class="bi bi-boxes display-3 text-primary"></i>
                                    </div>
                                    <h4 class="card-title text-primary mb-3">Magazijnvoorraad</h4>
                                    <p class="card-text text-muted mb-4">
                                        Bekijk en beheer het overzicht van alle producten in voorraad. 
                                        Monitor voorraadniveaus en zoek specifieke producten.
                                    </p>
                                    <a href="<?= URLROOT; ?>/magazijnvoorraad" class="btn btn-primary btn-lg w-100">
                                        <i class="bi bi-eye me-2"></i>Voorraad Bekijken
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="mb-4">
                                        <i class="bi bi-people display-3 text-success"></i>
                                    </div>
                                    <h4 class="card-title text-success mb-3">Klantenbeheer</h4>
                                    <p class="card-text text-muted mb-4">
                                        Beheer klantgegevens en hun specifieke wensen en allergieën. 
                                        Stel voedselpakketten samen op maat.
                                    </p>
                                    <button class="btn btn-outline-success btn-lg w-100" disabled>
                                        <i class="bi bi-person-plus me-2"></i>Binnenkort Beschikbaar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="mb-4">
                                        <i class="bi bi-basket display-3 text-warning"></i>
                                    </div>
                                    <h4 class="card-title text-warning mb-3">Voedselpakketten</h4>
                                    <p class="card-text text-muted mb-4">
                                        Werk aan het samenstellen en beheren van voedselpakketten voor klanten.
                                    </p>
                                    <a href="<?= URLROOT; ?>/voedselpakketten" class="btn btn-warning btn-lg w-100">
                                        <i class="bi bi-basket2 me-2"></i>Naar Voedselpakketten
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="mb-4">
                                        <i class="bi bi-truck display-3 text-info"></i>
                                    </div>
                                    <h4 class="card-title text-info mb-3">Leveranciers</h4>
                                    <p class="card-text text-muted mb-4">
                                        Beheer leveranciersinformatie en contactgegevens. 
                                        Voeg nieuwe leveranciers toe en bewerk bestaande gegevens.
                                    </p>
                                    <a href="<?= URLROOT; ?>/leveranciers" class="btn btn-info btn-lg w-100">
                                        <i class="bi bi-building me-2"></i>Leveranciers Beheren
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php require APPROOT . '/views/includes/footer.php'; ?>
