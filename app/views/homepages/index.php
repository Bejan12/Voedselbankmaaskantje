<?php require APPROOT . '/views/includes/header.php'; ?>

<h1><?php echo $data['title']; ?></h1>

<p>Welkom bij het managementsysteem van de voedselbank.</p>

<div>
    <a href="<?php echo URLROOT; ?>/accounts/register" class="btn">Registreren</a>
    <a href="<?php echo URLROOT; ?>/accounts/login" class="btn">Inloggen</a>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>