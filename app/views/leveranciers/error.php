<?php require APPROOT . '/views/includes/header.php'; ?>

<style>
    .error-container {
        background-color: #fff3f3;
        border: 1px solid #f5c2c7;
        color: #842029;
        border-radius: 10px;
        padding: 30px;
        margin-top: 80px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        animation: fadeIn 1s ease-in-out;
    }

    .error-message {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .error-subtext {
        font-size: 14px;
        color: #a94442;
    }

    .progress-container {
        margin-top: 20px;
        background-color: #e9ecef;
        border-radius: 20px;
        overflow: hidden;
        height: 20px;
        width: 100%;
    }

    .progress-bar {
        height: 100%;
        background-color: #7386FF;
        width: 100%;
        transition: width 1s linear;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="error-container text-center">
                <div class="error-message">
                    <?php echo htmlspecialchars($data['error_message']); ?>
                </div>
                <div class="error-subtext">
                    Er is iets misgegaan. Geen zorgen, probeer het gerust opnieuw of neem contact op als het probleem blijft voorkomen.
                </div>
                <p class="mt-4">U wordt over <span id="countdown">5</span> seconden doorgestuurd naar het dashboard...</p>
                <div class="progress-container mt-2">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let countdown = 5;
    const countdownElement = document.getElementById('countdown');
    const progressBar = document.getElementById('progressBar');

    const timer = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;
        progressBar.style.width = (countdown / 5) * 100 + '%';

        if (countdown <= 0) {
            clearInterval(timer);
            window.location.href = '<?php echo $data['redirect_url']; ?>';
        }
    }, 1000);
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>
