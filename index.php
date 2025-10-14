<?php
$manifestPath = __DIR__ . '/templates/manifest.json';
$manifest = json_decode(@file_get_contents($manifestPath), true) ?: [];
$templates = $manifest['templates'] ?? [];

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Log Sheets — RES</title>

<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Rounded:FILL,GRAD,opsz,wght@0,0,24,500;1,0,24,500" rel="stylesheet">

<style>
/* --- Material 3 Inspired Design Tokens (Variables) --- */
:root{
  /* Primary Palette (RES Brand) */
  --md-primary: #1D2C78; /* Deep Blue */
  --md-on-primary: #ffffff;
  --md-primary-container: #d9e2ff; /* Lighter Blue */
  --md-on-primary-container: #00006c;

  /* Secondary Palette */
  --md-secondary: #585e71;
  --md-on-secondary: #ffffff;
  --md-secondary-container: #dbe2f9;
  --md-on-secondary-container: #151b2c;

  /* Tertiary Palette */
  --md-tertiary: #715573;
  --md-on-tertiary: #ffffff;
  --md-tertiary-container: #fbdafc;
  --md-on-tertiary-container: #2a132d;

  /* Neutral Palette (Surfaces, Text) */
  --md-surface-dim: #dbdce6;
  --md-surface: #fcfcff; /* Lightest background */
  --md-surface-bright: #fcfcff;
  --md-on-surface: #1a1c22;
  --md-surface-container-lowest: #ffffff;
  --md-surface-container-low: #f5f6fa;
  --md-surface-container: #eff0f4; /* Default card background */
  --md-surface-container-high: #e9eaee;
  --md-surface-container-highest: #e3e4e9;

  /* Outline & Border */
  --md-outline: #757780;
  --md-outline-variant: #c3c6d0;

  /* Error */
  --md-error: #ba1a1a;
  --md-on-error: #ffffff;
  --md-error-container: #ffdad6;
  --md-on-error-container: #410002;

  /* Additional States */
  --md-shadow: #000000;
  --md-inverse-surface: #2f3036;
  --md-inverse-on-surface: #f1f0f7;
  --md-inverse-primary: #b1c6ff;

  /* Component specific */
  --md-chip-text: var(--md-on-surface);
  --md-link: var(--md-primary);

  /* Radius */
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;

  /* Shadows for elevation */
  --elevation-1: 0 1px 2px 0 rgba(0,0,0,0.08), 0 1px 3px 0 rgba(0,0,0,0.05);
  --elevation-2: 0 3px 6px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
  --elevation-3: 0 5px 10px rgba(0,0,0,0.12), 0 2px 4px rgba(0,0,0,0.08);
  --elevation-4: 0 8px 16px rgba(0,0,0,0.15), 0 3px 6px rgba(0,0,0,0.09);
  --elevation-5: 0 12px 24px rgba(0,0,0,0.2), 0 4px 8px rgba(0,0,0,0.12);

  color-scheme: light; /* Default to light mode */
}

/* --- Dark Mode Overrides --- */
@media (prefers-color-scheme: dark){
  :root{
    --md-primary: #b1c6ff;
    --md-on-primary: #00277f;
    --md-primary-container: #003aae;
    --md-on-primary-container: #d9e2ff;

    --md-secondary: #bec6dc;
    --md-on-secondary: #2a3042;
    --md-secondary-container: #414659;
    --md-on-secondary-container: #dbe2f9;

    --md-tertiary: #deaeff;
    --md-on-tertiary: #412842;
    --md-tertiary-container: #583e5a;
    --md-on-tertiary-container: #fbdafc;

    --md-surface-dim: #1a1c22;
    --md-surface: #1a1c22;
    --md-surface-bright: #41434a;
    --md-on-surface: #e3e2e9;
    --md-surface-container-lowest: #15171c;
    --md-surface-container-low: #222429;
    --md-surface-container: #26282e;
    --md-surface-container-high: #313339;
    --md-surface-container-highest: #3c3e44;

    --md-outline: #8e909a;
    --md-outline-variant: #45474e;

    --md-error: #ffb4ab;
    --md-on-error: #690005;
    --md-error-container: #93000a;
    --md-on-error-container: #ffdad6;

    --md-shadow: #000000;
    --md-inverse-surface: #e3e2e9;
    --md-inverse-on-surface: #2f3036;
    --md-inverse-primary: #1D2C78;

    --md-chip-text: var(--md-on-surface);
    --md-link: var(--md-primary);

    color-scheme: dark;
  }
}

/* --- Base Styles --- */
*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0;
  background:var(--md-surface-container-low); /* Slightly darker than card for contrast */
  color:var(--md-on-surface);
  font:15px/1.5 "Inter", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
  min-height: 100vh;
  display: flex; flex-direction: column;
}

/* --- Typography & Icons --- */
.icon{
  font-family:"Material Symbols Rounded";
  line-height:1;
  font-size:1.25em; /* Adjust relative to parent font-size */
  font-variation-settings:'FILL' 0, 'GRAD' 0, 'opsz' 24, 'wght' 500;
  vertical-align: middle;
}
.icon-filled{font-variation-settings:'FILL' 1, 'GRAD' 0, 'opsz' 24, 'wght' 500;}

h1,h2,h3,h4,h5,h6{margin-top:0;}

/* --- Buttons (Material 3 styles) --- */
.btn, .icon-btn, .chip, .a-btn{
  display:inline-flex; align-items:center; justify-content:center; gap:8px; cursor:pointer; text-decoration:none;
  transition: all .2s ease;
  font-weight:600;
  border-radius:var(--radius-sm);
  font-size: 14px;
}

/* ElevatedButton */
.btn.primary, .a-btn.primary{
  background:var(--md-primary);
  color:var(--md-on-primary);
  border:none;
  padding:10px 18px;
  box-shadow: var(--elevation-1);
}
.btn.primary:hover, .a-btn.primary:hover{
  background: var(--md-primary); /* Keep base color */
  box-shadow: var(--elevation-2); /* Elevate more on hover */
  filter: brightness(1.1); /* Slight brightness change for interaction */
}
.btn.primary:active, .a-btn.primary:active{
  box-shadow: var(--elevation-1);
  filter: brightness(0.9);
}

/* OutlinedButton */
.btn, .a-btn{
  background:transparent;
  color:var(--md-primary);
  border:1px solid var(--md-outline-variant);
  padding:9px 17px;
}
.btn:hover, .a-btn:hover{
  background:var(--md-primary-container);
  color:var(--md-on-primary-container);
  border-color:var(--md-primary);
}
.btn:active, .a-btn:active{
  background:var(--md-primary-container);
  color:var(--md-on-primary-container);
}


/* IconButton (Toggle/Standard) */
.icon-btn{
  width:40px; height:40px;
  background:transparent;
  color:var(--md-on-surface);
  border:none;
  border-radius:50%; /* Circular for icon buttons */
  font-size: 20px; /* Base icon size */
}
.icon-btn:hover{background:var(--md-surface-container-high);}
.icon-btn:active{background:var(--md-surface-container-highest);}

/* Toggle IconButton (e.g., Favorites, Theme) */
.icon-btn[aria-pressed="true"]{
  background:var(--md-primary-container);
  color:var(--md-on-primary-container);
}
.icon-btn[aria-pressed="true"]:hover{
  background:var(--md-primary-container);
  filter: brightness(1.05);
}

/* --- Chips --- */
.chip{
  background:var(--md-surface-container-high);
  color:var(--md-on-surface);
  border:1px solid var(--md-outline-variant);
  padding:6px 12px;
  font-weight:500;
  font-size:13px;
  height: 36px; /* Consistent height */
}
.chip .icon{font-size:1.1em;}
.chip:hover{
  background:var(--md-surface-container-highest);
  border-color:var(--md-outline);
}

/* Filter Chip (Selected state) */
.chip[aria-pressed="true"]{
  background:var(--md-primary-container);
  color:var(--md-on-primary-container);
  border-color:var(--md-primary);
}
.chip[aria-pressed="true"]:hover{
  background:var(--md-primary-container);
  filter: brightness(1.05);
}


/* --- Top Bar (App Bar) --- */
.topbar{
  background:var(--md-surface-container-low); /* Slightly darker than cards */
  border-bottom:1px solid var(--md-outline-variant);
  padding: 8px 0;
  box-shadow: var(--elevation-1);
}
.topbar-inner{
  max-width:1280px; margin:0 auto; padding:0 24px;
  display:flex; align-items:center; gap:20px;
}
.brand{
  display:flex; align-items:center; gap:12px;
  text-decoration: none; color: inherit;
  padding: 4px 0; /* Align vertically with buttons */
}
.logo{
  width:36px; height:36px; border-radius:var(--radius-sm);
  background:linear-gradient(135deg,var(--md-primary),#2e4ba4); /* Brand gradient */
  display:grid; place-items:center; font-size: 19px; color: #fff; font-weight: 700;
}
.title-group{line-height:1.2}
.title{font-size:18px; font-weight:700}
.subtitle{font-size:12px; color:var(--md-outline); margin-top:2px; font-weight:500}
.actions{margin-left:auto; display:flex; gap:8px; flex-wrap:wrap; align-items:center}


/* --- Main Container --- */
.wrap{
  max-width:1280px; margin:28px auto; padding:0 24px;
  flex-grow: 1;
}

/* --- Toolbar (Search & Controls) --- */
.toolbar{
  display:flex; flex-wrap: wrap; gap:12px; align-items:center; margin-bottom:24px;
}
.search-input-group{
  flex-grow: 1; min-width: 250px;
  display: flex; gap: 8px; /* Gap for the sort select */
}
.search{
  flex-grow: 1;
  display:flex; align-items:center; gap:10px;
  background:var(--md-surface-container); /* Slightly elevated look for search */
  border:1px solid var(--md-outline-variant);
  border-radius:var(--radius-sm);
  padding:10px 12px;
  box-shadow: var(--elevation-1);
}
.search input{
  flex:1; border:none; outline:none; background:transparent;
  color:var(--md-on-surface); font:500 15px/1 "Inter";
}
.search input::placeholder{color:var(--md-outline);}

.select{position:relative; z-index:1;}
.select select{
  appearance:none;
  background:var(--md-surface-container);
  color:var(--md-on-surface);
  border:1px solid var(--md-outline-variant);
  padding:10px 38px 10px 14px;
  border-radius:var(--radius-sm);
  font-weight:500;
  cursor:pointer;
  font-size: 14px;
  height: 44px; /* Match search input height */
}
.select:after{
  content:"expand_more"; font-family:"Material Symbols Rounded"; position:absolute; right:12px; top:50%;
  transform:translateY(-50%); font-size:20px; color:var(--md-outline); pointer-events: none;
}

.controls-group{
  display: flex; gap: 8px; flex-wrap: wrap;
}

/* --- Filters (Category & Tags) --- */
.filters{display:flex; gap:8px; flex-wrap:wrap; margin:10px 0 16px; align-items:center;}
.filter-label{
  color:var(--md-on-surface); font-weight:600; font-size:14px;
  padding: 6px 4px; /* Match vertical padding of chips */
}

/* --- Stats --- */
.stats{display:flex; gap:16px; flex-wrap:wrap; margin:0 0 24px}
.stat{
  background:var(--md-surface-container);
  border:1px solid var(--md-outline-variant);
  padding:8px 16px;
  border-radius:999px; /* Pill shape */
  font-weight:500; font-size:14px;
  display: flex; align-items: center; gap: 8px;
  color:var(--md-on-surface); /* Main text color for numbers */
}
.stat .icon{color:var(--md-outline); font-size: 1.1em;} /* Muted icon */
.stat .count{font-weight:700; color:var(--md-primary);} /* Emphasize numbers */

/* --- Views (Grid/List) --- */
.view{display:grid; gap:20px;}
.view.grid{grid-template-columns:repeat(auto-fill,minmax(320px,1fr));}
.view.list{grid-template-columns:1fr;}

/* --- Card / Row (Template Item) --- */
.card{
  background:var(--md-surface-container);
  border:1px solid var(--md-outline-variant);
  border-radius:var(--radius-lg);
  padding:16px;
  box-shadow:var(--elevation-1);
  display:grid; grid-template-columns:auto 1fr auto; /* Thumb | Content | Actions */
  gap:16px; align-items:center;
  transition: border-color .2s ease, box-shadow .2s ease;
}
.card:hover{
  border-color:var(--md-primary);
  box-shadow:var(--elevation-2);
}

.thumb{
  width:56px; height:56px; border-radius:var(--radius-md);
  background:linear-gradient(135deg,var(--md-primary),#2e4ba4);
  display:grid; place-items:center; color:#fff; font-weight:700; font-size:22px;
  flex-shrink: 0;
}
.card h3{
  font-size:18px; margin:0 0 4px 0; font-weight:600;
  color:var(--md-on-surface);
}
.meta{display:flex; flex-wrap:wrap; gap:8px 12px; font-size:13px; color:var(--md-outline);}
.meta .icon{font-size:16px; color:var(--md-outline);}
.meta span{display:inline-flex; align-items:center; gap:4px;} /* Align icon with text */

.description{
  color:var(--md-on-surface); /* More readable in list view */
  font-size:14px;
  margin-top:8px;
  line-height:1.4;
}

.card-actions{
  display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; align-items:center;
  margin-top: -8px; /* Pull actions up slightly to align better with description */
}
.card-actions .a-btn{padding:8px 14px; font-size:13px;}

/* Star / Favorite button */
.star{
  width:40px; height:40px; border-radius:50%; /* Circular */
  display:grid; place-items:center;
  background:transparent; color:var(--md-outline); border:none;
  cursor:pointer;
  transition: background .2s ease, color .2s ease, font-variation-settings .2s ease;
  flex-shrink: 0; /* Prevent shrinking */
  margin-right:4px; /* Space from other buttons */
}
.star .icon{font-size:20px;}
.star:hover{background:var(--md-surface-container-high);}
.star[data-active="true"]{
  background:var(--md-primary-container);
  color:var(--md-primary);
}
.star[data-active="true"] .icon{
  font-variation-settings:'FILL' 1, 'GRAD' 0, 'opsz' 24, 'wght' 500;
  color:var(--md-primary); /* Keep icon filled color consistent */
}
.star[data-active="true"]:hover{
  background:var(--md-primary-container);
  filter: brightness(1.05);
}


/* Grid variant adjustments */
.view.grid .card{
  grid-template-columns:1fr; /* Stack elements vertically */
  align-items: flex-start; /* Align content to top */
  padding-bottom: 20px; /* More space at bottom */
}
.view.grid .card-header-area{
  display:flex; gap:16px; align-items:center;
  margin-bottom: 12px;
}
.view.grid .thumb{
  /* No specific grid position needed, just part of flex flow */
}
.view.grid .content-area{
  flex-grow: 1; /* Allow content to take space */
}
.view.grid .meta{
  margin-top: 8px; /* Space between title and meta */
}
.view.grid .description{
  margin-top: 12px;
  color:var(--md-outline); /* Mute description slightly in grid */
}
.view.grid .card-actions{
  justify-content: flex-start; /* Align actions left */
  margin-top: 16px; /* Space from description */
}
.view.grid .card-actions .star{
  order: -1; /* Place star first in grid actions */
  margin-right: 8px;
  margin-left: 0;
}


/* Empty state */
.empty{
  border:1px dashed var(--md-outline-variant);
  background:var(--md-surface-container);
  border-radius:var(--radius-lg);
  padding:40px;
  text-align:center;
  color:var(--md-outline);
  font-size: 16px;
  box-shadow:var(--elevation-1);
}
.empty .icon{
  font-size: 48px;
  margin-bottom: 16px;
  color: var(--md-primary);
  font-variation-settings:'FILL' 1, 'GRAD' 0, 'opsz' 48, 'wght' 500;
  display: block; /* Center icon */
}
.empty p{margin:0 0 8px 0;}
.empty p:last-child{margin-bottom:0;}
.empty a{
  color:var(--md-primary);
  text-decoration: none;
  font-weight: 600;
  transition: color .2s ease;
}
.empty a:hover{color:var(--md-primary-container);}


/* Footer */
footer{
  max-width:1280px; margin:32px auto 24px; padding:0 24px;
  color:var(--md-outline);
  display:flex; justify-content:space-between; flex-wrap:wrap; gap:12px;
  font-size: 13px;
}
kbd{
  background:var(--md-surface-container-high);
  border:1px solid var(--md-outline-variant);
  padding:3px 8px;
  border-radius:6px; font-family:'Inter', sans-serif;
  font-weight: 600; font-size: 12px; color: var(--md-on-surface);
}

/* --- Responsive Adjustments --- */
@media (max-width: 960px) {
  .topbar-inner, .wrap, footer{padding-left: 16px; padding-right: 16px;}
  .toolbar{flex-direction: column; align-items: stretch;}
  .search-input-group{flex-direction: column; gap: 10px;}
  .controls-group{justify-content: center;}
  .select{width: 100%;}
  .select select{width: 100%;}
}

@media (max-width: 600px) {
  .topbar-inner{gap: 12px;}
  .brand{gap: 8px;}
  .logo{width:32px; height:32px; font-size:18px;}
  .title-group .title{font-size:16px;}
  .title-group .subtitle{display: none;} /* Hide subtitle on very small screens */
  .actions{gap: 4px;}
  .btn, .a-btn{padding: 8px 12px; font-size: 13px;}
  .icon-btn{width:36px; height:36px; font-size:18px;}

  .wrap{margin-top: 20px;}
  .filters{flex-direction: column; align-items: flex-start;}
  .filter-label{margin-bottom: 4px; padding-left: 0;}
  .controls-group{width: 100%; justify-content: space-around;}

  .card{
    grid-template-columns: 48px 1fr; /* Thumb | Content (actions below) */
    align-items: flex-start;
    gap: 12px;
    padding: 14px;
  }
  .thumb{width:48px; height:48px; font-size:20px; margin-top: 4px;} /* Adjust thumb size */
  .card h3{font-size:17px;}
  .card .meta{gap: 6px 10px; font-size:12px;}
  .card .meta .icon{font-size:14px;}
  .description{font-size:13px; margin-top:6px;}

  .card-actions{
    grid-column: 1 / -1; /* Span full width below content */
    justify-content: flex-start;
    margin-top: 12px;
    border-top: 1px solid var(--md-outline-variant); /* Separator line */
    padding-top: 12px;
  }
  .card-actions .star{margin-left: 0; margin-right: 8px;}

  .view.grid .card{padding-bottom: 16px;}
  .view.grid .card-header-area{flex-direction: column; align-items: flex-start; gap: 8px; margin-bottom: 8px;}
  .view.grid .thumb{margin-top: 0;}
  .view.grid .card-actions{justify-content: flex-start;}
  .view.grid .card-actions .star{order: -1;}

  footer{flex-direction: column; align-items: center; text-align: center;}
}
</style>
</head>
<body>

<div class="topbar">
  <div class="topbar-inner">
    <a class="brand" href="/">
      <div class="logo" aria-hidden="true">RES</div>
      <div class="title-group">
        <div class="title">Log Sheets</div>
        <div class="subtitle">Simple & Professional</div>
      </div>
    </a>
    <div class="actions">
      <a class="btn" href="admin.php"><span class="icon">settings</span> Admin</a>
      <button class="icon-btn" id="themeToggle" title="Toggle theme"><span class="icon">dark_mode</span></button>
      <a class="btn primary" href="admin.php#new"><span class="icon icon-filled">add</span> New Template</a>
    </div>
  </div>
</div>

<div class="wrap">
  <div class="toolbar">
    <div class="search-input-group">
      <div class="search">
        <span class="icon" aria-hidden="true">search</span>
        <input id="q" type="search" placeholder="Search templates (press / to focus)..." autocomplete="off">
      </div>
      <div class="select" title="Sort options">
        <select id="sort" aria-label="Sort templates by">
          <option value="name_asc">Name (A→Z)</option>
          <option value="name_desc">Name (Z→A)</option>
          <option value="updated_desc">Recently Updated</option>
          <option value="updated_asc">Oldest First</option>
        </select>
      </div>
    </div>
    <div class="controls-group">
      <button class="chip" id="viewToggle" aria-pressed="false" title="Toggle view layout"><span class="icon">view_list</span> View</button>
      <button class="chip" id="showFavs" aria-pressed="false" title="Filter by favorite templates"><span class="icon">star</span> Favorites</button>
      <button class="chip" id="clearFilters" title="Clear all active filters"><span class="icon">filter_alt_off</span> Clear Filters</button>
    </div>
  </div>

  <div class="filters" id="filters">
    <div class="filter-label">Categories:</div>
    </div>

  <div class="stats">
    <div class="stat"><span class="icon">inventory_2</span> Total: <span class="count" id="countAll">0</span></div>
    <div class="stat"><span class="icon">visibility</span> Showing: <span class="count" id="countVisible">0</span></div>
  </div>

  <div class="view list" id="view">
    <?php if (empty($templates)): ?>
      <div class="empty" id="empty">
        <span class="icon icon-filled" aria-hidden="true">info</span>
        <p>No templates created yet.</p>
        <p>Start by creating your first log sheet in the <a href="admin.php">Admin section</a>.</p>
      </div>
    <?php else: ?>
      <?php foreach($templates as $t):
        $id = (string)($t['id'] ?? '');
        $name = (string)($t['name'] ?? $id);
        $category = (string)($t['category'] ?? 'General');
        $tags = $t['tags'] ?? [];
        $pages = (int)($t['pages'] ?? 0);
        $updated = (string)($t['updated'] ?? '');
        $desc = (string)($t['desc'] ?? '');
        $initials = strtoupper(substr(preg_replace('~[^A-Za-z]~','', $name),0,2) ?: 'LS');
        $tagStr = implode(',', array_map(fn($x)=> (string)$x, $tags));
      ?>
      <div class="card"
           data-name="<?= h($name) ?>"
           data-id="<?= h($id) ?>"
           data-category="<?= h($category) ?>"
           data-tags="<?= h($tagStr) ?>"
           data-updated="<?= h($updated) ?>">

        <div class="thumb" aria-hidden="true"><?= h($initials) ?></div>

        <div class="content-area">
          <h3><?= h($name) ?></h3>
          <div class="meta">
            <span title="Template ID"><span class="icon" aria-hidden="true">qr_code_2</span><?= h($id) ?></span>
            <span title="Category"><span class="icon" aria-hidden="true">category</span><?= h($category ?: 'General') ?></span>
            <?php if ($pages>0): ?><span title="<?= (int)$pages ?> pages"><span class="icon" aria-hidden="true">description</span><?= (int)$pages ?> pages</span><?php endif; ?>
            <?php if ($updated): ?><span title="Last updated"><span class="icon" aria-hidden="true">update</span><?= h($updated) ?></span><?php endif; ?>
          </div>
          <?php if($desc): ?><div class="description"><?= h($desc) ?></div><?php endif; ?>
        </div>

        <div class="card-actions">
          <button class="star icon-btn" title="Toggle favorite" data-id="<?= h($id) ?>"><span class="icon">star</span></button>
          <a class="a-btn primary" href="run.php?id=<?= urlencode($id) ?>"><span class="icon">play_arrow</span> Open</a>
          <a class="a-btn" href="run.php?id=<?= urlencode($id) ?>&preview=1"><span class="icon">visibility</span> Preview</a>
          <button class="a-btn" data-id="<?= h($id) ?>"><span class="icon">content_copy</span> Copy ID</button>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<footer>
  <div>Shortcuts: <kbd>/</kbd> search · <kbd>G</kbd> then <kbd>L</kbd> grid/list · <kbd>F</kbd> favorites</div>
  <div>© <?= date('Y') ?> Renewable Energy Systems</div>
</footer>

<script>
(() => {
  const $ = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));
  const view = $('#view');
  const q = $('#q');
  const sortSel = $('#sort');
  const viewToggle = $('#viewToggle');
  const showFavsBtn = $('#showFavs');
  const clearBtn = $('#clearFilters');
  const filtersWrap = $('#filters');
  const countAll = $('#countAll');
  const countVisible = $('#countVisible');
  const themeToggle = $('#themeToggle');
  const cards = $$('.card');
  const emptyState = $('#empty');

  const PREFS_KEY = 'res-logs-prefs';
  const FAVS_KEY = 'res-logs-favs';
  const prefs = JSON.parse(localStorage.getItem(PREFS_KEY) || '{}');
  const favs = new Set(JSON.parse(localStorage.getItem(FAVS_KEY) || '[]'));

  // --- Initial Setup ---
  countAll.textContent = cards.length;
  updateFavStars();

  // Restore view preference
  if (prefs.view === 'grid') {
    view.classList.remove('list'); view.classList.add('grid');
    viewToggle.setAttribute('aria-pressed','true');
    viewToggle.innerHTML = `<span class="icon">view_module</span> View`;
  }
  // Restore theme preference
  if (prefs.theme) document.documentElement.style.colorScheme = prefs.theme;
  else if (window.matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.style.colorScheme = 'dark';
  else document.documentElement.style.colorScheme = 'light';

  // --- Theme Toggle ---
  themeToggle.addEventListener('click', () => {
    const dark = document.documentElement.style.colorScheme === 'dark';
    document.documentElement.style.colorScheme = dark ? 'light' : 'dark';
    prefs.theme = dark ? 'light' : 'dark';
    localStorage.setItem(PREFS_KEY, JSON.stringify(prefs));
  });

  // --- Filter Chip Generation ---
  const cats = new Set(), tags = new Set();
  cards.forEach(c => {
    const cat = (c.dataset.category || 'General').trim();
    if (cat) cats.add(cat);
    (c.dataset.tags || '').split(',').map(s=>s.trim()).filter(Boolean).forEach(t => tags.add(t));
  });

  const mkChip = (label, type) => {
    const b = document.createElement('button');
    b.className = 'chip'; b.textContent = label; b.dataset.type = type; b.dataset.value = label; b.setAttribute('aria-pressed','false');
    b.addEventListener('click', () => {
      // Logic: Only one Category chip can be pressed, multiple Tag chips can be pressed.
      if (type === 'cat') {
        // Find currently active category chip
        const currentActiveCat = $('.chip[data-type="cat"][aria-pressed="true"]');
        if (currentActiveCat && currentActiveCat === b) {
          // If the same chip is clicked, unpress it (clearing category filter)
          b.setAttribute('aria-pressed', 'false');
        } else {
          // Unpress all other category chips
          $$('.chip[data-type="cat"]').forEach(x => x.setAttribute('aria-pressed','false'));
          // Press the clicked chip
          b.setAttribute('aria-pressed', 'true');
        }
      } else { // type === 'tag'
        b.setAttribute('aria-pressed', b.getAttribute('aria-pressed') === 'true' ? 'false' : 'true');
      }
      render();
    });
    return b;
  };

  // Append Category Chips
  if (filtersWrap.children.length === 1 && filtersWrap.children[0].classList.contains('filter-label')) {
      const categoryFragment = document.createDocumentFragment();
      const allCatChip = mkChip('All','cat');
      allCatChip.setAttribute('aria-pressed', 'true'); // 'All' starts as active
      categoryFragment.appendChild(allCatChip);
      cats.forEach(c => categoryFragment.appendChild(mkChip(c,'cat')));
      filtersWrap.appendChild(categoryFragment);
  }

  // Append Tag Chips (if any exist)
  if (tags.size){
    const tagLabel = document.createElement('div');
    tagLabel.className = 'filter-label';
    tagLabel.textContent = 'Tags:';
    filtersWrap.appendChild(tagLabel);
    const tagFragment = document.createDocumentFragment();
    tags.forEach(t => tagFragment.appendChild(mkChip(t,'tag')));
    filtersWrap.appendChild(tagFragment);
  }

  // --- Event Handlers ---
  q.addEventListener('input', render);
  sortSel.addEventListener('change', render);

  // View Toggle
  viewToggle.addEventListener('click', () => {
    const isList = view.classList.contains('list');
    view.classList.toggle('list', !isList);
    view.classList.toggle('grid', isList);
    viewToggle.setAttribute('aria-pressed', isList ? 'true' : 'false');
    viewToggle.innerHTML = isList ? `<span class="icon">view_module</span> View` : `<span class="icon">view_list</span> View`;
    prefs.view = isList ? 'grid' : 'list';
    localStorage.setItem(PREFS_KEY, JSON.stringify(prefs));
  });

  // Show Favorites Toggle
  showFavsBtn.addEventListener('click', () => {
    const now = showFavsBtn.getAttribute('aria-pressed') === 'true' ? 'false' : 'true';
    showFavsBtn.setAttribute('aria-pressed', now);
    render();
  });

  // Clear Filters Button
  clearBtn.addEventListener('click', () => {
    q.value = ''; sortSel.value = 'name_asc';
    $$('.chip').forEach(c => c.setAttribute('aria-pressed','false'));
    // Ensure 'All' category chip is active after clearing
    const allCatChip = $('.chip[data-type="cat"][data-value="All"]');
    if(allCatChip) allCatChip.setAttribute('aria-pressed', 'true');

    showFavsBtn.setAttribute('aria-pressed','false');
    render();
  });

  // Copy ID - uses dynamic delegation for buttons created after initial load if needed
  // (though in this case, buttons are static)
  document.body.addEventListener('click', async (e) => {
    if (e.target.closest('.card-actions .a-btn:not(.primary)')) { // Target the copy ID button
      const btn = e.target.closest('.a-btn');
      if (btn.textContent.includes('Copy ID') && btn.dataset.id) { // Check for text and data-id
        try {
          await navigator.clipboard.writeText(btn.dataset.id);
          toast('ID copied to clipboard!');
        } catch (err) {
          console.error('Copy failed:', err);
          toast('Copy failed (requires secure context or user permission)');
        }
      }
    }
  });


  // Favorites
  $$('.star').forEach(btn => btn.addEventListener('click', () => {
    const id = btn.dataset.id;
    favs.has(id) ? favs.delete(id) : favs.add(id);
    localStorage.setItem(FAVS_KEY, JSON.stringify([...favs]));
    updateFavStars();
    render(); // Re-render if 'Show Favorites' is active
  }));

  // Keyboard shortcuts
  window.addEventListener('keydown', (e) => {
    // '/' for search focus
    if (e.key === '/' && document.activeElement !== q){ e.preventDefault(); q.focus(); }
    // 'G' then 'L' for view toggle
    if (e.key.toLowerCase() === 'g') window.__go = true;
    else if (window.__go && e.key.toLowerCase() === 'l'){ window.__go = false; viewToggle.click(); e.preventDefault(); }
    // 'F' for favorites toggle
    else if (e.key.toLowerCase() === 'f' && document.activeElement !== q){ showFavsBtn.click(); e.preventDefault(); }
    else window.__go = false; // Reset __go if another key is pressed
  });

  // --- Filtering/Sorting Logic ---

  function updateFavStars(){
    $$('.star').forEach(s => s.dataset.active = favs.has(s.dataset.id) ? 'true' : 'false');
  }

  function activeCategory(){
    const act = $('.chip[data-type="cat"][aria-pressed="true"]');
    if (!act || act.dataset.value === 'All') return null;
    return act.dataset.value.toLowerCase();
  }
  function activeTags(){ return $$('.chip[data-type="tag"][aria-pressed="true"]').map(x => x.dataset.value.toLowerCase()); }

  function render(){
    const needle = q.value.trim().toLowerCase();
    const cat = activeCategory();
    const tags = activeTags();
    const onlyFavs = showFavsBtn.getAttribute('aria-pressed') === 'true';
    const sort = sortSel.value;

    let visible = [];
    cards.forEach(card => {
      const id = card.dataset.id.toLowerCase();
      const name = card.dataset.name.toLowerCase();
      const category = (card.dataset.category || '').toLowerCase();
      const cardTags = (card.dataset.tags || '').toLowerCase().split(',').map(s=>s.trim()).filter(Boolean);

      let ok = true;

      // 1. Search filter
      if (needle) {
        ok = name.includes(needle) ||
             id.includes(needle) ||
             category.includes(needle) ||
             cardTags.some(t => t.includes(needle));
      }

      // 2. Category filter
      if (ok && cat) {
        ok = category === cat;
      }

      // 3. Tag filters (must contain ALL selected tags)
      if (ok && tags.length) {
        ok = tags.every(t => cardTags.includes(t));
      }

      // 4. Favorites filter
      if (ok && onlyFavs) {
        ok = favs.has(card.dataset.id);
      }

      card.style.display = ok ? '' : 'none';
      if (ok) visible.push(card);
    });

    // Sort visible cards
    visible.sort((a,b) => {
      const an = a.dataset.name.toLowerCase(), bn = b.dataset.name.toLowerCase();
      const au = Date.parse(a.dataset.updated)||0; // Fallback to 0 for invalid dates
      const bu = Date.parse(b.dataset.updated)||0;

      switch (sort){
        case 'name_desc': return bn.localeCompare(an);
        case 'updated_desc': return bu - au;
        case 'updated_asc': return au - bu;
        default: return an.localeCompare(bn); // name_asc
      }
    }).forEach(c => view.appendChild(c)); // Re-append to update DOM order

    // Update stats and empty state
    countVisible.textContent = visible.length;
    if (emptyState) emptyState.style.display = visible.length === 0 ? '' : 'none';
  }

  // Toast notification for copy
  function toast(msg){
    const existing = $('.toast');
    if (existing) existing.remove();

    const t = document.createElement('div');
    t.className = 'toast';
    t.textContent = msg;
    t.style.cssText = `
      position:fixed;left:50%;bottom:28px;transform:translateX(-50%);
      background:var(--md-inverse-surface); color:var(--md-inverse-on-surface);
      padding:12px 18px; border-radius:var(--radius-md); box-shadow:var(--elevation-3);
      z-index:999; font-weight:500; font-size: 14px; opacity: 0;
      transition: opacity .3s ease, transform .3s ease;
    `;
    document.body.appendChild(t);

    setTimeout(() => {t.style.opacity = 1; t.style.transform = 'translateX(-50%) translateY(-5px)';}, 10); // Fade in & slight lift
    setTimeout(() => {t.style.opacity = 0; t.style.transform = 'translateX(-50%) translateY(5px)';}, 2000); // Fade out & slight drop
    setTimeout(() => {t.remove()}, 2300);
  }

  // initial render
  render();
})();
</script>
</body>
</html>