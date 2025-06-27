<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voedselbank Maaskantje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;700&display=swap');

        body {
            font-family: 'Instrument Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .navbar {
            background-color: #14193B;
            padding: 0 30px;
            display: flex;
            align-items: center;
            height: 80px;
        }

        .navbar img.logo {
            height: 60px;
            margin-right: 30px;
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            color: #CECECE;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            transition: color 0.3s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #EE7B00;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                height: auto;
                padding: 10px 0;
            }

            .navbar img.logo {
                margin-bottom: 10px;
            }

            .nav-links {
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }
        }

        .content {
            text-align: center;
            padding: 40px 20px;
        }

        .content h1 {
            font-size: 22px;
            font-weight: 700;
            color: #000;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <img src="<?= URLROOT ?>/public/img/voedselbank_logo.png" alt="Voedselbank Logo" class="logo">
        <div class="nav-links">
            <a href="<?= URLROOT ?>/homepages/index" class="<?= ($_SERVER['REQUEST_URI'] == '/' ? 'active' : '') ?>">Dashboard</a>
            <a href="#">Overzicht accounts</a>
            <a href="#">Overzicht Leveranciers</a>
            <a href="#">Overzicht Voorraadbeheer</a>
            <a href="<?= URLROOT ?>/voedselpakket/index" class="active">Overzicht voedselpakket</a>
            <a href="#">Overzicht klanten</a>
        </div>
    </nav>

</body>
</html>
