<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?> - <?php echo SITENAME; ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .btn { background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 5px; }
        .btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1><?php echo $data['title']; ?></h1>
    
    <p>Welkom bij het managementsysteem van de voedselbank.</p>
    
    <div>
        <a href="<?php echo URLROOT; ?>/accounts/register" class="btn">Registreren</a>
        <a href="<?php echo URLROOT; ?>/accounts/login" class="btn">Inloggen</a>
    </div>
</body>
</html>