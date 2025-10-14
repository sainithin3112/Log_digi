<?php
date_default_timezone_set('Asia/Kolkata');
$dataDir = __DIR__ . '/data';
$manifest = json_decode(file_get_contents(__DIR__.'/templates/manifest.json'), true);
$templatesMeta = []; foreach(($manifest['templates']??[]) as $t){ $templatesMeta[$t['id']] = $t; }
if (!is_dir($dataDir)) { @mkdir($dataDir, 0775, true); }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function safe($s){ return preg_replace('~[^a-z0-9]+~i','_', $s); }

$action = $_GET['action'] ?? '';
$templateId = $_GET['template'] ?? '';
if ($action === 'download_json' && $templateId) {
  $file = $dataDir.'/'.safe($templateId).'.json';
  if (file_exists($file)) { header('Content-Type: application/json'); header('Content-Disposition: attachment; filename="'.basename($file).'"'); readfile($file); exit; }
  http_response_code(404); echo 'Template not found'; exit;
}
if ($action === 'export_csv' && $templateId) {
  $file = $dataDir.'/'.safe($templateId).'.json';
  if (!file_exists($file)) { http_response_code(404); echo 'Template not found'; exit; }
  $arr = json_decode(file_get_contents($file), true); if (!is_array($arr)) $arr = [];
  header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="'.safe($templateId).'_logs.csv"');
  $out = fopen('php://output', 'w');
  fputcsv($out, ['created_at','template_id','uid','wi_no','log_id','log_sr_no','op_name','qty','operator','date','yield','mh_stack','lost_mh']);
  foreach($arr as $e){
    fputcsv($out,[
      $e['meta']['created_at']??'',
      $e['meta']['template_id']??($templateId),
      $e['meta']['uid']??'',
      $e['header1']['wi_no']??'',
      $e['header1']['log_id']??'',
      $e['header1']['log_sr_no']??'',
      $e['header2']['op_name']??'',
      $e['header2']['qty']??'',
      $e['header2']['operator']??'',
      $e['header2']['date']??'',
      $e['sec5_output']['yield']??'',
      $e['sec5_output']['mh_stack']??'',
      $e['sec5_output']['lost_mh']??'',
    ]);
  }
  fclose($out); exit;
}

$templates = array_map(fn($t)=>$t['id'], $manifest['templates']??[]); sort($templates);
$tplFilter = $_GET['tpl'] ?? 'all'; $search = trim($_GET['q'] ?? '');
function read_json($file){ $a = json_decode(@file_get_contents($file), true); return is_array($a) ? $a : []; }

$rows = [];
$loadTpls = ($tplFilter==='all') ? $templates : [$tplFilter];
foreach($loadTpls as $tplId){
  $file = $dataDir . '/' . safe($tplId) . '.json';
  if (!file_exists($file)) continue;
  foreach(read_json($file) as $e){ $rows[] = [$tplId, $e]; }
}
if ($search !== ''){ $rows = array_values(array_filter($rows, fn($pair)=> stripos(json_encode($pair[1]), $search)!==false)); }
usort($rows, fn($A,$B)=> (strtotime($B[1]['meta']['created_at'] ?? '1970-01-01') <=> strtotime($A[1]['meta']['created_at'] ?? '1970-01-01')));
?>
<!doctype html><html>
  <head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Logs Admin</title>
    <!-- Other head elements like Bootstrap CSS link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
  body{font:14px/1.35 "Segoe UI", Arial, sans-serif; color:#111; background:#fff; margin:0}
  .wrap{max-width:1200px;margin:20px auto;padding:0 12px}
  h1{margin:0 0 12px}
  .toolbar{display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-bottom:12px}
  select,input[type="text"]{padding:8px;border:1px solid #ccc;border-radius:6px}
  .btn{padding:8px 12px;border:1px solid #333;border-radius:6px;background:#111;color:#fff;cursor:pointer;text-decoration:none}
  .btn.light{background:#fff;color:#111;border-color:#888}
  table{width:100%;border-collapse:collapse}
  th,td{border:1px solid #ccc;padding:8px;vertical-align:top}
  th{background:#f4f6ff;cursor:pointer}
  .pill{display:inline-block;padding:2px 8px;border-radius:999px;background:#eef3ff;border:1px solid #c7d5ff;margin-right:6px}
  .mono{font-family:ui-monospace, Menlo, Consolas, monospace}
  .center{text-align:center}
  .modal{position:fixed;inset:0;background:rgba(0,0,0,.55);display:none;align-items:center;justify-content:center;padding:20px}
  .card{background:#fff;max-width:900px;max-height:80vh;overflow:auto;border-radius:10px;box-shadow:0 10px 20px rgba(0,0,0,.25);padding:16px}
  pre{white-space:pre-wrap;word-break:break-word;font-size:12px;background:#f7f7f9;border:1px solid #eee;border-radius:8px;padding:12px}
</style>
</head><body>
<div class="wrap">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:12px">
    <h1>Logs Admin</h1>
    <a href="index.php" class="btn light">← Home</a>
  </div>
  <form class="toolbar" method="get" action="">
    <label>Template:
      <select name="tpl" onchange="this.form.submit()">
        <option value="all"<?= $tplFilter==='all'?' selected':'' ?>>All</option>
        <?php foreach($templates as $t): ?>
          <option value="<?= h($t) ?>"<?= $tplFilter===$t?' selected':'' ?>>
            <?= h($t) ?><?php if(!empty($templatesMeta[$t]['name'])) echo ' — '.h($templatesMeta[$t]['name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <input type="text" name="q" placeholder="Search…" value="<?= h($search) ?>">
    <button class="btn light" type="submit">Apply</button>
    <?php if($tplFilter!=='all'): ?>
      <a class="btn" href="?action=export_csv&template=<?= urlencode($tplFilter) ?>">Export CSV</a>
      <a class="btn light" href="?action=download_json&template=<?= urlencode($tplFilter) ?>">Download JSON</a>
    <?php endif; ?>
  </form>

  <table id="grid">
    <thead><tr>
      <th>Template</th><th>Created</th><th>UID</th><th>WI No</th><th>Log ID</th><th>Log SR No</th>
      <th>O/P Name</th><th class="center">Qty</th><th>Operator</th><th>Date</th>
      <th>Yield</th><th>MH/Stack</th><th>Lost MH</th><th>View</th>
      <!-- <th>Print</th> -->
    </tr></thead>
    <tbody>
      <?php foreach($rows as [$t,$e]): $created=$e['meta']['created_at']??''; $when=$created?date('d-M-Y H:i:s', strtotime($created)):''; $payload = htmlspecialchars(json_encode($e, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); $uid=$e['meta']['uid']??''; ?>
        <tr>
          <td><span class="pill mono"><?= h($t) ?></span></td>
          <td class="mono"><?= h($when) ?></td>
          <td class="mono"><?= h($uid) ?></td>
          <td><?= h($e['header1']['wi_no'] ?? '') ?></td>
          <td class="mono"><?= h($e['header1']['log_id'] ?? '') ?></td>
          <td><?= h($e['header1']['log_sr_no'] ?? '') ?></td>
          <td><?= h($e['header2']['op_name'] ?? '') ?></td>
          <td class="center"><?= h($e['header2']['qty'] ?? '') ?></td>
          <td><?= h($e['header2']['operator'] ?? '') ?></td>
          <td><?= h($e['header2']['date'] ?? '') ?></td>
          <td><?= h($e['sec5_output']['yield'] ?? '') ?></td>
          <td><?= h($e['sec5_output']['mh_stack'] ?? '') ?></td>
          <td><?= h($e['sec5_output']['lost_mh'] ?? '') ?></td>
          <td class="center">
            <a class="btn light" style="border: none;background: none;text-align: center;padding: 0;"type="button" href="run.php?id=<?= urlencode($t) ?>&load=<?= urlencode($uid) ?>&autopreview=1" target="_blank">
              <i class="fa fa-eye"></i>
            </a>
          </td>
          <!-- <td class="center">
            <a class="btn"  >Open</a>
          </td> -->
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="modal" id="modal" onclick="if(event.target===this) this.style.display='none'">
  <div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:8px">
      <h3 style="margin:0">Entry</h3>
      <button class="btn light" onclick="document.getElementById('modal').style.display='none'">Close</button>
    </div>
    <pre id="jsonBox"></pre>
  </div>
</div>

<script>
function show(jsonStr){
  try{ document.getElementById('jsonBox').textContent = JSON.stringify(JSON.parse(jsonStr), null, 2); }
  catch(e){ document.getElementById('jsonBox').textContent = jsonStr; }
  document.getElementById('modal').style.display='flex';
}
// client-side click-to-sort
document.querySelectorAll('#grid thead th').forEach(th=>{
  th.addEventListener('click', ()=>{
    const idx = Array.from(th.parentNode.children).indexOf(th);
    const tbody = document.querySelector('#grid tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const asc = th.dataset.asc === '1' ? false : true;
    th.dataset.asc = asc ? '1' : '0';
    rows.sort((a,b)=>{
      const A = a.children[idx].innerText.trim().toLowerCase();
      const B = b.children[idx].innerText.trim().toLowerCase();
      if(!isNaN(A) && !isNaN(B)) return (asc?1:-1) * (parseFloat(A)-parseFloat(B));
      return (asc?1:-1) * A.localeCompare(B);
    });
    rows.forEach(r=>tbody.appendChild(r));
  });
});
</script>
</body></html>
