<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Edit Listing</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('assets/css/base.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/sell-seeds.css') ?>">
</head>
<body>

<?php require __DIR__ . '/../includes/navbar.php'; ?>

<div class="sc-dashboard">

  <?php $activePage = 'my-seeds'; require __DIR__ . '/../includes/sidebar.php'; ?>

  <main class="sc-main">

    <div class="sc-main-header">
      <div>
        <a href="<?= htmlspecialchars($backUrl) ?>" class="sc-back-link"><i class="fa-solid fa-arrow-left"></i> Back</a>
        <h1>Edit Listing</h1>
        <p>Update your seed listing details below.</p>
      </div>
      <?php if (!empty($listing['status'])): ?>
        <span class="sc-status-badge sc-status-<?= htmlspecialchars($listing['status']) ?>" style="align-self:flex-start; margin-top:8px;">
          <?= ucfirst($listing['status']) ?>
        </span>
      <?php endif; ?>
    </div>

    <div class="sc-sell-card">

      <div class="sc-sell-header">
        <h2>Edit: <?= htmlspecialchars($listing['seed_name'] ?? '') ?></h2>
        <p>Changes to approved listings will be reflected immediately on the marketplace.</p>
      </div>

      <?php if (isset($error)): ?>
        <div class="sc-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if (isset($success)): ?>
        <div class="sc-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="inventory_id" value="<?= (int)$listing['inventory_id'] ?>">
        <input type="hidden" name="back_url" value="<?= htmlspecialchars($backUrl) ?>">

        <!-- IMAGE UPLOAD -->
        <div class="sc-form-group">
          <label>Seed Image</label>
          <div class="sc-upload-area" id="upload-area" onclick="document.getElementById('image-input').click()">
            <?php if (!empty($listing['image_url'])): ?>
              <img id="img-preview" src="<?= htmlspecialchars($listing['image_url']) ?>"
                   alt="Current image" style="width:100%; max-height:140px; object-fit:cover; border-radius:8px;">
              <div id="upload-placeholder" class="sc-upload-placeholder" style="display:none;">
            <?php else: ?>
              <img id="img-preview" src="" alt="" style="display:none; width:100%; max-height:140px; object-fit:cover; border-radius:8px;">
              <div id="upload-placeholder" class="sc-upload-placeholder">
            <?php endif; ?>
                <span>📷</span>
                <p>Click to upload a new image</p>
                <small>JPG, PNG, WEBP — max 2MB</small>
              </div>
          </div>
          <input type="file" id="image-input" name="image" accept="image/*" style="display:none;" onchange="previewImage(event)">
          <small style="color:#888; margin-top:4px; display:block;">Leave empty to keep the current image.</small>
        </div>

        <!-- SEED NAME -->
        <div class="sc-form-group">
          <label>Seed Name <span class="sc-required">*</span></label>
          <div class="sc-input-fetch-row">
            <input type="text" id="seed-name-input" name="seed_name"
                   placeholder="e.g. Tomato Seeds"
                   value="<?= htmlspecialchars($listing['seed_name'] ?? '') ?>" required>
            <button type="button" class="sc-btn-fetch" id="fetchPlantBtn" onclick="fetchPlantInfo()">
              <i class="fa-solid fa-wand-magic-sparkles"></i> Re-fetch Info
            </button>
          </div>
          <p class="sc-form-hint" id="fetch-hint">
            <i class="fa-solid fa-circle-info"></i>
            Click "Re-fetch Info" to auto-fill details from the Perenual plant database.
          </p>
        </div>

        <!-- PLANT PREVIEW CARD -->
        <div id="plant-preview-card" class="sc-plant-preview" style="display:none;">
          <div class="sc-plant-preview-header">
            <i class="fa-solid fa-leaf"></i>
            <div>
              <h4 id="preview-common-name">—</h4>
              <p id="preview-scientific-name" style="font-style:italic; color:#888; font-size:12px;"></p>
            </div>
            <span class="sc-plant-preview-badge" id="preview-family"></span>
            <button type="button" class="sc-plant-preview-close" onclick="clearPlantPreview()" title="Dismiss">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
          <div class="sc-plant-preview-body">
            <div class="sc-plant-preview-image" id="preview-image-wrap" style="display:none;">
              <img id="preview-img" src="" alt="" style="max-height:120px; border-radius:8px; object-fit:cover;">
            </div>
            <div class="sc-plant-preview-stats">
              <span id="preview-sunlight" class="sc-plant-stat"></span>
              <span id="preview-watering" class="sc-plant-stat"></span>
              <span id="preview-growth" class="sc-plant-stat"></span>
            </div>
          </div>
          <p class="sc-plant-preview-source" id="preview-source"></p>
        </div>

        <!-- HIDDEN auto-filled fields -->
        <input type="hidden" name="scientific_name" id="field-scientific-name">
        <input type="hidden" name="plant_family"    id="field-plant-family">
        <input type="hidden" name="plant_genus"     id="field-plant-genus">
        <input type="hidden" name="sunlight"        id="field-sunlight">
        <input type="hidden" name="watering"        id="field-watering">
        <input type="hidden" name="soil"            id="field-soil">
        <input type="hidden" name="growth_rate"     id="field-growth-rate">
        <input type="hidden" name="care_guide"      id="field-care-guide">
        <input type="hidden" name="trefle_image_url" id="field-trefle-image">

        <!-- CATEGORY + PRICE -->
        <div class="sc-form-row">
          <div class="sc-form-group">
            <label>Category</label>
            <select name="category">
              <option value="">Select category</option>
              <?php foreach (['Vegetable','Herb','Fruit','Flower','Grain','Other'] as $cat): ?>
                <option value="<?= $cat ?>" <?= ($listing['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="sc-form-group">
            <label>Price per Pack (₱) <span class="sc-required">*</span></label>
            <input type="number" name="price" step="0.01" min="0.01" placeholder="e.g. 45.00"
                   value="<?= htmlspecialchars($listing['price'] ?? '') ?>" required>
          </div>
        </div>

        <!-- STOCK -->
        <div class="sc-form-group sc-form-half">
          <label>Stock Quantity (packs) <span class="sc-required">*</span></label>
          <input type="number" name="stock_quantity" min="0" placeholder="e.g. 50"
                 value="<?= htmlspecialchars($listing['stock_quantity'] ?? '') ?>" required>
        </div>

        <!-- PLANTING INFO -->
        <?php
        $mNames = ['','January','February','March','April','May','June',
                   'July','August','September','October','November','December'];
        ?>
        <div class="sc-form-row">
          <div class="sc-form-group">
            <label>Planting Start Month</label>
            <select name="planting_start_month">
              <option value="">None</option>
              <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= (int)($listing['planting_start_month'] ?? 0) === $m ? 'selected' : '' ?>>
                  <?= $mNames[$m] ?>
                </option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="sc-form-group">
            <label>Planting End Month</label>
            <select name="planting_end_month">
              <option value="">None</option>
              <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= (int)($listing['planting_end_month'] ?? 0) === $m ? 'selected' : '' ?>>
                  <?= $mNames[$m] ?>
                </option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <div class="sc-form-group sc-form-half">
          <label>Growing Days</label>
          <input type="number" name="growing_days" min="1" placeholder="e.g. 60"
                 value="<?= htmlspecialchars($listing['growing_days'] ?? '') ?>">
        </div>

        <!-- SUNLIGHT + WATER (auto-filled from Perenual) -->
        <div class="sc-form-row">
          <div class="sc-form-group">
            <label><i class="fa-solid fa-sun" style="color:#f9a825;"></i> Sunlight Requirement</label>
            <input type="text" name="sunlight_display" id="field-sunlight-display"
                   placeholder="Auto-filled from plant data"
                   value="<?= htmlspecialchars($listing['sunlight'] ?? '') ?>">
          </div>
          <div class="sc-form-group">
            <label><i class="fa-solid fa-droplet" style="color:#1565c0;"></i> Water Requirement</label>
            <input type="text" name="watering_display" id="field-watering-display"
                   placeholder="Auto-filled from plant data"
                   value="<?= htmlspecialchars($listing['watering'] ?? '') ?>">
          </div>
        </div>

        <!-- DESCRIPTION -->
        <div class="sc-form-group">
          <label>Description</label>
          <textarea name="description" id="field-description" rows="3" placeholder="Describe your seed — variety, quality, origin, etc."><?= htmlspecialchars($listing['description'] ?? '') ?></textarea>
        </div>

        <div style="display:flex; gap:12px; flex-wrap:wrap;">
          <button type="submit" class="sc-btn-submit">
            <i class="fa-solid fa-floppy-disk"></i> Save Changes
          </button>
          <a href="<?= htmlspecialchars($backUrl) ?>" class="sc-btn sc-btn-ghost sc-btn-lg" style="text-decoration:none;">
            Cancel
          </a>
        </div>

      </form>
    </div>

  </main>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/logout-modal.php'; ?>

<script>
  function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const preview     = document.getElementById('img-preview');
    const placeholder = document.getElementById('upload-placeholder');
    preview.src               = URL.createObjectURL(file);
    preview.style.display     = 'block';
    placeholder.style.display = 'none';
  }

  // ── PERENUAL PLANT LOOKUP ────────────────────────────────────────────────
  function fetchPlantInfo() {
    const query = document.getElementById('seed-name-input').value.trim();
    if (!query) { document.getElementById('seed-name-input').focus(); return; }

    const btn  = document.getElementById('fetchPlantBtn');
    const hint = document.getElementById('fetch-hint');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Searching…';

    fetch('plant-lookup.php?q=' + encodeURIComponent(query))
      .then(r => r.json())
      .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Re-fetch Info';
        if (!data.success) {
          hint.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="color:#e53935;"></i> ' + escHtml(data.error || 'No plant found.');
          hint.style.color = '#e53935';
          return;
        }
        fillForm(data.plant, data.source);
        hint.innerHTML = '<i class="fa-solid fa-circle-check" style="color:#2E7D32;"></i> Plant info loaded! Review before saving.';
        hint.style.color = '#2E7D32';
      })
      .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Re-fetch Info';
        hint.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="color:#e53935;"></i> Network error.';
        hint.style.color = '#e53935';
      });
  }

  function fillForm(p, source) {
    document.getElementById('field-scientific-name').value = p.scientific_name || '';
    document.getElementById('field-plant-family').value    = p.plant_family    || '';
    document.getElementById('field-plant-genus').value     = p.plant_genus     || '';
    document.getElementById('field-sunlight').value        = p.sunlight        || '';
    document.getElementById('field-watering').value        = p.watering        || '';
    document.getElementById('field-soil').value            = p.soil            || '';
    document.getElementById('field-growth-rate').value     = p.growth_rate     || '';
    document.getElementById('field-care-guide').value      = p.care_guide      || '';
    document.getElementById('field-trefle-image').value    = p.image_url       || '';

    // Category
    if (p.category) {
      const sel = document.querySelector('select[name="category"]');
      if (sel) for (let opt of sel.options) if (opt.value === p.category) { opt.selected = true; break; }
    }

    // Planting start month
    if (p.planting_start) {
      const sel = document.querySelector('select[name="planting_start_month"]');
      if (sel) sel.value = p.planting_start;
    }

    // Planting end month
    if (p.planting_end) {
      const sel = document.querySelector('select[name="planting_end_month"]');
      if (sel) sel.value = p.planting_end;
    }

    // Growing days
    if (p.growing_days) {
      const inp = document.querySelector('input[name="growing_days"]');
      if (inp) inp.value = p.growing_days;
    }

    // Sunlight + water visible fields
    const sunEl = document.getElementById('field-sunlight-display');
    const watEl = document.getElementById('field-watering-display');
    if (sunEl) sunEl.value = p.sunlight || '';
    if (watEl) watEl.value = p.watering || '';
    document.getElementById('preview-scientific-name').textContent = p.scientific_name || '';
    document.getElementById('preview-family').textContent          = p.plant_family    || '';

    const stats = [
      p.sunlight    ? '☀️ ' + p.sunlight    : '',
      p.watering    ? '💧 ' + p.watering    : '',
      p.growth_rate ? '📈 ' + p.growth_rate : '',
    ].filter(Boolean);
    document.getElementById('preview-sunlight').textContent = stats[0] || '';
    document.getElementById('preview-watering').textContent = stats[1] || '';
    document.getElementById('preview-growth').textContent   = stats[2] || '';

    if (p.image_url) {
      document.getElementById('preview-img').src = p.image_url;
      document.getElementById('preview-image-wrap').style.display = 'block';
    }

    document.getElementById('preview-source').textContent =
      source === 'cache' ? '✓ Loaded from local cache' : '✓ Fetched from Perenual plant database';
    document.getElementById('plant-preview-card').style.display = 'block';
  }

  function clearPlantPreview() {
    document.getElementById('plant-preview-card').style.display = 'none';
    ['field-scientific-name','field-plant-family','field-plant-genus',
     'field-sunlight','field-watering','field-soil','field-growth-rate',
     'field-care-guide','field-trefle-image'].forEach(id => {
      document.getElementById(id).value = '';
    });
  }

  function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
  }
</script>
</body>
</html>
