<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren - <?php echo SITENAME; ?></title>
    
    <style>
        body {
            background: #fff4f1;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 60px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.8s ease-in-out;
        }

        h1 {
            text-align: center;
            color: #ff7a00;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border-radius: 0.75rem;
            border: 1px solid #ccc;
            transition: box-shadow 0.3s ease, border 0.3s ease;
            font-size: 1rem;
        }

        input:focus {
            border-color: #ff7a00;
            box-shadow: 0 0 10px rgba(255, 122, 0, 0.4);
            outline: none;
        }

        .btn {
            width: 100%;
            background-color: #ff7a00;
            color: white;
            padding: 12px;
            font-weight: bold;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
            background-color: #e86c00;
            transform: scale(1.03);
        }

        .error {
            color: red;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .success {
            color: green;
            margin-bottom: 10px;
            font-weight: 500;
        }

        p a {
            color: #ff7a00;
            text-decoration: none;
            font-weight: 500;
        }

        p a:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Registreren</h1>

        <?php if (!empty($data['error'])): ?>
            <div class="error"><?php echo $data['error']; ?></div>
        <?php endif; ?>

        <?php if (!empty($data['success'])): ?>
            <div class="success"><?php echo $data['success']; ?></div>
        <?php endif; ?>

        <form action="<?php echo URLROOT; ?>accounts/register" method="POST">
            <div class="form-group">
                <label for="voornaam">Voornaam *</label>
                <input type="text" id="voornaam" name="voornaam" value="<?php echo $data['voornaam']; ?>" required>
            </div>

            <div class="form-group">
                <label for="achternaam">Achternaam *</label>
                <input type="text" id="achternaam" name="achternaam" value="<?php echo $data['achternaam']; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?php echo $data['email']; ?>" required>
            </div>

            <div class="form-group">
                <label for="telefoon">Telefoon</label>
                <input type="tel" id="telefoon" name="telefoon" value="<?php echo $data['telefoon']; ?>">
            </div>

            <div class="form-group">
                <label for="geboortedatum">Geboortedatum</label>
                <input type="date" id="geboortedatum" name="geboortedatum" value="<?php echo $data['geboortedatum']; ?>">
            </div>

            <div class="form-group">
                <label for="gebruikersnaam">Gebruikersnaam *</label>
                <input type="text" id="gebruikersnaam" name="gebruikersnaam" value="<?php echo $data['gebruikersnaam']; ?>" required>
            </div>

            <div class="form-group">
                <label for="wachtwoord">Wachtwoord *</label>
                <input type="password" id="wachtwoord" name="wachtwoord" required>
            </div>

            <button type="submit" class="btn">Registreren</button>
        </form>
        <p class="mt-3"><a href="<?php echo URLROOT; ?>accounts/login">Heb je al een account? Log dan in</a></p>
    </div>

    <script>
        // Extra glow op input-focus
        const fields = document.querySelectorAll('input');
        fields.forEach(input => {
            input.addEventListener('focus', () => {
                input.style.boxShadow = '0 0 10px rgba(255, 122, 0, 0.5)';
            });
            input.addEventListener('blur', () => {
                input.style.boxShadow = 'none';
            });
        });
    </script>

</body>
</html>
