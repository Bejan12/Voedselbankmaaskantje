<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - <?php echo SITENAME; ?></title>
    <style>
        body {
            background-color: #fff4f1;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 450px;
            margin: 80px auto;
            background-color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            animation: fadeIn 0.6s ease;
        }

        .form-header {
            background-color: #ff7a00;
            color: white;
            padding: 30px;
            text-align: center;
            font-size: 1.6rem;
            font-weight: bold;
        }

        .form-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            transition: border 0.3s, box-shadow 0.3s;
        }

        input:focus {
            border-color: #ff7a00;
            box-shadow: 0 0 8px rgba(255, 122, 0, 0.4);
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #ff7a00;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn:hover {
            background-color: #e86c00;
            transform: scale(1.02);
        }

        .form-footer {
            margin-top: 20px;
            text-align: center;
        }

        .form-footer a {
            color: #ff7a00;
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .error, .success {
            text-align: center;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="form-container">
        <div class="form-header">
            Inloggen
        </div>
        <div class="form-body">

            <?php if (!empty($data['error'])): ?>
                <div class="error"><?php echo $data['error']; ?></div>
            <?php endif; ?>

            <?php if (!empty($data['success'])): ?>
                <div class="success"><?php echo $data['success']; ?></div>
            <?php endif; ?>

            <form action="<?php echo URLROOT; ?>accounts/login" method="POST">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo $data['email']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="wachtwoord">Wachtwoord *</label>
                    <input type="password" id="wachtwoord" name="wachtwoord" required>
                </div>

                <button type="submit" class="btn">Inloggen</button>
            </form>

            <div class="form-footer">
                <p><a href="<?php echo URLROOT; ?>accounts/register">Nog geen account? Registreren</a></p>
            </div>
        </div>
    </div>

</body>
</html>
