<?php
$currentMonth = (int) date('n');

$monthData = [];
for ($m = 1; $m <= 12; $m++) { $monthData[$m] = []; }

if (!empty($plantingSeeds)) {
    foreach ($plantingSeeds as $seed) {
        $start = (int)($seed['planting_start_month'] ?? 0);
        $end   = (int)($seed['planting_end_month']   ?? $start);
        if ($start < 1) continue;
        for ($m = $start; $m <= min($end, 12); $m++) {
            $monthData[$m][] = $seed;
        }
    }
}

$monthNames = ['','January','February','March','April','May','June',
               'July','August','September','October','November','December'];
$monthNums  = ['','01','02','03','04','05','06','07','08','09','10','11','12'];
$seasons    = [1=>'Dry',2=>'Dry',3=>'Dry',4=>'Dry',5=>'Wet',6=>'Wet',
               7=>'Wet',8=>'Wet',9=>'Wet',10=>'Dry',11=>'Dry',12=>'Dry'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Planting Guide</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('assets/css/base.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/planting-guide.css') ?>">
</head>
<body>

<?php
$user = ['first_name' => $_SESSION['first_name'] ?? 'Grower',
         'email'      => $_SESSION['email']      ?? '',
         'profile_image' => $_SESSION['profile_image'] ?? ''];
require __DIR__ . '/includes/navbar.php';
?>

<div class="sc-dashboard">

  <?php $activePage = 'planting-guide'; require __DIR__ . '/includes/sidebar.php'; ?>

  <main class="sc-main sc-main--guide">

    <!-- ── PAGE HEADER ── -->
    <div class="sc-guide-header">
      <div>
        <h1><i class="fa-solid fa-calendar-days"></i> Planting Guide</h1>
        <p>Best planting times for every seed, every season.</p>
      </div>
      <div class="sc-guide-controls">
        <select id="month-filter" onchange="filterByMonth()" class="sc-select">
          <option value="">All Months</option>
          <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>"><?= $monthNames[$m] ?></option>
          <?php endfor; ?>
        </select>
        <div class="sc-view-toggle">
          <button class="sc-toggle-btn active" id="btn-calendar" onclick="switchView('calendar')">
            <i class="fa-solid fa-calendar"></i> Calendar
          </button>
          <button class="sc-toggle-btn" id="btn-list" onclick="switchView('list')">
            <i class="fa-solid fa-list"></i> List
          </button>
        </div>
      </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- ── PLANT TODAY? WEATHER SECTION ── -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    <div class="sc-weather-card" id="weatherCard">

      <!-- Card Header -->
      <div class="sc-weather-card-header">
        <div class="sc-weather-card-title">
          <i class="fa-solid fa-cloud-sun"></i>
          <div>
            <h2>Plant Today?</h2>
            <p>Check real-time weather to see if today is a good day to plant.</p>
          </div>
        </div>
        <button class="sc-weather-collapse-btn" id="weatherCollapseBtn" onclick="toggleWeatherCard()" title="Collapse">
          <i class="fa-solid fa-chevron-up" id="weatherCollapseIcon"></i>
        </button>
      </div>

      <!-- Card Body -->
      <div id="weatherCardBody">

        <!-- Location Input -->
        <div class="sc-weather-search">
          <div class="sc-weather-search-row">
            <div class="sc-weather-input-wrap">
              <i class="fa-solid fa-location-dot sc-weather-input-icon"></i>
              <input type="text" id="cityInput" class="sc-weather-input"
                placeholder="Enter city (e.g. Quezon City, Manila, Cebu)"
                onkeydown="if(event.key==='Enter') checkWeather()">
            </div>
            <button class="sc-weather-btn-check" onclick="checkWeather()" id="checkBtn">
              <i class="fa-solid fa-magnifying-glass"></i> Check Weather
            </button>
            <button class="sc-weather-btn-locate" onclick="useMyLocation()" id="locateBtn" title="Use my location">
              <i class="fa-solid fa-crosshairs"></i> <span class="sc-locate-label">My Location</span>
            </button>
          </div>
          <p class="sc-weather-hint">
            <i class="fa-solid fa-circle-info"></i>
            Powered by OpenWeatherMap. Data refreshes on each check.
          </p>
        </div>

        <!-- Loading State -->
        <div class="sc-weather-loading" id="weatherLoading" style="display:none;">
          <div class="sc-weather-spinner"></div>
          <p>Fetching weather data<span class="sc-dots"></span></p>
        </div>

        <!-- Error State -->
        <div class="sc-weather-error" id="weatherError" style="display:none;">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <p id="weatherErrorMsg">Something went wrong.</p>
          <button onclick="document.getElementById('weatherError').style.display='none'" class="sc-weather-btn-dismiss">Dismiss</button>
        </div>

        <!-- ── RESULT PANEL ── -->
        <div id="weatherResult" style="display:none;">

          <!-- Location + Timestamp -->
          <div class="sc-weather-location-bar">
            <span id="wLocationText"><i class="fa-solid fa-location-dot"></i> —</span>
            <span id="wTimestamp" class="sc-weather-timestamp"></span>
          </div>

          <!-- Main Weather Display -->
          <div class="sc-weather-main">

            <!-- Left: Big weather visual -->
            <div class="sc-weather-visual">
              <img id="wIcon" src="" alt="Weather icon" class="sc-weather-icon-img">
              <div class="sc-weather-temp-block">
                <span class="sc-weather-temp" id="wTemp">—</span>
                <span class="sc-weather-unit">°C</span>
              </div>
              <p class="sc-weather-condition" id="wCondition">—</p>
              <p class="sc-weather-feels" id="wFeels">Feels like —°C</p>
            </div>

            <!-- Right: Stats grid -->
            <div class="sc-weather-stats">
              <div class="sc-weather-stat">
                <div class="sc-weather-stat-icon sc-stat-rain">
                  <i class="fa-solid fa-droplet"></i>
                </div>
                <div class="sc-weather-stat-info">
                  <span class="sc-weather-stat-value" id="wRain">—%</span>
                  <span class="sc-weather-stat-label">Rain Chance</span>
                </div>
              </div>
              <div class="sc-weather-stat">
                <div class="sc-weather-stat-icon sc-stat-humidity">
                  <i class="fa-solid fa-water"></i>
                </div>
                <div class="sc-weather-stat-info">
                  <span class="sc-weather-stat-value" id="wHumidity">—%</span>
                  <span class="sc-weather-stat-label">Humidity</span>
                </div>
              </div>
              <div class="sc-weather-stat">
                <div class="sc-weather-stat-icon sc-stat-wind">
                  <i class="fa-solid fa-wind"></i>
                </div>
                <div class="sc-weather-stat-info">
                  <span class="sc-weather-stat-value" id="wWind">— m/s</span>
                  <span class="sc-weather-stat-label">Wind Speed</span>
                </div>
              </div>
              <div class="sc-weather-stat">
                <div class="sc-weather-stat-icon sc-stat-temp">
                  <i class="fa-solid fa-temperature-half"></i>
                </div>
                <div class="sc-weather-stat-info">
                  <span class="sc-weather-stat-value" id="wTempRange">—</span>
                  <span class="sc-weather-stat-label">Min / Max</span>
                </div>
              </div>
            </div>
          </div>

          <!-- ── RECOMMENDATION BANNER ── -->
          <div class="sc-recommendation" id="recommendationBanner">
            <div class="sc-rec-icon-wrap" id="recIconWrap">
              <i class="fa-solid fa-circle-check" id="recIcon"></i>
            </div>
            <div class="sc-rec-content">
              <h3 id="recTitle">—</h3>
              <p id="recMessage">—</p>
            </div>
          </div>

          <!-- Reason pills -->
          <div class="sc-rec-reasons" id="recReasons"></div>

          <!-- ── SUGGESTED CROPS ── -->
          <div id="suggestedCropsSection" style="display:none;">
            <div class="sc-crops-header">
              <h3><i class="fa-solid fa-seedling"></i> Recommended Crops to Plant Today</h3>
              <span class="sc-crops-month" id="cropsMonthLabel"></span>
            </div>
            <div class="sc-crops-grid" id="cropsGrid"></div>
          </div>

          <!-- No crops message -->
          <div id="noCropsMsg" class="sc-no-crops" style="display:none;">
            <i class="fa-solid fa-circle-info"></i>
            <p>No in-season crops found for this month in your inventory. Add seeds with planting months to see suggestions here.</p>
          </div>

        </div><!-- end #weatherResult -->

      </div><!-- end #weatherCardBody -->
    </div><!-- end .sc-weather-card -->

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- ── PLANTING CALENDAR ── -->
    <!-- ════════════════════════════════════════════════════════════════════ -->

    <!-- CALENDAR VIEW -->
    <div id="view-calendar">
      <div class="sc-calendar-grid">
        <?php for ($m = 1; $m <= 12; $m++):
          $season = $seasons[$m];
          $crops  = $monthData[$m];
        ?>
        <div class="sc-cal-card <?= $m === $currentMonth ? 'current' : '' ?>" data-month="<?= $m ?>">
          <div class="sc-cal-header">
            <span class="sc-cal-month"><?= $monthNames[$m] ?></span>
          </div>
          <div class="sc-cal-badges">
            <?php if ($m === $currentMonth): ?>
              <div class="sc-cal-now-badge"><i class="fa-solid fa-circle-dot"></i> This Month</div>
            <?php endif; ?>
            <span class="sc-cal-season <?= strtolower($season) ?>">
              <?= $season === 'Dry' ? '<i class="fa-solid fa-sun"></i> Dry' : '<i class="fa-solid fa-cloud-rain"></i> Wet' ?>
            </span>
          </div>
          <ul class="sc-cal-crops">
            <?php if (!empty($crops)): ?>
              <?php foreach ($crops as $crop): ?>
                <li><i class="fa-solid fa-seedling"></i> <?= htmlspecialchars($crop['name']) ?></li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="sc-cal-empty">No seeds this month</li>
            <?php endif; ?>
          </ul>
        </div>
        <?php endfor; ?>
      </div>
    </div>

    <!-- LIST VIEW -->
    <div id="view-list" style="display:none;">
      <div class="sc-list-wrap">
        <?php for ($m = 1; $m <= 12; $m++):
          $season = $seasons[$m];
          $crops  = $monthData[$m];
        ?>
        <div class="sc-list-row <?= $m === $currentMonth ? 'current' : '' ?>" data-month="<?= $m ?>">
          <div class="sc-list-month">
            <span class="sc-list-num"><?= $monthNums[$m] ?></span>
            <span class="sc-list-name"><?= $monthNames[$m] ?></span>
            <span class="sc-list-season <?= strtolower($season) ?>">
              <?= $season === 'Dry' ? '<i class="fa-solid fa-sun"></i> Dry' : '<i class="fa-solid fa-cloud-rain"></i> Wet' ?>
            </span>
          </div>
          <div class="sc-list-crops">
            <?php if (!empty($crops)): ?>
              <?php foreach ($crops as $crop): ?>
                <span class="sc-crop-tag"><i class="fa-solid fa-seedling"></i> <?= htmlspecialchars($crop['name']) ?></span>
              <?php endforeach; ?>
            <?php else: ?>
              <span class="sc-list-empty">No seeds this month</span>
            <?php endif; ?>
          </div>
          <?php if ($m === $currentMonth): ?>
            <span class="sc-now-badge"><i class="fa-solid fa-circle-dot"></i> Now</span>
          <?php endif; ?>
        </div>
        <?php endfor; ?>
      </div>
    </div>

  </main>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/logout-modal.php'; ?>

<script>
// ── VIEW TOGGLE ──────────────────────────────────────────────────────────────
function switchView(view) {
  document.getElementById('view-calendar').style.display = view === 'calendar' ? 'block' : 'none';
  document.getElementById('view-list').style.display     = view === 'list'     ? 'block' : 'none';
  document.getElementById('btn-calendar').classList.toggle('active', view === 'calendar');
  document.getElementById('btn-list').classList.toggle('active', view === 'list');
}

function filterByMonth() {
  const val = document.getElementById('month-filter').value;
  document.querySelectorAll('.sc-cal-card').forEach(c => {
    c.style.display = (!val || c.dataset.month === val) ? '' : 'none';
  });
  document.querySelectorAll('.sc-list-row').forEach(r => {
    r.style.display = (!val || r.dataset.month === val) ? '' : 'none';
  });
}

// ── WEATHER CARD COLLAPSE ────────────────────────────────────────────────────
function toggleWeatherCard() {
  const body = document.getElementById('weatherCardBody');
  const icon = document.getElementById('weatherCollapseIcon');
  const collapsed = body.style.display === 'none';
  body.style.display = collapsed ? '' : 'none';
  icon.className = collapsed ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down';
}

// ── GEOLOCATION ──────────────────────────────────────────────────────────────
function useMyLocation() {
  if (!navigator.geolocation) {
    showError('Geolocation is not supported by your browser.');
    return;
  }
  const btn = document.getElementById('locateBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Locating...';

  navigator.geolocation.getCurrentPosition(
    pos => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-crosshairs"></i> <span class="sc-locate-label">My Location</span>';
      fetchWeather({ lat: pos.coords.latitude, lon: pos.coords.longitude });
    },
    err => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-crosshairs"></i> <span class="sc-locate-label">My Location</span>';
      showError('Location access denied. Please enter your city manually.');
    }
  );
}

// ── MANUAL CITY CHECK ────────────────────────────────────────────────────────
function checkWeather() {
  const city = document.getElementById('cityInput').value.trim();
  if (!city) {
    document.getElementById('cityInput').focus();
    document.getElementById('cityInput').classList.add('sc-input-shake');
    setTimeout(() => document.getElementById('cityInput').classList.remove('sc-input-shake'), 500);
    return;
  }
  fetchWeather({ city });
}

// ── FETCH & RENDER ───────────────────────────────────────────────────────────
function fetchWeather(params) {
  showLoading(true);
  hideError();
  document.getElementById('weatherResult').style.display = 'none';

  const qs = new URLSearchParams(params).toString();
  fetch('weather-check.php?' + qs)
    .then(r => r.json())
    .then(data => {
      showLoading(false);
      if (data.error) { showError(data.error); return; }
      renderWeather(data);
    })
    .catch(() => {
      showLoading(false);
      showError('Network error. Please check your connection and try again.');
    });
}

function renderWeather(data) {
  const w  = data.weather;
  const a  = data.analysis;
  const crops = data.suggested_crops || [];
  const month = data.current_month;

  const monthNames = ['','January','February','March','April','May','June',
                      'July','August','September','October','November','December'];

  // Location + time
  document.getElementById('wLocationText').innerHTML =
    '<i class="fa-solid fa-location-dot"></i> ' +
    escHtml(w.city) + (w.country ? ', ' + escHtml(w.country) : '');
  document.getElementById('wTimestamp').textContent =
    'Checked: ' + new Date().toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' });

  // Weather visuals
  document.getElementById('wIcon').src = w.icon_url;
  document.getElementById('wIcon').alt = w.condition_desc;
  document.getElementById('wTemp').textContent = w.temperature;
  document.getElementById('wCondition').textContent = w.condition_desc;
  document.getElementById('wFeels').textContent = 'Feels like ' + w.feels_like + '°C';

  // Stats
  document.getElementById('wRain').textContent     = w.rain_chance + '%';
  document.getElementById('wHumidity').textContent = w.humidity + '%';
  document.getElementById('wWind').textContent     = w.wind_speed + ' m/s';
  document.getElementById('wTempRange').textContent = w.temp_min + '° / ' + w.temp_max + '°';

  // Recommendation banner
  const banner = document.getElementById('recommendationBanner');
  const iconWrap = document.getElementById('recIconWrap');
  const icon = document.getElementById('recIcon');
  banner.className = 'sc-recommendation sc-rec-' + a.status;
  iconWrap.className = 'sc-rec-icon-wrap sc-rec-icon-' + a.status;

  const icons = { good: 'fa-circle-check', caution: 'fa-triangle-exclamation', bad: 'fa-circle-xmark' };
  icon.className = 'fa-solid ' + (icons[a.status] || 'fa-circle-check');

  document.getElementById('recTitle').textContent   = a.title;
  document.getElementById('recMessage').textContent = a.message;

  // Reason pills
  const reasonsEl = document.getElementById('recReasons');
  reasonsEl.innerHTML = '';
  (a.reasons || []).forEach(r => {
    const pill = document.createElement('span');
    pill.className = 'sc-rec-pill';
    pill.textContent = r;
    reasonsEl.appendChild(pill);
  });

  // Suggested crops
  const cropsSection = document.getElementById('suggestedCropsSection');
  const noCropsMsg   = document.getElementById('noCropsMsg');
  const cropsGrid    = document.getElementById('cropsGrid');

  if (crops.length > 0 && a.status !== 'bad') {
    cropsSection.style.display = 'block';
    noCropsMsg.style.display   = 'none';
    document.getElementById('cropsMonthLabel').textContent =
      'In season for ' + (monthNames[month] || '');

    cropsGrid.innerHTML = '';
    crops.forEach(crop => {
      const card = document.createElement('div');
      card.className = 'sc-crop-card';

      const categoryIcons = {
        'Vegetable': 'fa-carrot',
        'Herb':      'fa-mortar-pestle',
        'Fruit':     'fa-apple-whole',
        'Flower':    'fa-fan',
        'Grain':     'fa-wheat-awn',
        'Other':     'fa-seedling',
      };
      const catIcon = categoryIcons[crop.category] || 'fa-seedling';
      const growDays = crop.growing_days ? crop.growing_days + ' days to harvest' : '';

      card.innerHTML =
        '<div class="sc-crop-card-icon"><i class="fa-solid ' + catIcon + '"></i></div>' +
        '<div class="sc-crop-card-info">' +
          '<span class="sc-crop-card-name">' + escHtml(crop.name) + '</span>' +
          '<span class="sc-crop-card-cat">' + escHtml(crop.category || 'Seed') + '</span>' +
          (growDays ? '<span class="sc-crop-card-days"><i class="fa-regular fa-clock"></i> ' + escHtml(growDays) + '</span>' : '') +
        '</div>';
      cropsGrid.appendChild(card);
    });
  } else if (a.status !== 'bad') {
    cropsSection.style.display = 'none';
    noCropsMsg.style.display   = 'block';
  } else {
    cropsSection.style.display = 'none';
    noCropsMsg.style.display   = 'none';
  }

  // Apply background tint to weather card
  const card = document.getElementById('weatherCard');
  card.className = 'sc-weather-card sc-weather-bg-' + getWeatherBg(w.condition_main, w.icon);

  document.getElementById('weatherResult').style.display = 'block';

  // Smooth scroll to result
  document.getElementById('weatherResult').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function getWeatherBg(condMain, icon) {
  const c = (condMain || '').toLowerCase();
  const isNight = icon && icon.endsWith('n');
  if (c === 'thunderstorm') return 'storm';
  if (c === 'drizzle' || c === 'rain') return 'rain';
  if (c === 'snow') return 'cold';
  if (c === 'clear') return isNight ? 'night' : 'sunny';
  if (c === 'clouds') return 'cloudy';
  return 'default';
}

// ── UI HELPERS ───────────────────────────────────────────────────────────────
function showLoading(show) {
  document.getElementById('weatherLoading').style.display = show ? 'flex' : 'none';
  document.getElementById('checkBtn').disabled  = show;
  document.getElementById('locateBtn').disabled = show;
}

function showError(msg) {
  document.getElementById('weatherErrorMsg').textContent = msg;
  document.getElementById('weatherError').style.display = 'flex';
}

function hideError() {
  document.getElementById('weatherError').style.display = 'none';
}

function escHtml(str) {
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(str || ''));
  return d.innerHTML;
}

// Animated dots for loading text
(function() {
  let n = 0;
  setInterval(() => {
    const el = document.querySelector('.sc-dots');
    if (el) el.textContent = '.'.repeat((n++ % 3) + 1);
  }, 500);
})();
</script>
</body>
</html>
