<?php
// --- Data Setup ---
// In a real application, this would come from a database.
// For this example, we'll use the manifest.json file.
$manifestPath = __DIR__ . '/templates/manifest.json';
$manifest = json_decode(@file_get_contents($manifestPath), true) ?: [];
$templates = $manifest['templates'] ?? [
    // --- Mock Data if manifest.json is empty ---
    [
        'id' => 'LI-PRD-RC-28A',
        'name' => 'Pellet Manufacturing (Anode)',
        'category' => 'Production',
        'tags' => ['Anode', 'Quality Control'],
        'pages' => 5,
        'updated' => '2025-10-12',
        'desc' => 'Log sheet for the entire anode pellet manufacturing process, from raw material to final inspection.'
    ],
    [
        'id' => 'LI-PRD-RC-13/2S4P',
        'name' => 'Stack Assembly',
        'category' => 'Assembly',
        'tags' => ['2S4P', 'Assembly Line'],
        'pages' => 8,
        'updated' => '2025-10-14',
        'desc' => 'Detailed assembly log for the 2S4P configuration battery stack.'
    ],
    [
        'id' => 'QA-FIN-RC-05B',
        'name' => 'Final QA Inspection',
        'category' => 'Quality',
        'tags' => ['Final Product', 'Inspection'],
        'pages' => 3,
        'updated' => '2025-09-28',
        'desc' => 'Final quality assurance checklist before product dispatch.'
    ]
];

// Helper function for safe HTML output
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Log Sheets — RES</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">

<style>
/* --- Material 3 Inspired Design Tokens (Variables) --- */
:root {
  /* FIX: Added transition for smoother theme changes */
  --theme-transition: background-color .3s ease, color .3s ease, border-color .3s ease;

  /* Primary Palette (RES Brand) */
  --md-primary: #1D2C78; --md-on-primary: #ffffff;
  --md-primary-container: #d9e2ff; --md-on-primary-container: #00006c;
  /* Secondary Palette */
  --md-secondary: #585e71; --md-on-secondary: #ffffff;
  --md-secondary-container: #dbe2f9; --md-on-secondary-container: #151b2c;
  /* Neutral Palette (Surfaces, Text) */
  --md-surface: #fcfcff; --md-on-surface: #1a1c22;
  --md-surface-container: #eff0f4; --md-surface-container-low: #f5f6fa;
  --md-surface-container-high: #e9eaee; --md-surface-container-highest: #e3e4e9;
  /* Outline & Border */
  --md-outline: #757780; --md-outline-variant: #c3c6d0;
  /* Others */
  --md-inverse-surface: #2f3036; --md-inverse-on-surface: #f1f0f7;
  --md-shadow: #000000;

  /* Radius & Elevation */
  --radius-sm: 8px; --radius-md: 12px; --radius-lg: 16px; --radius-full: 999px;
  --elevation-1: 0 1px 2px 0 rgba(0,0,0,0.08), 0 1px 3px 0 rgba(0,0,0,0.05);
  --elevation-2: 0 3px 6px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
  --elevation-3: 0 5px 10px rgba(0,0,0,0.12), 0 2px 4px rgba(0,0,0,0.08);
}

/* --- Dark Mode Overrides ---
  FIX: Switched from @media query to a data-theme attribute on <html>.
  This allows the JavaScript toggle to work reliably.
*/
html[data-theme="dark"] {
  --md-primary: #b1c6ff; --md-on-primary: #00277f;
  --md-primary-container: #003aae; --md-on-primary-container: #d9e2ff;
  --md-secondary: #bec6dc; --md-on-secondary: #2a3042;
  --md-secondary-container: #414659; --md-on-secondary-container: #dbe2f9;
  --md-surface: #1a1c22; --md-on-surface: #e3e2e9;
  --md-surface-container: #26282e; --md-surface-container-low: #15171c;
  --md-surface-container-high: #313339; --md-surface-container-highest: #3c3e44;
  --md-outline: #8e909a; --md-outline-variant: #45474e;
  --md-inverse-surface: #e3e2e9; --md-inverse-on-surface: #2f3036;
}

/* --- Base Styles --- */
*, *::before, *::after { box-sizing: border-box; }
html { font-size: 16px; }
body {
  margin: 0;
  background-color: var(--md-surface-container-low);
  color: var(--md-on-surface);
  font-family: "Inter", system-ui, -apple-system, sans-serif;
  font-size: 1rem;
  line-height: 1.5;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  transition: var(--theme-transition);
}

/* --- Typography & Icons --- */
.icon {
  font-family: 'Material Symbols Rounded', sans-serif; /* This must match the font name */
  font-weight: normal; font-style: normal; font-size: 24px;
  line-height: 1; letter-spacing: normal; text-transform: none;
  display: inline-block; white-space: nowrap; word-wrap: normal;
  direction: ltr; -webkit-font-feature-settings: 'liga'; -webkit-font-smoothing: antialiased;
  vertical-align: middle;
}
.icon.filled { font-variation-settings: 'FILL' 1; }

/* --- Buttons & Chips --- */
.btn, .chip {
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  cursor: pointer; text-decoration: none; border-radius: var(--radius-full);
  font-weight: 600; font-size: 0.875rem; padding: 10px 16px;
  border: 1px solid var(--md-outline-variant); background-color: transparent;
  color: var(--md-primary); transition: var(--theme-transition), transform .1s ease;
}
.btn:hover { background-color: var(--md-primary-container); border-color: var(--md-primary); }
.btn:active { transform: scale(0.97); }

.btn.primary {
  background-color: var(--md-primary); color: var(--md-on-primary);
  border-color: transparent; box-shadow: var(--elevation-1);
}
.btn.primary:hover { filter: brightness(1.1); box-shadow: var(--elevation-2); }

.icon-btn {
  width: 40px; height: 40px; border-radius: 50%; padding: 0;
  font-size: 22px; color: var(--md-on-surface);
  border: none; background-color: transparent;
}
.icon-btn:hover { background-color: var(--md-surface-container-high); }

/* --- Top Bar (Header) --- */
.topbar {
  background-color: var(--md-surface-container-low);
  border-bottom: 1px solid var(--md-outline-variant);
  padding: 12px 0; position: sticky; top: 0; z-index: 10;
  backdrop-filter: blur(8px);
}
.topbar-inner {
  max-width: 1200px; margin: 0 auto; padding: 0 24px;
  display: flex; align-items: center; gap: 16px;
}
.brand {
  display: flex; align-items: center; gap: 12px;
  text-decoration: none; color: inherit;
}
.logo {
  width: 40px; height: 40px; border-radius: var(--radius-md);
  background: var(--md-primary); color: var(--md-on-primary);
  display: grid; place-items: center; font-size: 1.2rem; font-weight: 700;
}
.title-group { line-height: 1.2; }
.title { font-size: 1.125rem; font-weight: 700; }
.subtitle { font-size: 0.8rem; color: var(--md-outline); }
.actions { margin-left: auto; display: flex; gap: 8px; align-items: center; }

/* --- Main Layout --- */
.wrap {
  width: 100%; max-width: 1200px;
  margin: 32px auto; padding: 0 24px;
  flex-grow: 1;
}

/* --- Toolbar (Search, Sort, Filters) --- */
.toolbar {
  display: flex; flex-wrap: wrap; gap: 12px; align-items: center;
  justify-content: space-between; margin-bottom: 24px;
}
.search-group { display: flex; align-items: center; gap: 10px; flex-grow: 1; min-width: 250px; }
.search-input {
  display: flex; align-items: center; gap: 10px; width: 100%;
  background-color: var(--md-surface-container);
  border: 1px solid var(--md-outline-variant);
  border-radius: var(--radius-full); padding: 10px 16px;
  box-shadow: var(--elevation-1);
}
.search-input input {
  flex: 1; border: none; outline: none; background: transparent;
  color: var(--md-on-surface); font: 500 1rem "Inter";
}
.search-input input::placeholder { color: var(--md-outline); }
.search-input:focus-within { border-color: var(--md-primary); box-shadow: 0 0 0 2px var(--md-primary-container); }

/* --- Filter Chips --- */
.filters { display: flex; gap: 8px; flex-wrap: wrap; margin: 16px 0; align-items: center; }
.filter-label { color: var(--md-on-surface); font-weight: 600; font-size: 0.875rem; }
.chip {
  padding: 8px 12px; height: 36px;
  background-color: var(--md-surface-container); color: var(--md-on-surface);
  border-color: var(--md-outline-variant);
}
.chip .icon { font-size: 18px; }
.chip[aria-pressed="true"] {
  background-color: var(--md-primary-container);
  color: var(--md-on-primary-container);
  border-color: transparent;
}
.chip[aria-pressed="true"] .icon { font-variation-settings: 'FILL' 1; }

/* --- Stats --- */
.stats { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; }
.stat { font-weight: 500; font-size: 0.875rem; color: var(--md-outline); }
.stat .count { font-weight: 700; color: var(--md-on-surface); }

/* --- Template List --- */
.view { display: grid; gap: 20px; }
.card {
  background-color: var(--md-surface-container);
  border: 1px solid var(--md-outline-variant);
  border-radius: var(--radius-lg);
  padding: 20px;
  display: grid;
  grid-template: "thumb content actions" auto / 56px 1fr auto; /* Grid areas and sizes */
  gap: 16px;
  align-items: center;
  transition: var(--theme-transition), box-shadow .2s ease;
}
.card:hover { border-color: var(--md-primary); box-shadow: var(--elevation-2); }

.thumb {
  grid-area: thumb;
  width: 56px; height: 56px; border-radius: var(--radius-md);
  background: var(--md-primary); color: var(--md-on-primary);
  display: grid; place-items: center; font-weight: 700; font-size: 1.5rem;
}
.content-area { grid-area: content; }
.card h3 { font-size: 1.25rem; margin: 0 0 4px; font-weight: 600; }
.meta { display: flex; flex-wrap: wrap; gap: 6px 16px; font-size: 0.8rem; color: var(--md-outline); }
.meta span { display: inline-flex; align-items: center; gap: 4px; }
.meta .icon { font-size: 16px; }

.card-actions {
  grid-area: actions;
  display: flex; gap: 8px; flex-wrap: nowrap;
}
.card-actions .btn { padding: 8px 14px; font-size: 0.8rem; } /* Smaller buttons in card */
.card-actions .btn .icon { font-size: 20px; }

/* --- Empty State --- */
.empty {
  border: 2px dashed var(--md-outline-variant);
  background-color: var(--md-surface-container);
  border-radius: var(--radius-lg); padding: 48px; text-align: center;
  color: var(--md-outline); font-size: 1rem;
}
.empty .icon { font-size: 48px; margin-bottom: 16px; color: var(--md-primary); display: block; }
.empty a { color: var(--md-primary); text-decoration: none; font-weight: 600; }

/* --- Footer --- */
footer {
  max-width: 1200px; margin: 48px auto 24px; padding: 0 24px;
  color: var(--md-outline); display: flex; justify-content: space-between;
  flex-wrap: wrap; gap: 12px; font-size: 0.8rem;
}
kbd {
  background: var(--md-surface-container-high); border: 1px solid var(--md-outline-variant);
  padding: 3px 8px; border-radius: 6px; font-family: 'Inter', sans-serif;
  font-weight: 600; font-size: 0.75rem; color: var(--md-on-surface);
}

/* --- Toast Notification --- */
.toast {
  position: fixed; left: 50%; bottom: 28px; transform: translateX(-50%);
  background-color: var(--md-inverse-surface); color: var(--md-inverse-on-surface);
  padding: 12px 18px; border-radius: var(--radius-md); box-shadow: var(--elevation-3);
  z-index: 999; font-weight: 500; font-size: 0.875rem; opacity: 0;
  transition: opacity .3s ease, transform .3s ease;
}

/* --- Responsive Adjustments --- */
@media (max-width: 768px) {
  .topbar-inner, .wrap, footer { padding-left: 16px; padding-right: 16px; }
  .toolbar { flex-direction: column; align-items: stretch; gap: 16px; }
  .card {
    grid-template: "thumb content" auto "actions actions" auto / 48px 1fr;
    row-gap: 20px;
    padding: 16px;
  }
  .thumb { width: 48px; height: 48px; font-size: 1.2rem; }
  .card-actions {
    justify-content: flex-start;
    padding-top: 16px; border-top: 1px solid var(--md-outline-variant);
  }
}
@media (max-width: 480px) {
  .subtitle { display: none; }
  .actions .btn:not(.primary) { display: none; } /* Hide Admin text button */
  .footer { flex-direction: column; align-items: center; text-align: center; }
}

</style>
</head>
<body>

<header class="topbar">
  <div class="topbar-inner">
    <a class="brand" href="/">
      <div class="logo" aria-hidden="true">RES</div>
      <div class="title-group">
        <div class="title">Log Sheets</div>
        <div class="subtitle">Renewable Energy Systems Limited</div>
      </div>
    </a>
    <div class="actions">
      <a class="btn" href="admin.php"><span class="icon">settings</span> Admin</a>
      <button class="icon-btn" id="themeToggle" title="Toggle theme"><span class="icon">dark_mode</span></button>
      <a class="btn primary" href="admin.php#new"><span class="icon filled">add</span> New Template</a>
    </div>
  </div>
</header>

<main class="wrap">
  <div class="toolbar">
    <div class="search-group">
      <div class="search-input">
        <span class="icon" aria-hidden="true">search</span>
        <input id="q" type="search" placeholder="Search templates (press / to focus)..." autocomplete="off">
      </div>
    </div>
    <div class="controls-group" id="filters">
      <div class="filter-label">Category:</div>
      </div>
  </div>

  <div class="stats">
    <div class="stat">Total Templates: <span class="count" id="countAll">0</span></div>
    <div class="stat">Showing: <span class="count" id="countVisible">0</span></div>
  </div>

  <div class="view" id="view">
    <?php if (empty($templates)): ?>
      <div class="empty" id="empty">
        <span class="icon filled" aria-hidden="true">info</span>
        <p>No templates created yet.</p>
        <p>Start by creating your first log sheet in the <a href="admin.php">Admin section</a>.</p>
      </div>
    <?php else: ?>
      <?php foreach($templates as $t):
        // --- Prepare data for each template ---
        $id = (string)($t['id'] ?? '');
        $name = (string)($t['name'] ?? $id);
        $category = (string)($t['category'] ?? 'General');
        $tags = $t['tags'] ?? [];
        $pages = (int)($t['pages'] ?? 0);
        $updated = (string)($t['updated'] ?? '');
        $desc = (string)($t['desc'] ?? '');
        $initials = strtoupper(substr(preg_replace('~[^A-Z]~','', $name),0,2) ?: 'LS');
        $tagStr = implode(',', array_map(fn($x)=> (string)$x, $tags));
      ?>
      <div class="card"
           data-name="<?= h($name) ?>"
           data-id="<?= h($id) ?>"
           data-category="<?= h($category) ?>"
           data-tags="<?= h($tagStr) ?>">

        <div class="thumb" aria-hidden="true"><?= h($initials) ?></div>

        <div class="content-area">
          <h3><?= h($name) ?></h3>
          <div class="meta">
            <span title="Template ID"><span class="icon" aria-hidden="true">qr_code_2</span><?= h($id) ?></span>
            <?php if ($pages>0): ?><span title="<?= (int)$pages ?> pages"><span class="icon" aria-hidden="true">description</span><?= (int)$pages ?> pages</span><?php endif; ?>
            <?php if ($updated): ?><span title="Last updated"><span class="icon" aria-hidden="true">update</span><?= h($updated) ?></span><?php endif; ?>
          </div>
        </div>

        <div class="card-actions">
          <button class="icon-btn star" title="Toggle favorite" data-id="<?= h($id) ?>"><span class="icon">star</span></button>
          <button class="btn" data-action="copy-id" data-id="<?= h($id) ?>"><span class="icon">content_copy</span> Copy ID</button>
          <a class="btn primary" href="run.php?id=<?= urlencode($id) ?>"><span class="icon">play_arrow</span> Open</a>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>

<footer>
  <div>Shortcuts: <kbd>/</kbd> search · <kbd>F</kbd> favorites</div>
  <div>© <?= date('Y') ?> Renewable Energy Systems</div>
</footer>

<script>
(() => {
  'use strict';
  const $ = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

  // --- Element References ---
  const view = $('#view');
  const q = $('#q');
  const themeToggle = $('#themeToggle');
  const filtersWrap = $('#filters');
  const countAll = $('#countAll');
  const countVisible = $('#countVisible');
  const cards = $$('.card');
  const emptyState = $('#empty');

  // --- State Management ---
  const PREFS_KEY = 'res-logs-prefs';
  const FAVS_KEY = 'res-logs-favs';
  let prefs = JSON.parse(localStorage.getItem(PREFS_KEY) || '{}');
  const favs = new Set(JSON.parse(localStorage.getItem(FAVS_KEY) || '[]'));

  // --- Theme Controller ---
  const themeController = {
    init() {
      const savedTheme = prefs.theme;
      const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (savedTheme) {
        this.setTheme(savedTheme);
      } else {
        this.setTheme(systemPrefersDark ? 'dark' : 'light');
      }
      themeToggle.addEventListener('click', () => this.toggleTheme());
    },
    setTheme(theme) {
      document.documentElement.setAttribute('data-theme', theme);
      $('#themeToggle .icon').textContent = theme === 'dark' ? 'light_mode' : 'dark_mode';
      prefs.theme = theme;
      localStorage.setItem(PREFS_KEY, JSON.stringify(prefs));
    },
    toggleTheme() {
      const currentTheme = document.documentElement.getAttribute('data-theme');
      this.setTheme(currentTheme === 'dark' ? 'light' : 'dark');
    }
  };

  // --- Filter & Render Controller ---
  const renderController = {
    init() {
      this.generateFilterChips();
      this.updateFavStars();
      this.render(); // Initial render
      q.addEventListener('input', () => this.render());
    },
    generateFilterChips() {
      const categories = new Set(['All', ...cards.map(c => c.dataset.category || 'General')]);
      const fragment = document.createDocumentFragment();
      categories.forEach(cat => {
        const chip = document.createElement('button');
        chip.className = 'chip';
        chip.textContent = cat;
        chip.dataset.category = cat;
        chip.setAttribute('aria-pressed', cat === 'All' ? 'true' : 'false');
        chip.addEventListener('click', () => {
          $$('[data-category]').forEach(c => c.setAttribute('aria-pressed', 'false'));
          chip.setAttribute('aria-pressed', 'true');
          this.render();
        });
        fragment.appendChild(chip);
      });
      filtersWrap.appendChild(fragment);
    },
    getActiveFilters() {
      const query = q.value.trim().toLowerCase();
      const activeCatChip = $('[data-category][aria-pressed="true"]');
      const category = (activeCatChip && activeCatChip.dataset.category !== 'All') ? activeCatChip.dataset.category : null;
      const onlyFavs = favs.size > 0 && ($('.chip[aria-pressed="true"][data-category="Favorites"]') !== null);
      return { query, category, onlyFavs };
    },
    render() {
      const { query, category } = this.getActiveFilters();
      let visibleCount = 0;
      cards.forEach(card => {
        const name = card.dataset.name.toLowerCase();
        const id = card.dataset.id.toLowerCase();
        const cardCategory = card.dataset.category;
        
        let isVisible = true;
        if (query && !(name.includes(query) || id.includes(query))) isVisible = false;
        if (category && cardCategory !== category) isVisible = false;

        card.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
      });
      countVisible.textContent = visibleCount;
      if (emptyState) emptyState.style.display = visibleCount === 0 ? '' : 'none';
    },
    updateFavStars() {
      $$('.star').forEach(s => {
        const isFav = favs.has(s.dataset.id);
        s.classList.toggle('is-favorite', isFav);
        s.querySelector('.icon').classList.toggle('filled', isFav);
      });
    }
  };

  // --- Event Delegation & Other Handlers ---
  function setupEventListeners() {
    document.body.addEventListener('click', (e) => {
      const copyBtn = e.target.closest('[data-action="copy-id"]');
      if (copyBtn) {
        navigator.clipboard.writeText(copyBtn.dataset.id).then(() => {
          toast('ID copied to clipboard!');
        }).catch(err => {
          toast('Could not copy ID.');
          console.error('Copy failed:', err);
        });
      }

      const starBtn = e.target.closest('.star');
      if (starBtn) {
        const id = starBtn.dataset.id;
        favs.has(id) ? favs.delete(id) : favs.add(id);
        localStorage.setItem(FAVS_KEY, JSON.stringify([...favs]));
        renderController.updateFavStars();
        if ($('.chip[data-category="Favorites"][aria-pressed="true"]')) {
            renderController.render();
        }
      }
    });

    window.addEventListener('keydown', (e) => {
      if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
        e.preventDefault();
        q.focus();
      }
    });
  }

  // --- Toast Notification ---
  function toast(msg) {
    const existing = $('.toast');
    if (existing) existing.remove();

    const t = document.createElement('div');
    t.className = 'toast';
    t.textContent = msg;
    document.body.appendChild(t);

    setTimeout(() => { t.style.opacity = 1; t.style.transform = 'translateX(-50%) translateY(-10px)'; }, 10);
    setTimeout(() => { t.style.opacity = 0; t.style.transform = 'translateX(-50%) translateY(0)'; }, 2000);
    setTimeout(() => t.remove(), 2300);
  }

  // --- App Initialization ---
  function init() {
    if (!cards.length && emptyState) {
        emptyState.style.display = '';
        $('.toolbar').style.display = 'none';
        $('.stats').style.display = 'none';
        return;
    }
    countAll.textContent = cards.length;
    themeController.init();
    renderController.init();
    setupEventListeners();
  }

  // Run the app
  init();

})();
</script>
</body>
</html>