<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <h1 class="title"><?php echo $data['title']; ?></h1>

    <?php if (!empty($data['success'])): ?>
        <div class="alert alert-success">
            Leverancier succesvol toegevoegd!
        </div>
        <script>
            setTimeout(function() {
                window.location.href = "<?php echo URLROOT; ?>/leveranciers";
            }, 5000);
        </script>
    <?php endif; ?>

    <?php if (!empty($data['general_err'])): ?>
        <div class="alert alert-error">
            <strong>Fout:</strong> <?php echo $data['general_err']; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form action="<?php echo URLROOT; ?>/leveranciers/add" method="POST">
            <div class="form-group">
                <label for="bedrijfsnaam">Bedrijfsnaam *</label>
                <input type="text" id="bedrijfsnaam" name="bedrijfsnaam"
                       value="<?php echo $data['bedrijfsnaam'] ?? ''; ?>" required>
                <span class="error"><?php echo $data['bedrijfsnaam_err'] ?? ''; ?></span>
            </div>

            <div class="form-group">
                <label for="adres">Adres *</label>
                <input type="text" id="adres" name="adres"
                       value="<?php echo $data['adres'] ?? ''; ?>" required>
                <span class="error"><?php echo $data['adres_err'] ?? ''; ?></span>
            </div>

            <div class="form-group">
                <label for="contactnaam">Contact Naam *</label>
                <input type="text" id="contactnaam" name="contactnaam"
                       value="<?php echo $data['contactnaam'] ?? ''; ?>" required>
                <span class="error"><?php echo $data['contactnaam_err'] ?? ''; ?></span>
            </div>

            <div class="form-group">
                <label for="contactemail">Contact E-mail *</label>
                <input type="email" id="contactemail" name="contactemail" 
                       value="<?php echo $data['contactemail'] ?? ''; ?>" required>
                <?php if (!empty($data['contactemail_err']) && $data['contactemail_err'] !== 'Dit e-mailadres is al in gebruik bij een andere leverancier'): ?>
                    <span class="error"><?php echo $data['contactemail_err']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="contacttelefoon">Contact Telefoonnummer *</label>
                <input type="tel" id="contacttelefoon" name="contacttelefoon"
                       value="<?php echo $data['contacttelefoon'] ?? ''; ?>" required>
                <span class="error"><?php echo $data['contacttelefoon_err'] ?? ''; ?></span>
            </div>

            <div class="form-group">
                <label for="eerstvolgendelevering">Eerstvolgende Levering *</label>
                <input type="datetime-local" id="eerstvolgendelevering" name="eerstvolgendelevering"
                       value="<?php echo $data['eerstvolgendelevering'] ?? ''; ?>" 
                       min="<?php echo date('Y-m-d\TH:i'); ?>"
                       max="2026-12-31T23:59"
                       required>
                <span class="error"><?php echo $data['eerstvolgendelevering_err'] ?? ''; ?></span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Toevoegen</button>
                <a href="<?php echo URLROOT; ?>/leveranciers" class="btn-cancel">Annuleren</a>
            </div>
        </form>
    </div>
</div>

<style>
    .title {
        text-align: center;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .form-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #333;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box;
    }

    .form-group input:focus {
        border-color: #7386FF;
        outline: none;
        box-shadow: 0 0 5px rgba(115, 134, 255, 0.3);
    }

    .error {
        color: #dc3545;
        font-size: 14px;
        margin-top: 5px;
        display: block;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .btn-submit {
        background-color: #28a745;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
    }

    .btn-submit:hover {
        background-color: #218838;
    }

    .btn-cancel {
        background-color: #6c757d;
        color: white;
        padding: 12px 24px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 16px;
        font-weight: bold;
        display: inline-block;
    }

    .btn-cancel:hover {
        background-color: #545b62;
        text-decoration: none;
        color: white;
    }

    .alert.alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        padding: 10px 20px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: bold;
    }

    .alert.alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 2px solid #f5c6cb;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: bold;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
  .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ddd;
        margin-bottom: 15px;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 18px;
        color: #333;
    }

    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .modal-body {
        margin-bottom: 15px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-modal-close {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
    }

    .btn-modal-close:hover {
        background-color: #0056b3;
    }
</style>


  

<script>
// Modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('errorModal');
    if (modal) {
        const closeBtn = modal.querySelector('.close');
        const modalCloseBtn = modal.querySelector('.btn-modal-close');
        
        // Close modal when clicking X
        if (closeBtn) {
            closeBtn.onclick = function() {
                modal.style.display = 'none';
            }
        }
        
        // Close modal when clicking close button
        if (modalCloseBtn) {
            modalCloseBtn.onclick = function() {
                modal.style.display = 'none';
            }
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    }
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>
