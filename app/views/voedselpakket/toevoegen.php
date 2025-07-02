<?php require_once APPROOT . '/views/includes/header.php'; ?>
<style>
    .modal-backdrop { z-index: 1040 !important; }
    .modal-content { border-radius: 1rem; }
</style>
<div class="modal fade" id="voegPakketToeModal" tabindex="-1" aria-labelledby="voegPakketToeLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="<?= URLROOT; ?>/voedselpakket/toevoegen">
        <div class="modal-header">
          <h5 class="modal-title" id="voegPakketToeLabel">Voedselpakket toevoegen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="klant" class="form-label">Klant (gezin)</label>
            <select name="klant" id="klant" class="form-select" required>
              <option value="">Selecteer een klant</option>
              <?php foreach ($data['klanten'] as $klant): ?>
                <option value="<?= $klant->KlantID ?>"> <?= htmlspecialchars($klant->gezinsnaam) ?> </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="datum" class="form-label">Datum samenstelling</label>
            <input type="date" name="datum" id="datum" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="producten" class="form-label">Producten in pakket</label>
            <select name="producten[]" id="producten" class="form-select" multiple required>
              <?php foreach ($data['producten'] as $product): ?>
                <option value="<?= $product->ProductID ?>"> <?= htmlspecialchars($product->ProductNaam) ?> </option>
              <?php endforeach; ?>
            </select>
            <small class="text-muted">Houd Ctrl (Windows) of Cmd (Mac) ingedrukt om meerdere producten te selecteren.</small>
          </div>
          <div class="mb-3">
            <label for="aantallen" class="form-label">Aantal per product (zelfde volgorde als selectie)</label>
            <input type="text" name="aantallen" id="aantallen" class="form-control" placeholder="Bijv: 2,1,3" required>
            <small class="text-muted">Vul het aantal in voor elk geselecteerd product, gescheiden door komma's.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
          <button type="submit" class="btn btn-success">Toevoegen</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Open modal als ?toevoegen=1 in de url staat
    document.addEventListener('DOMContentLoaded', function() {
        const url = new URL(window.location.href);
        if (url.searchParams.get('toevoegen') === '1') {
            const modal = new bootstrap.Modal(document.getElementById('voegPakketToeModal'));
            modal.show();
        }
    });
</script>
<?php require_once APPROOT . '/views/includes/footer.php'; ?>
