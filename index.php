<?php
session_start();
include 'includes/functions.php';

// Session / auth
if (isset($_SESSION['LOGI_EMP_ID']) && $_SESSION['LOGI_EMP_ID'] != '') {
    $role = $_SESSION['LOGI_USER_ROLE_NAME'];
} else {
    echo "<script>window.location.href='login'</script>";
    exit;
}

// ---------------------------------
// Fetch templates from API instead of manifest.json
// ---------------------------------
$templates = [];
$api_url_full = $api_url . "/templates/list";

$raw = get_api_data($api_url_full, $api_key);
$resp = json_decode($raw, true);

if (is_array($resp) && isset($resp['status']) && $resp['status'] === 'success' && isset($resp['data']) && is_array($resp['data'])) {
    // Normalize each template row into what the UI expects
    foreach ($resp['data'] as $row) {
        $templates[] = [
            'id'       => $row['id'] ?? '',
            'name'     => $row['name'] ?? '',
            'file'     => $row['file'] ?? '',
            'desc'     => $row['description'] ?? '',
            'category' => $row['department'] ?? '',
            'rev_no'   => $row['rev_no'] ?? '',
            'updated'  => $row['updated_on'] ?? '',
            // pages doesn't exist in API; keep 0 so UI won't break
            'pages'    => 0,
        ];
    }
} else {
    // API failed or returned error → graceful fallback
    $templates = [];
    // you could also echo an error badge later in HTML if you want
}

// helper for escaping
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

?>
<!doctype html>
<html lang="en" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Log Sheets — RES</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">

<style>
:root{
  --theme-transition: background-color .3s ease, color .3s ease, border-color .3s ease;
  --md-primary:#1D2C78; --md-on-primary:#fff; --md-primary-container:#d9e2ff; --md-on-primary-container:#00006c;
  --md-secondary:#585e71; --md-on-secondary:#fff; --md-secondary-container:#dbe2f9; --md-on-secondary-container:#151b2c;
  --md-surface:#fcfcff; --md-on-surface:#1a1c22;
  --md-surface-container:#eff0f4; --md-surface-container-low:#f5f6fa; --md-surface-container-high:#e9eaee; --md-surface-container-highest:#e3e4e9;
  --md-outline:#757780; --md-outline-variant:#c3c6d0; --md-shadow:#000;
  --radius-sm:8px; --radius-md:12px; --radius-lg:16px; --radius-full:999px;
  --elevation-1:0 1px 2px rgba(0,0,0,.08),0 1px 3px rgba(0,0,0,.05);
  --elevation-2:0 3px 6px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06);
  --elevation-3:0 5px 10px rgba(0,0,0,.12),0 2px 4px rgba(0,0,0,.08);
}
html[data-theme="dark"]{
  --md-primary:#b1c6ff; --md-on-primary:#00277f; --md-primary-container:#003aae; --md-on-primary-container:#d9e2ff;
  --md-secondary:#bec6dc; --md-on-secondary:#2a3042; --md-secondary-container:#414659; --md-on-secondary-container:#dbe2f9;
  --md-surface:#1a1c22; --md-on-surface:#e3e2e9; --md-surface-container:#26282e; --md-surface-container-low:#15171c; --md-surface-container-high:#313339; --md-surface-container-highest:#3c3e44;
  --md-outline:#8e909a; --md-outline-variant:#45474e;
}
*,*:before,*:after{box-sizing:border-box}
body{margin:0;background:var(--md-surface-container-low);color:var(--md-on-surface);font-family:"Inter",system-ui,-apple-system,sans-serif;transition:var(--theme-transition)}
.icon{font-family:'Material Symbols Rounded';font-weight:normal;font-style:normal;line-height:1;display:inline-block;vertical-align:middle}
.icon.filled{font-variation-settings:'FILL' 1}
.topbar{position:sticky;top:0;background:var(--md-surface-container-low);border-bottom:1px solid var(--md-outline-variant);padding:12px 0;z-index:10}
.topbar-inner{max-width:1200px;margin:0 auto;padding:0 24px;display:flex;gap:16px;align-items:center}
.logo{width:40px;height:40px;border-radius:12px;background:var(--md-primary);color:#fff;display:grid;place-items:center;font-weight:700}
.title{font-weight:700}
.actions{margin-left:auto;display:flex;gap:8px}
.btn{display:inline-flex;gap:8px;align-items:center;border:1px solid var(--md-outline-variant);background:transparent;color:var(--md-primary);padding:10px 16px;border-radius:999px;text-decoration:none;font-weight:600}
.btn.primary{background:var(--md-primary);color:var(--md-on-primary);border-color:transparent;box-shadow:var(--elevation-1)}
.icon-btn{width:40px;height:40px;border-radius:50%;border:none;background:transparent}
.wrap{max-width:1200px;margin:32px auto;padding:0 24px}
.toolbar{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:24px}
.search-input{display:flex;gap:10px;align-items:center;background:var(--md-surface-container);border:1px solid var(--md-outline-variant);border-radius:999px;padding:10px 16px;box-shadow:var(--elevation-1);min-width:260px}
.search-input input{border:none;outline:none;background:transparent;font:500 1rem "Inter";color:var(--md-on-surface)}
.view{display:grid;gap:20px}
.card{background:var(--md-surface-container);border:1px solid var(--md-outline-variant);border-radius:16px;padding:20px;display:grid;grid-template:"thumb content actions" auto/56px 1fr auto;gap:16px;align-items:center}
.thumb{grid-area:thumb;width:56px;height:56px;border-radius:12px;background:var(--md-primary);color:#fff;display:grid;place-items:center;font-weight:700;font-size:1.5rem}
.content-area{grid-area:content}
.card-actions{grid-area:actions;display:flex;gap:8px}
.meta{display:flex;gap:12px;color:var(--md-outline);font-size:.85rem}

/* ---- Pre-initialize modal ---- */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;align-items:center;justify-content:center;z-index:1000}
.modal{width:min(860px,96vw);max-height:90vh;overflow:auto;background:var(--md-surface);border:1px solid var(--md-outline-variant);border-radius:16px;box-shadow:var(--elevation-3)}
.modal header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--md-outline-variant)}
.modal h3{margin:0;font-size:1.1rem}
.modal .body{padding:16px 20px}
.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.field{display:flex;flex-direction:column;gap:6px}
.field label{font-weight:600;font-size:.9rem}
.field input{border:1px solid var(--md-outline-variant);border-radius:10px;background:var(--md-surface-container);padding:10px 12px;font:500 0.95rem "Inter";outline:none}
.field input:invalid{border-color:#d33}
.req{color:#d33;margin-left:4px}
.modal footer{padding:16px 20px;border-top:1px solid var(--md-outline-variant);display:flex;gap:10px;justify-content:flex-end}
.badge{display:inline-flex;gap:6px;align-items:center;background:var(--md-surface-container-high);border:1px solid var(--md-outline-variant);padding:6px 10px;border-radius:999px;font-size:.8rem}
.help{color:var(--md-outline);font-size:.85rem;margin-top:8px}
.hidden{display:none !important}
</style>
</head>
<body>

<header class="topbar">
  <div class="topbar-inner">
    <div class="logo">RES</div>
    <div>
      <div class="title">Log Sheets</div>
      <div style="color:var(--md-outline);font-size:.85rem">Renewable Energy Systems Limited</div>
    </div>
    <div class="actions">
      <a class="btn" href="admin"><span class="icon">settings</span> Admin</a>
      <button class="icon-btn" id="themeToggle" title="Toggle theme"><span class="icon">dark_mode</span></button>
      <a class="btn primary" href="admin#new"><span class="icon">inventory_2</span> Saved Logs</a>
      <a class="btn primary" href="logout"><span class="icon">logout</span> Logout</a>
    </div>
  </div>
</header>

<main class="wrap">
  <div class="toolbar">
    <div class="search-input">
      <span class="icon" aria-hidden="true">search</span>
      <input id="q" type="search" placeholder="Search templates…" autocomplete="off">
    </div>
    <span class="badge"><span class="icon">info</span> Click <b>Initialize</b> to start a new log</span>
  </div>

  <div class="view" id="view">

    <?php if (empty($templates)): ?>
      <div style="color:var(--md-outline);font-size:.9rem;">
        <span class="icon">warning</span>&nbsp;No templates available.
      </div>
    <?php else: ?>

      <?php foreach ($templates as $t):
        $id        = (string)($t['id'] ?? '');
        $name      = (string)($t['name'] ?? $id);
        $file      = (string)($t['file'] ?? '');
        $category  = (string)($t['category'] ?? 'General');
        $pages     = (int)($t['pages'] ?? 0); // API didn't provide pages; will be 0
        $updated   = (string)($t['updated'] ?? '');
        $rev_no    = (string)($t['rev_no'] ?? '');
        $initials  = strtoupper(substr(preg_replace('~[^A-Z]~','', $name),0,2) ?: 'LS');

        // we'll treat LI-PRD-RC-28A as the special modal-trigger template
        $is_pellet = ($id === 'LI-PRD-RC-28A');
      ?>
      <div
        class="card"
        data-id="<?= h($id) ?>"
        data-name="<?= h($name) ?>"
        data-category="<?= h($category) ?>"
      >
        <div class="thumb"><?= h($initials) ?></div>

        <div class="content-area">
          <h3 style="margin:0 0 4px"><?= h($name) ?></h3>
          <div class="meta">
            <span><span class="icon">qr_code_2</span> <?= h($id) ?></span>
            <?php if ($rev_no !== ''): ?>
              <span><span class="icon">fact_check</span> <?= h($rev_no) ?></span>
            <?php endif; ?>
            <?php if($pages): ?>
              <span><span class="icon">description</span> <?= (int)$pages ?> pages</span>
            <?php endif; ?>
            <?php if($updated): ?>
              <span><span class="icon">update</span> <?= h($updated) ?></span>
            <?php endif; ?>
          </div>
          <?php if (!empty($t['desc'])): ?>
            <div style="font-size:.85rem; color:var(--md-outline); margin-top:6px;">
              <?= h($t['desc']) ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="card-actions">
          <?php if ($is_pellet): ?>
            <!-- Pellet Manufacturing (Anode) opens pre-init modal -->
            <button
              class="btn primary"
              data-init="pellet"
              data-id="<?= h($id) ?>"
              data-file="<?= h($file) ?>"
            >
              <span class="icon">play_arrow</span> Initialize
            </button>
          <?php else: ?>
            <!-- Others go straight to file -->
            <a
              class="btn primary"
              href="templates/<?= urlencode($file) ?>?id=<?= urlencode($id) ?>"
            >
              <span class="icon">play_arrow</span> Initialize
            </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>

    <?php endif; ?>

  </div>
</main>

<!-- Pre-initialize Modal -->
<div class="modal-backdrop" id="preModalBackdrop" aria-hidden="true">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="preTitle">
    <header>
      <h3 id="preTitle">Initialize: Pellet Manufacturing (Anode)</h3>
      <button class="icon-btn" id="preClose" aria-label="Close"><span class="icon">close</span></button>
    </header>
    <div class="body">
      <!-- IMPORTANT:
           We'll set form action dynamically in JS based on data-file from the clicked card -->
      <form id="preForm" method="get" action="" novalidate>
        <input type="hidden" name="id" value="LI-PRD-RC-28A">

        <div class="grid">
          <div class="field">
            <label>BATTERY CODE <span class="req">*</span></label>
            <input required name="battery_code" placeholder="e.g. ZZ" autocomplete="off">
          </div>
          <div class="field">
            <label>PID NO. <span class="req">*</span></label>
            <input required name="pid_no" placeholder="e.g. 046" autocomplete="off">
          </div>
          <div class="field">
            <label>BATTERY NO(s) <span class="req">*</span></label>
            <input required name="battery_no" placeholder="e.g. 010" autocomplete="off">
          </div>
          <div class="field">
            <label>I/P PRODUCT CODE <span class="req">*</span></label>
            <input required name="ip_code" placeholder="e.g. ANP-35-ZZ" autocomplete="off">
          </div>
          <div class="field">
            <label>I/P PRODUCT LOT NO. <span class="req">*</span></label>
            <input required name="ip_lot" placeholder="e.g. DDMMYY-XXX" autocomplete="off">
          </div>
          <div class="field">
            <label>WEIGHT (g) <span class="req">*</span></label>
            <input required name="ip_weight" placeholder="e.g. 30" inputmode="decimal" autocomplete="off">
          </div>
          <div class="field">
            <label>PELLET WT. RANGE AS PER PID (g) <span class="req">*</span></label>
            <input required name="pellet_weight_range" autocomplete="off" placeholder="e.g. 0.32 – 0.40">
          </div>
          <div class="field">
            <label>PELLET DIA (mm) (D) <span class="req">*</span></label>
            <input required name="pellet_dia" placeholder="e.g. 30" inputmode="decimal" autocomplete="off">
          </div>
          <div class="field">
            <label>PELLET THK. RANGE AS PER PID (mm) <span class="req">*</span></label>
            <input required name="pellet_thk_range" autocomplete="off" placeholder="e.g. 0.24 – 0.28">
          </div>
          <div class="field">
            <label>PRESSURE (kg/cm²) <span class="req">*</span></label>
            <input required name="pressure" placeholder="e.g. 175" inputmode="decimal" autocomplete="off">
          </div>
          <div class="field">
            <label>COMPRESSION TIME (sec) <span class="req">*</span></label>
            <input required name="compression" placeholder="e.g. 06" inputmode="numeric" autocomplete="off">
          </div>
          <div class="field">
            <label>Standard MH <span class="req">*</span></label>
            <input required name="standard_mh" inputmode="numeric" autocomplete="off" placeholder="e.g. 90 min">
          </div>
        </div>

        <div class="help">Tip: values like ranges can include a hyphen (e.g., “0.24 - 0.28”).</div>
      </form>
    </div>
    <footer>
      <button class="btn" id="preCancel"><span class="icon"></span> Cancel</button>
      <button class="btn primary" id="preSubmit" form="preForm" type="submit" disabled>
        <span class="icon"></span> Submit
      </button>
    </footer>
  </div>
</div>

<script>
(() => {
  const $  = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

  // Theme toggle
  const themeToggle = $('#themeToggle');
  themeToggle.addEventListener('click', () => {
    const cur  = document.documentElement.getAttribute('data-theme') || 'light';
    const next = (cur === 'dark') ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    $('#themeToggle .icon').textContent = (next === 'dark') ? 'light_mode' : 'dark_mode';
  });

  // Search filter
  const q = $('#q');
  q.addEventListener('input', () => {
    const term = q.value.trim().toLowerCase();
    $$('.card').forEach(card => {
      const hay = (
        (card.dataset.name || '') + ' ' +
        (card.dataset.id || '')   + ' ' +
        (card.dataset.category || '')
      ).toLowerCase();
      card.style.display = hay.includes(term) ? '' : 'none';
    });
  });

  // ---- Pre-init modal logic ----
  const modal      = $('#preModalBackdrop');
  const preForm    = $('#preForm');
  const preSubmit  = $('#preSubmit');
  const preCancel  = $('#preCancel');
  const preClose   = $('#preClose');
  const preTitle   = $('#preTitle');

  function openModal() {
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden','false');
  }
  function closeModal() {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden','true');
  }

  // Click handler for Initialize buttons that need modal
  document.body.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-init="pellet"]');
    if (!btn) return;

    e.preventDefault();

    // get template meta from button
    const tmplId   = btn.getAttribute('data-id')   || 'LI-PRD-RC-28A';
    const tmplFile = btn.getAttribute('data-file') || 'pellet_manufacturing_anode.php';

    // reset form
    preForm.reset();
    preSubmit.disabled = true;

    // update hidden ID so the template sees its own ID
    // and set action to correct file from API
    preForm.querySelector('input[name="id"]').value = tmplId;
    preForm.setAttribute('action', 'templates/' + tmplFile);

    // update modal title dynamically using the card title if we can find it
    const card = btn.closest('.card');
    const h3   = card ? card.querySelector('.content-area h3') : null;
    const niceName = h3 ? h3.textContent.trim() : 'Initialize';
    preTitle.textContent = 'Initialize: ' + niceName;

    openModal();
  });

  // Close actions
  preCancel.addEventListener('click', (e)=>{ e.preventDefault(); closeModal(); });
  if (preClose) {
    preClose.addEventListener('click', (e)=>{ e.preventDefault(); closeModal(); });
  }
  modal.addEventListener('click', (e)=>{ if(e.target === modal) closeModal(); });

  // Enable submit only when all required fields are filled
  function validateForm(){
    const allFilled = $$('#preForm [required]').every(inp => String(inp.value).trim() !== '');
    preSubmit.disabled = !allFilled;
  }
  preForm.addEventListener('input', validateForm);

})();
</script>

</body>
</html>
