<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - <?php echo SITENAME; ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;
        }
        .error { color: red; margin-top: 5px; }
        .success { color: green; margin-top: 5px; }
        .btn { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1>Inloggen</h1>
    
    <?php if (!empty($data['error'])): ?>
        <div class="error"><?php echo $data['error']; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($data['success'])): ?>
        <div class="success"><?php echo $data['success']; ?></div>
    <?php endif; ?>
    
    <form action="<?php echo URLROOT; ?>/accounts/login" method="POST">
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
    
    <p><a href="<?php echo URLROOT; ?>">Terug naar home</a></p>
    <p><a href="<?php echo URLROOT; ?>/accounts/register">Nog geen account? Registreren</a></p>
</body>
</html>
