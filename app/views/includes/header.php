<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overzicht Voedselpakket</title>
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
            height: 220px;
            margin-right: 40px;
            position: relative;
            top: 50%;
            left: 1%;
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            color: #CECECE;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #ffffff;
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
    <div class="navbar">
        <img src="/img/voedselbank_logo.png" alt="Voedselbank Logo" class="logo">
        <div class="nav-links">
            <a href="#">Dashboard</a>
            <a href="#">Overzicht accounts</a>
            <a href="#">Overzicht Leveranciers</a>
            <a href="#">Overzicht Voorraadbeheer</a>
            <a href="#">Overzicht voedselpakket</a>
            <a href="<?= URLROOT; ?>klanten/index">Overzicht klanten</a>
            <a href="<?= URLROOT; ?>klanten/afgerondepakketten">Afgeronde voedselpakketten</a>
        </div>
    </div>

</body>
</html>
