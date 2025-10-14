<?php
$manifestPath = __DIR__ . '/templates/manifest.json';
$manifest = json_decode(file_get_contents($manifestPath), true);
$templates = $manifest['templates'] ?? []; 
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?><!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Log Sheets – Home</title>
<link href="css/style.css" rel="stylesheet">
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
        <a class="btn" href="run.php?id=<?= urlencode($t['id']) ?>">Open Logsheet</a>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body></html>
