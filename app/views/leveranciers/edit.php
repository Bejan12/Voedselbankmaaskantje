<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <h1 class="title"><?php echo $data['title']; ?></h1>

    <?php if (!empty($data['success'])): ?>
        <div class="alert alert-success">
            Leverancier succesvol gewijzigd!
        </div>
        <script>
            setTimeout(function() {
                window.location.href = "<?php echo URLROOT; ?>/leveranciers";
            }, 3000);
        </script>
    <?php endif; ?>

    <?php if (!empty($data['general_err'])): ?>
        <div class="alert alert-error">
            <strong>Fout:</strong> <?php echo $data['general_err']; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form action="<?php echo URLROOT; ?>/leveranciers/edit/<?php echo $data['leverancier_id'] ?? ''; ?>" method="POST">
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
                <span class="error"><?php echo $data['contactemail_err'] ?? ''; ?></span>
            </div>

            <div class="form-group">
                <label for="contacttelefoon">Contact Telefoonnummer *</label>
                <input type="tel" id="contacttelefoon" name="contacttelefoon"
                       value="<?php echo $data['contacttelefoon'] ?? ''; ?>" 
                       placeholder="0612345678"
                       pattern="[0-9]{8,15}"
                       maxlength="15"
                       required>
                <small class="phone-format-hint">Alleen cijfers, 8-15 karakters (bijv. 0612345678)</small>
                <span class="error"><?php echo $data['contacttelefoon_err'] ?? ''; ?></span>
            </div>

            <div class="form-group">
                <label for="eerstvolgendelevering">Eerstvolgende Levering *</label>
                <div class="datetime-container">
                    <div class="date-input">
                        <label for="levering_datum">Datum</label>
                        <input type="text" id="levering_datum" name="levering_datum"
                               value="<?php echo !empty($data['eerstvolgendelevering']) ? date('d-m-Y', strtotime($data['eerstvolgendelevering'])) : ''; ?>" 
                               placeholder="dd-mm-jjjj"
                               pattern="^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}$"
                               maxlength="10"
                               required>
                        <small class="date-format-hint">Formaat: dag-maand-jaar (bijv. 15-03-2024)</small>
                    </div>
                    <div class="time-input">
                        <label for="levering_tijd">Tijd</label>
                        <input type="time" id="levering_tijd" name="levering_tijd"
                               value="<?php echo !empty($data['eerstvolgendelevering']) ? date('H:i', strtotime($data['eerstvolgendelevering'])) : ''; ?>" 
                               min="00:00"
                               max="23:59"
                               required>
                        <small class="time-format-hint">24-uurs formaat (00:00 - 23:59)</small>
                    </div>
                </div>
                <span class="error"><?php echo $data['eerstvolgendelevering_err'] ?? ''; ?></span>
            </div>


            <div class="form-actions">
                <button type="submit" class="btn-submit">Wijzigen</button>
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

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group select:focus {
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
        background-color: #007bff;
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
        background-color: #0056b3;
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

    .datetime-container {
        display: flex;
        gap: 15px;
    }

    .date-input,
    .time-input {
        flex: 1;
    }

    .date-input label,
    .time-input label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #333;
        font-size: 14px;
    }

    .time-format-hint,
    .date-format-hint,
    .phone-format-hint {
        font-size: 12px;
        color: #666;
        margin-top: 3px;
        display: block;
        font-style: italic;
    }

    /* Forceer 24-uurs formaat voor verschillende browsers */
    input[type="time"] {
        font-family: monospace;
        -webkit-appearance: none;
        -moz-appearance: textfield;
        appearance: none;
    }
    
    /* Verberg AM/PM indicator */
    input[type="time"]::-webkit-calendar-picker-indicator {
        filter: invert(0.5);
    }
    
    input[type="time"]::-webkit-datetime-edit-ampm-field {
        display: none;
    }
    
    input[type="time"]::-webkit-datetime-edit-fields-wrapper {
        display: flex;
    }
    
    /* Voor Firefox */
    input[type="time"]::-moz-focus-inner {
        border: 0;
    }

    @media (max-width: 768px) {
        .datetime-container {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const timeInput = document.getElementById('levering_tijd');
    const dateInput = document.getElementById('levering_datum');
    const phoneInput = document.getElementById('contacttelefoon');
    
    // Telefoonnummer input validatie
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            // Verwijder alle niet-cijfer karakters
            let value = e.target.value.replace(/\D/g, '');
            
            // Beperk tot maximaal 15 cijfers
            if (value.length > 15) {
                value = value.substring(0, 15);
            }
            
            e.target.value = value;
            
            // Validatie
            if (value.length < 8) {
                e.target.setCustomValidity('Telefoonnummer moet minimaal 8 cijfers bevatten');
            } else if (value.length > 15) {
                e.target.setCustomValidity('Telefoonnummer mag maximaal 15 cijfers bevatten');
            } else {
                e.target.setCustomValidity('');
            }
        });
        
        // Valideer bij blur
        phoneInput.addEventListener('blur', function(e) {
            const value = e.target.value;
            if (value && (value.length < 8 || value.length > 15)) {
                if (value.length < 8) {
                    e.target.setCustomValidity('Telefoonnummer moet minimaal 8 cijfers bevatten');
                } else {
                    e.target.setCustomValidity('Telefoonnummer mag maximaal 15 cijfers bevatten');
                }
            }
        });
        
        // Voorkom plakken van tekst met letters
        phoneInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const numbersOnly = paste.replace(/\D/g, '').substring(0, 15);
            e.target.value = numbersOnly;
            
            // Trigger input event voor validatie
            const inputEvent = new Event('input', { bubbles: true });
            e.target.dispatchEvent(inputEvent);
        });
    }
    
    // Datum input masking en validatie
    if (dateInput) {
        dateInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Alleen cijfers
            
            // Voeg automatisch streepjes toe
            if (value.length >= 2) {
                value = value.substring(0, 2) + '-' + value.substring(2);
            }
            if (value.length >= 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 9);
            }
            
            e.target.value = value;
            
            // Valideer datum
            if (value.length === 10) {
                const parts = value.split('-');
                const day = parseInt(parts[0]);
                const month = parseInt(parts[1]);
                const year = parseInt(parts[2]);
                const inputDate = new Date(year, month - 1, day);
                const today = new Date();
                const maxDate = new Date(2026, 11, 31);
                
                today.setHours(0, 0, 0, 0);
                
                if (inputDate.getDate() !== day || inputDate.getMonth() !== month - 1 || inputDate.getFullYear() !== year) {
                    e.target.setCustomValidity('Voer een geldige datum in (dd-mm-jjjj)');
                } else if (inputDate < today) {
                    e.target.setCustomValidity('Datum kan niet in het verleden liggen');
                } else if (inputDate > maxDate) {
                    e.target.setCustomValidity('Datum kan niet verder dan 31-12-2026 liggen');
                } else {
                    e.target.setCustomValidity('');
                }
            } else if (value.length > 0) {
                e.target.setCustomValidity('Voer een complete datum in (dd-mm-jjjj)');
            } else {
                e.target.setCustomValidity('');
            }
        });
        
        // Valideer bij blur
        dateInput.addEventListener('blur', function(e) {
            if (e.target.value && e.target.value.length !== 10) {
                e.target.setCustomValidity('Voer een complete datum in (dd-mm-jjjj)');
            }
        });
    }
    
    // Tijd input validatie
    if (timeInput) {
        timeInput.removeAttribute('step');
        timeInput.lang = 'nl-NL';
        
        timeInput.addEventListener('input', function(e) {
            let value = e.target.value;
            value = value.replace(/\s*(AM|PM|am|pm|Am|Pm)\s*/g, '');
            
            if (value.match(/^\d{1,2}:\d{2}$/)) {
                const parts = value.split(':');
                let hours = parseInt(parts[0]);
                let minutes = parseInt(parts[1]);
                
                if (hours > 23) hours = 23;
                if (minutes > 59) minutes = 59;
                
                value = String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
            }
            
            if (value && !value.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
                e.target.setCustomValidity('Gebruik 24-uurs formaat (00:00 - 23:59)');
            } else {
                e.target.setCustomValidity('');
            }
            
            e.target.value = value;
        });
        
        timeInput.addEventListener('focus', function() {
            this.setAttribute('data-format', '24');
            if (this.value) {
                let currentValue = this.value;
                this.value = '';
                setTimeout(() => {
                    this.value = currentValue;
                }, 10);
            }
        });
        
        timeInput.addEventListener('blur', function() {
            let value = this.value.replace(/\s*(AM|PM|am|pm|Am|Pm)\s*/g, '');
            if (value !== this.value) {
                this.value = value;
            }
        });
    }
    
    // Form submit: combineer datum en tijd
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const datumInput = document.getElementById('levering_datum');
            const tijdInput = document.getElementById('levering_tijd');
            
            if (datumInput && tijdInput && datumInput.value && tijdInput.value) {
                // Converteer Nederlandse datum naar ISO formaat
                const dateParts = datumInput.value.split('-');
                const isoDate = dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0]; // jjjj-mm-dd
                
                let cleanTime = tijdInput.value.replace(/\s*(AM|PM|am|pm|Am|Pm)\s*/g, '');
                
                // Maak verborgen input voor gecombineerde datetime
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'eerstvolgendelevering';
                hiddenInput.value = isoDate + 'T' + cleanTime;
                form.appendChild(hiddenInput);
            }
        });
    }
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>
