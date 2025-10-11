<?php
$manifestPath = __DIR__ . '/templates/manifest.json';
$manifest = json_decode(file_get_contents($manifestPath), true);
$templates = $manifest['templates'] ?? [];
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?><!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Log Sheets – Home</title>
<style>
  body{font:16px/1.35 "Segoe UI", Arial, sans-serif;color:#111;background:#fff;margin:0}
  .wrap{max-width:1100px;margin:20px auto;padding:0 12px}
  h1{margin:0 0 12px}
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px}
  .card{border:1px solid #e2e2e2;border-radius:12px;padding:14px;box-shadow:0 2px 8px rgba(0,0,0,.04)}
  .card h3{margin:0 0 8px}
  .mono{font-family:ui-monospace, Menlo, Consolas, monospace;color:#333}
  .btn{display:inline-block;margin-top:8px;padding:8px 12px;border:1px solid #111;border-radius:8px;background:#111;color:#fff;text-decoration:none}
  .topbar{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px}
  a.link{color:#0a66ff;text-decoration:none}
</style>
</head><body>
<div class="wrap">
  <div class="topbar">
    <h1>Log Sheets</h1>
    <a class="link" href="admin.php">Admin panel →</a>
  </div>
  <div class="grid">
    <?php foreach($templates as $t): ?>
      <div class="card">
        <h3><?= h($t['name']) ?></h3>
        <div class="mono"><?= h($t['id']) ?></div>
        <a class="btn" href="run.php?id=<?= urlencode($t['id']) ?>">Open template</a>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body></html>
