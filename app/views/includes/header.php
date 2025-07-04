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
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;700&display=swap');

        body {
            font-family: 'Instrument Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background-color: #14193B;
            padding: 0 30px;
            height: 80px;
            position: relative;
        }

        .navbar img.logo {
            height: 220px;
            margin-right: 40px;
            position: relative;
            top: 70px;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 0 0 15px 15px;
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

        .navbar-brand img,
        .logo {
            max-width: 120px !important;
            max-height: 60px !important;
            width: auto;
            height: auto;
        }

        /* Responsive navbar styling */
        .navbar-toggler {
            border: none;
            padding: 4px 8px;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28206, 206, 206, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        @media (max-width: 991px) {
            .navbar-custom {
                padding: 0 15px;
                height: auto;
                min-height: 60px;
            }

            .navbar-collapse {
                margin-top: 10px;
            }

            .nav-link {
                padding: 8px 16px !important;
                border-bottom: 1px solid rgba(206, 206, 206, 0.2);
            }

            .btn {
                width: 100%;
                margin: 5px 0;
                text-align: center;
            }

            .d-flex {
                flex-direction: column !important;
                width: 100%;
                padding: 10px 16px;
            }

            .dropdown-menu {
                position: static !important;
                transform: none !important;
                border: none;
                box-shadow: none;
                background-color: rgba(255, 255, 255, 0.1);
                margin-top: 5px;
            }

            .dropdown-item {
                color: #CECECE !important;
                padding: 8px 16px;
            }

            .dropdown-item:hover {
                background-color: rgba(255, 255, 255, 0.1);
                color: #ffffff !important;
            }

            .dropdown-item-text {
                color: #CECECE !important;
            }
        }

        @media (max-width: 576px) {
            .navbar-custom {
                padding: 0 10px;
            }

            .nav-link {
                font-size: 16px;
                padding: 12px 16px !important;
            }
        }
    </style>
</head>
<body>
<div class="main-wrapper flex-grow-1">
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo URLROOT; ?>homepages/index">
            <span class="d-lg-none">Voedselpakket</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URLROOT; ?>homepages/index">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URLROOT; ?>leveranciers/index">Overzicht Leveranciers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URLROOT; ?>magazijnvoorraad/index">Overzicht Voorraadbeheer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URLROOT; ?>voedselpakketoverzicht/index">Overzicht voedselpakket</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URLROOT; ?>klanten/index">Overzicht klanten</a>
                </li>
            </ul>
            <?php if (empty($_SESSION['user_id'])): ?>
                <div class="d-flex">
                    <a href="<?php echo URLROOT; ?>accounts/register" class="btn">Registreren</a>
                    <a href="<?php echo URLROOT; ?>accounts/login" class="btn">Inloggen</a>
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
                                <a class="dropdown-item text-danger" href="<?php echo URLROOT; ?>accounts/logout">Uitloggen</a>
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