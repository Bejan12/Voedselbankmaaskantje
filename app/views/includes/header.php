<?php
// Initialize session only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overzicht Voedselpakket</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;700&display=swap');

        body {
            font-family: 'Instrument Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .navbar-custom {
            background-color: #14193B;
            padding: 0 30px;
            height: 80px;
        }

        .navbar img.logo {
            height: 60px;
            margin-right: 40px;
        }

        .nav-link, .navbar-brand {
            color: #CECECE !important;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.3s;
        }

        .nav-link:hover, .navbar-brand:hover {
            color: #ffffff !important;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin: 10px 5px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .dropdown-menu {
            min-width: 200px;
        }

        /* Styling for user dropdown */
        .navbar-nav .dropdown-toggle::after {
            display: none; /* Hide default dropdown arrow */
        }

        .dropdown-item-text {
            padding: 0.5rem 1rem;
            font-weight: 500;
            color: #333;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo URLROOT; ?>/homepages/index">
            <img src="/img/voedselbank_logo.png" alt="Voedselbank Logo" class="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URLROOT; ?>/homepages/index">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Overzicht accounts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/leveranciers/index.php">Overzicht Leveranciers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Overzicht Voorraadbeheer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Overzicht voedselpakket</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Overzicht klanten</a>
                </li>
            </ul>
            <?php if (empty($_SESSION['user_id'])): ?>
                <div class="d-flex">
                    <a href="<?php echo URLROOT; ?>/accounts/register" class="btn">Registreren</a>
                    <a href="<?php echo URLROOT; ?>/accounts/login" class="btn">Inloggen</a>
                </div>
            <?php else: ?>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle" style="font-size: 1.5rem; margin-right: 8px;"></i>
                            <span class="d-none d-md-inline">Account</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <span class="dropdown-item-text">
                                    <?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo URLROOT; ?>/accounts/logout">Uitloggen</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>
<!-- Bootstrap 5 JS (for dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
