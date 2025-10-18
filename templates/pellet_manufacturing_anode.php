<?php
// Template: Pellet Manufacturing (Anode) — 2 pages
// Saves JSON to data/<safe(TEMPLATE_ID)>.json
date_default_timezone_set('Asia/Kolkata');

// Ensure safe_slug exists (you had it commented out)
if (!function_exists('safe_slug')) {
  function safe_slug($s){ return preg_replace('~[^a-z0-9]+~i','_', $s); }
}

// Next serial number from the data file (001, 002, ...)
function next_sr_no($file){
  if(!file_exists($file)) return '001';
  $arr = json_decode(@file_get_contents($file), true);
  if(!is_array($arr)) return '001';
  $max = 0;
  foreach($arr as $e){
    $raw = (string)($e['header1']['log_sr_no'] ?? '');
    if(preg_match('~(\d+)~', $raw, $m)){
      $n = (int)$m[1];
      if($n > $max) $max = $n;
    }
  }
  return str_pad((string)($max+1), 3, '0', STR_PAD_LEFT);
}

$TEMPLATE_ID = 'LI-PRD-RC-28A';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function v($name, $def=''){ return isset($_POST[$name]) ? $_POST[$name] : $def; }
// function safe_slug($s){ return preg_replace('~[^a-z0-9]+~i','_', $s); }
$SAFE = safe_slug($TEMPLATE_ID);
$logfile = __DIR__ . '/../data/' . $SAFE . '.json';

/* ---------- Rehydrate when admin opens an existing entry ---------- */
if (isset($_GET['load']) && $_GET['load']!==''){
  $uid = $_GET['load'];
  $arr = json_decode(@file_get_contents($logfile), true) ?: [];
  foreach($arr as $e){
    if(($e['meta']['uid']??'') === $uid){
      // Header 1
      $_POST['wi_name'] = $e['header1']['wi_name']??'PELLET MANUFACTURING (ANODE)';
      $_POST['wi_no']   = $e['header1']['wi_no']??'WI-PRD-28';
      $_POST['log_id']  = $e['header1']['log_id']??$TEMPLATE_ID;
      $_POST['log_sr_no'] = $e['header1']['log_sr_no']??'';
      $_POST['pid_no']  = $e['header1']['pid_no']??'';
      $_POST['battery_no'] = $e['header1']['battery_no']??'';
      $_POST['date']    = $e['header1']['date']??'';

      // Safety (operator/earthing)
      foreach(($e['safety']['operator']??[]) as $k=>$val){ $_POST['op_'.$k]=$val; }
      foreach(($e['safety']['earthing']??[]) as $k=>$val){ $_POST['earth_'.$k]=$val; }
      $_POST['safety_comments'] = $e['safety']['comments']??'';

      // WS prep
      $_POST['gb_id'] = $e['ws_prep']['ids']['glove_box_id']??'';
      $_POST['hp_id'] = $e['ws_prep']['ids']['h_press_id']??'';
      $_POST['bal_id']= $e['ws_prep']['ids']['balance_id']??'';
      $_POST['thk_id']= $e['ws_prep']['ids']['thickness_gauge_id']??'';
      foreach(($e['ws_prep']['cleaning']??[]) as $k=>$val){ $_POST['cl_'.$k]=$val; }
      $_POST['dew']   = $e['ws_prep']['dew']??'';
      foreach(($e['ws_prep']['calibration']??[]) as $k=>$val){ $_POST['cal_'.$k]=$val; }
      foreach(($e['ws_prep']['utilities']??[]) as $k=>$val){ $_POST['ut_'.$k]=$val; }

      // Hazard/Risk
      $_POST['hz_1'] = $e['hazards']['r07_15']??'';
      $_POST['hz_2'] = $e['hazards']['r07_16']??'';
      $_POST['hz_3'] = $e['hazards']['r07_17']??'';
      $_POST['hz_4'] = $e['hazards']['r07_18']??'';
      $_POST['risk_rank'] = $e['hazards']['risk_rank']??'MEDIUM';

      // Quality Inspection rows (page 1)
      $_POST['qi_time']   = array_column($e['quality_inspection']??[], 'time');
      $_POST['qi_range']  = array_column($e['quality_inspection']??[], 'pellet_range');
      $_POST['qi_pow']    = array_column($e['quality_inspection']??[], 'powder_weight');
      $_POST['qi_w']      = array_column($e['quality_inspection']??[], 'pellet_weight');
      $_POST['qi_t1']     = array_column($e['quality_inspection']??[], 't1');
      $_POST['qi_t2']     = array_column($e['quality_inspection']??[], 't2');
      $_POST['qi_t3']     = array_column($e['quality_inspection']??[], 't3');
      $_POST['qi_t4']     = array_column($e['quality_inspection']??[], 't4');
      $_POST['qi_t5']     = array_column($e['quality_inspection']??[], 't5');
      $_POST['qi_avg']    = array_column($e['quality_inspection']??[], 'avg');
      $_POST['qi_mm']     = array_column($e['quality_inspection']??[], 'maxmin');
      $_POST['qi_density']= array_column($e['quality_inspection']??[], 'density');
      $_POST['qi_insp']   = array_column($e['quality_inspection']??[], 'inspection_by');

      $_POST['qc_obs']    = $e['qc_observations']??'';
      $_POST['prev_log']  = $e['previous_log']??'';

      // Page 2 header (IP product / times)
      $_POST['ip_name']   = $e['page2']['ip']['name']??'ANODE POWDER';
      $_POST['ip_code']   = $e['page2']['ip']['code']??'ANP-06-';
      $_POST['ip_lot']    = $e['page2']['ip']['lot']??'';
      $_POST['ip_weight'] = $e['page2']['ip']['weight']??'';
      $_POST['ip_exp']    = $e['page2']['ip']['expiry']??'';
      $_POST['start_time']= $e['page2']['ip']['start_time']??'';
      $_POST['end_time']  = $e['page2']['ip']['end_time']??'';

      // Page 2 inspection rows
      $_POST['p2_time']   = array_column($e['page2']['inspection']??[], 'time');
      $_POST['p2_range']  = array_column($e['page2']['inspection']??[], 'pellet_range');
      $_POST['p2_pow']    = array_column($e['page2']['inspection']??[], 'powder_weight');
      $_POST['p2_w']      = array_column($e['page2']['inspection']??[], 'pellet_weight');
      $_POST['p2_d']      = array_column($e['page2']['inspection']??[], 'pellet_dia');
      $_POST['p2_t1']     = array_column($e['page2']['inspection']??[], 't1');
      $_POST['p2_t2']     = array_column($e['page2']['inspection']??[], 't2');
      $_POST['p2_t3']     = array_column($e['page2']['inspection']??[], 't3');
      $_POST['p2_t4']     = array_column($e['page2']['inspection']??[], 't4');
      $_POST['p2_t5']     = array_column($e['page2']['inspection']??[], 't5');
      $_POST['p2_avg']    = array_column($e['page2']['inspection']??[], 'avg');
      $_POST['p2_press']  = array_column($e['page2']['inspection']??[], 'pressure');
      $_POST['p2_comp']   = array_column($e['page2']['inspection']??[], 'compression');
      $_POST['p2_mm']     = array_column($e['page2']['inspection']??[], 'maxmin');
      $_POST['p2_density']= array_column($e['page2']['inspection']??[], 'density');
      $_POST['p2_insp']   = array_column($e['page2']['inspection']??[], 'inspection_by');

      // Page 2 process log (two blocks + barcode region)
      $_POST['pl1_time']  = array_column($e['page2']['process_log_left']??[], 'time');
      $_POST['pl1_dew']   = array_column($e['page2']['process_log_left']??[], 'dew');
      $_POST['pl1_bw']    = array_column($e['page2']['process_log_left']??[], 'bottle_weight');
      $_POST['pl2_time']  = array_column($e['page2']['process_log_right']??[], 'time');
      $_POST['pl2_dew']   = array_column($e['page2']['process_log_right']??[], 'dew');
      $_POST['pl2_bw']    = array_column($e['page2']['process_log_right']??[], 'bottle_weight');
      $_POST['barcode']   = $e['page2']['barcode']??'';

      // Page2 O/P block + tallies
      $_POST['op_name']   = $e['page2']['op']['name']??'ANODE PELLETS';
      $_POST['op_code']   = $e['page2']['op']['code']??'ANP-28-';
      $_POST['op_lot']    = $e['page2']['op']['lot']??'';
      $_POST['qty_produced'] = $e['page2']['op']['qty']??'';
      $_POST['accepted_a']   = $e['page2']['op']['accepted']??'';
      $_POST['accepted_weight'] = $e['page2']['op']['accepted_weight']??'';
      $_POST['rejected_r']   = $e['page2']['op']['rejected']??'';
      $_POST['flag_T']       = $e['page2']['op']['flags']['T']??'';
      $_POST['flag_W']       = $e['page2']['op']['flags']['W']??'';
      $_POST['flag_B']       = $e['page2']['op']['flags']['B']??'';
      $_POST['yield_pct']    = $e['page2']['op']['yield_pct']??'';

      $_POST['actual_mh'] = $e['page2']['actual_mh']??'';
      $_POST['lost_mh']   = $e['page2']['lost_mh']??'';
      $_POST['mh_pellet'] = $e['page2']['mh_pellet']??'';
      $_POST['leftover_qty'] = $e['page2']['leftover_qty']??'';
      $_POST['storage_label'] = $e['page2']['storage_label']??'';
      $_POST['operator_name'] = $e['page2']['operator_name']??'';
      $_POST['prd_obs'] = $e['page2']['prd_observations']??'';
      break;
    }
  }
}

$save_message = '';

/* ------------------------------ Save ------------------------------ */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['__action']) && $_POST['__action']==='save') {

  $entry = [
    'meta'=>[
      'uid'=>bin2hex(random_bytes(8)),
      'template_id'=>$TEMPLATE_ID,
      'created_at'=>date('c'),
      'ip'=>$_SERVER['REMOTE_ADDR'] ?? '',
      'user_agent'=>$_SERVER['HTTP_USER_AGENT'] ?? '',
    ],
    'header1'=>[
      'wi_name'=>v('wi_name','PELLET MANUFACTURING (ANODE)'),
      'wi_no'=>v('wi_no','WI-PRD-28'),
      'log_id'=>v('log_id',$TEMPLATE_ID),
      'log_sr_no'=>v('log_sr_no',''),             // EDITABLE by request
      'pid_no'=>v('pid_no',''),
      'battery_no'=>v('battery_no',''),
      'date'=>v('date',''),
    ],
    'safety'=>[
      'operator'=>[
        'apron'=>v('op_apron',''),
        'gloves'=>v('op_gloves',''),
        'mask'=>v('op_mask',''),
      ],
      'earthing'=>[
        'glove_box'=>v('earth_gb',''),
        'h_press'=>v('earth_hp',''),
        'operator'=>v('earth_op',''),
      ],
      'comments'=>v('safety_comments',''),
    ],
    'ws_prep'=>[
      'ids'=>[
        'glove_box_id'=>v('gb_id',''),
        'h_press_id'=>v('hp_id',''),
        'balance_id'=>v('bal_id',''),
        'thickness_gauge_id'=>v('thk_id',''),
      ],
      'cleaning'=>[
        'gb'=>v('cl_gb',''),
        'hp'=>v('cl_hp',''),
        'bal'=>v('cl_bal',''),
        'thk'=>v('cl_thk',''),
      ],
      'dew'=>v('dew',''),
      'calibration'=>[
        'gb'=>v('cal_gb',''),
        'hp'=>v('cal_hp',''),
        'bal'=>v('cal_bal',''),
        'thk'=>v('cal_thk',''),
      ],
      'utilities'=>[
        'watch_glass'=>v('ut_watch',''),
        'tweezers'=>v('ut_tweez',''),
        'ac'=>v('ut_ac',''),
        'rc'=>v('ut_rc',''),
      ],
    ],
    'hazards'=>[
      'r07_15'=>v('hz_1',''),
      'r07_16'=>v('hz_2',''),
      'r07_17'=>v('hz_3',''),
      'r07_18'=>v('hz_4',''),
      'risk_rank'=>v('risk_rank','MEDIUM'),
      'consequence_rank'=>['E'=>2,'M'=>2,'F'=>2,'R'=>2],
    ],
    'quality_inspection'=>[],
    'qc_observations'=>v('qc_obs',''),
    'previous_log'=>v('prev_log',''),
    'page2'=>[
      'ip'=>[
        'name'=>v('ip_name','ANODE POWDER'),
        'code'=>v('ip_code','ANP-06-'),
        'lot'=>v('ip_lot',''),
        'weight'=>v('ip_weight',''),
        'expiry'=>v('ip_exp',''),
        'start_time'=>v('start_time',''),
        'end_time'=>v('end_time',''),
      ],
      'inspection'=>[],
      'process_log_left'=>[],
      'process_log_right'=>[],
      'barcode'=>v('barcode',''),
      'op'=>[
        'name'=>v('op_name','ANODE PELLETS'),
        'code'=>v('op_code','ANP-28-'),
        'lot'=>v('op_lot',''),
        'qty'=>v('qty_produced',''),
        'accepted'=>v('accepted_a',''),
        'accepted_weight'=>v('accepted_weight',''),
        'rejected'=>v('rejected_r',''),
        'flags'=>[
          'T'=>v('flag_T',''),
          'W'=>v('flag_W',''),
          'B'=>v('flag_B',''),
        ],
        'yield_pct'=>v('yield_pct',''),
      ],
      'actual_mh'=>v('actual_mh',''),
      'lost_mh'=>v('lost_mh',''),
      'mh_pellet'=>v('mh_pellet',''),
      'leftover_qty'=>v('leftover_qty',''),
      'storage_label'=>v('storage_label',''),
      'operator_name'=>v('operator_name',''),
      'prd_observations'=>v('prd_obs',''),
    ]
  ];

  // Page 1 quality inspection rows
  $rows = max(
    count($_POST['qi_time'] ?? []),
    count($_POST['qi_range'] ?? []),
    count($_POST['qi_pow'] ?? []),
    count($_POST['qi_w'] ?? [])
  );
  for($i=0;$i<$rows;$i++){
    $entry['quality_inspection'][] = [
      'time'=>$_POST['qi_time'][$i] ?? '',
      'pellet_range'=>$_POST['qi_range'][$i] ?? '',
      'powder_weight'=>$_POST['qi_pow'][$i] ?? '',
      'pellet_weight'=>$_POST['qi_w'][$i] ?? '',
      't1'=>$_POST['qi_t1'][$i] ?? '',
      't2'=>$_POST['qi_t2'][$i] ?? '',
      't3'=>$_POST['qi_t3'][$i] ?? '',
      't4'=>$_POST['qi_t4'][$i] ?? '',
      't5'=>$_POST['qi_t5'][$i] ?? '',
      'avg'=>$_POST['qi_avg'][$i] ?? '',
      'maxmin'=>$_POST['qi_mm'][$i] ?? '',
      'density'=>$_POST['qi_density'][$i] ?? '',
      'inspection_by'=>$_POST['qi_insp'][$i] ?? '',
    ];
  }

  // Page 2 inspection rows
  $rows2 = max(
    count($_POST['p2_time'] ?? []),
    count($_POST['p2_range'] ?? []),
    count($_POST['p2_pow'] ?? []),
    count($_POST['p2_w'] ?? [])
  );
  for($i=0;$i<$rows2;$i++){
    $entry['page2']['inspection'][] = [
      'time'=>$_POST['p2_time'][$i] ?? '',
      'pellet_range'=>$_POST['p2_range'][$i] ?? '',
      'powder_weight'=>$_POST['p2_pow'][$i] ?? '',
      'pellet_weight'=>$_POST['p2_w'][$i] ?? '',
      'pellet_dia'=>$_POST['p2_d'][$i] ?? '',
      't1'=>$_POST['p2_t1'][$i] ?? '',
      't2'=>$_POST['p2_t2'][$i] ?? '',
      't3'=>$_POST['p2_t3'][$i] ?? '',
      't4'=>$_POST['p2_t4'][$i] ?? '',
      't5'=>$_POST['p2_t5'][$i] ?? '',
      'avg'=>$_POST['p2_avg'][$i] ?? '',
      'pressure'=>$_POST['p2_press'][$i] ?? '',
      'compression'=>$_POST['p2_comp'][$i] ?? '',
      'maxmin'=>$_POST['p2_mm'][$i] ?? '',
      'density'=>$_POST['p2_density'][$i] ?? '',
      'inspection_by'=>$_POST['p2_insp'][$i] ?? '',
    ];
  }

  // Page 2 process logs (left and right)
  $n1 = max(count($_POST['pl1_time'] ?? []), count($_POST['pl1_dew'] ?? []), count($_POST['pl1_bw'] ?? []));
  for($i=0;$i<$n1;$i++){
    $entry['page2']['process_log_left'][] = [
      'time'=>$_POST['pl1_time'][$i] ?? '',
      'dew'=>$_POST['pl1_dew'][$i] ?? '',
      'bottle_weight'=>$_POST['pl1_bw'][$i] ?? '',
    ];
  }
  $n2 = max(count($_POST['pl2_time'] ?? []), count($_POST['pl2_dew'] ?? []), count($_POST['pl2_bw'] ?? []));
  for($i=0;$i<$n2;$i++){
    $entry['page2']['process_log_right'][] = [
      'time'=>$_POST['pl2_time'][$i] ?? '',
      'dew'=>$_POST['pl2_dew'][$i] ?? '',
      'bottle_weight'=>$_POST['pl2_bw'][$i] ?? '',
    ];
  }

  // Ensure directory and write
  if (!is_dir(dirname($logfile))) { @mkdir(dirname($logfile), 0775, true); }
  if (!file_exists($logfile)) { file_put_contents($logfile, json_encode([])); }

  $fh = fopen($logfile, 'c+');
  if ($fh) {
    flock($fh, LOCK_EX);
    $json = stream_get_contents($fh);
    $arr = $json ? json_decode($json, true) : [];
    if (!is_array($arr)) $arr = [];
    $arr[] = $entry;
    ftruncate($fh, 0); rewind($fh);
    fwrite($fh, json_encode($arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    fflush($fh); flock($fh, LOCK_UN); fclose($fh);
    $save_message = 'Saved to data/'.$SAFE.'.json at '.date('d-M-Y H:i:s').' (UID: '.$entry['meta']['uid'].')';
  } else {
    $save_message = 'ERROR: Unable to write data file.';
  }
}
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>PELLET MANUFACTURING (ANODE) – <?= h($TEMPLATE_ID) ?></title>
<link href="css/style.css" rel="stylesheet">
<style>
/* Minimal UI glue to match your previous template look */
:root{ --ink:#111; --bd2:2px solid #000; --bd1:1px solid #000; --ok:#0a8a00; --bad:#c1121f; }
*{box-sizing:border-box}
html,body{margin:0;background:#fff;color:#111;font:13px/1.25 "Segoe UI",Arial,sans-serif}
.page{width:1100px;margin:16px auto;padding:8px;border:var(--bd2);position:relative}
.tbl{width:100%;border-collapse:collapse;border:var(--bd2);table-layout:fixed}
.tbl th,.tbl td{border:var(--bd1);padding:6px;vertical-align:middle}
.narrow td,.narrow th{padding:4px}
.center{text-align:center}
.bold{font-weight:700}
input[type="text"], input[type="number"], input[type="date"],input[type="time"], textarea{ width:100%;border:none;outline:none;padding:2px 4px;font:inherit;background:#fff;text-align:center }
textarea{resize:vertical}
.sec-title{background:#f3f6ff;font-weight:700}
.logo-cell{padding:0;vertical-align:middle}
.logo{display:flex;align-items:center;justify-content:center}
.logo img{max-height:54px;width:auto;display:block}
.box{display:inline-flex;align-items:center;justify-content:center;min-width:24px;height:20px;border:var(--bd1);user-select:none;cursor:pointer}
.chk.tick{color:var(--ok);font-weight:900}
.chk.cross{color:var(--bad);font-weight:900}
.bar{position:sticky;top:0;background:#fff;padding:8px 0;display:flex;gap:10px;justify-content:flex-end}
.btn{border:1px solid #444;background:#0a0a0a;color:#fff;padding:8px 12px;border-radius:6px;cursor:pointer}
.btn.secondary{background:#e9eefb;color:#111;border-color:#9bb2ff}
.btn.light{background:#fff;color:#111;border-color:#999}
.msg{margin:8px auto;width:1100px}
.ok{background:#e8f7ea;border:1px solid #9bd3a0;padding:8px;border-radius:6px}
.err{background:#fde8e9;border:1px solid #f29ca3;padding:8px;border-radius:6px}
.footer-stamp{position:absolute;left:0;right:0;bottom:6px;text-align:center;font-size:12px}
@media print{ .bar{display:none} .page{margin:0;border:none;width:auto;page-break-after:always} .page:last-of-type{page-break-after:auto} }
.small{font-size:12px}
</style>
</head>
<body>

<?php if($save_message): ?>
  <div class="msg <?php echo (strpos($save_message,'ERROR')===false?'ok':'err'); ?>"><?php echo h($save_message); ?></div>
<?php endif; ?>

<form method="post" id="logForm">
<input type="hidden" name="__action" id="__action" value="">

<div class="bar">
  <a class="btn light" href="index.php">Home</a>
  <a class="btn light" href="admin.php" target="_blank">Admin</a>
  <button type="button" class="btn light" onclick="openPreview()">Preview</button>
  <button type="button" class="btn" onclick="saveForm()">Save</button>
  <button type="button" class="btn secondary" onclick="window.print()">Print (as-is)</button>
</div>

<!-- ========================= PAGE 1 ========================= -->
<div class="page" id="page1">

  <!-- Header -->
  <table class="tbl narrow">
    <colgroup>
      <col style="width:10%"><col style="width:22%"><col style="width:10%"><col style="width:12%"><col style="width:10%"><col style="width:10%"><col style="width:12%"><col style="width:14%">
    </colgroup>
    <tr>
      <td class="logo-cell" rowspan="2"><div class="logo"><img src="assets/logo.png" alt="RES"></div></td>
      <th class="center">NAME OF WORK INSTRUCTION (WI)</th>
      <th class="center">WI NO.</th>
      <th class="center">LOG ID</th>
      <th class="center">LOG SR NO.</th>
      <th class="center">PID NO.</th>
      <th class="center">BATTERY NO (s)</th>
      <th class="center">DATE</th>
    </tr>
    <tr>
      <td><input type="text" name="wi_name" value="<?= h(v('wi_name','PELLET MANUFACTURING (ANODE)')) ?>"></td>
      <td><input type="text" name="wi_no" value="<?= h(v('wi_no','WI-PRD-28')) ?>"></td>
      <td><input type="text" name="log_id" value="<?= h(v('log_id',$TEMPLATE_ID)) ?>"></td>
      <td>  <input type="text" name="log_sr_no"
         value="<?= h(v('log_sr_no', $_POST['log_sr_no'] ?? next_sr_no($logfile))) ?>"></td> <!-- EDITABLE -->
      <td><input type="text" name="pid_no" value="<?= h(v('pid_no','')) ?>"></td>
      <td><input type="text" name="battery_no" value="<?= h(v('battery_no','')) ?>"></td>
      <td><input type="date" name="date" value="<?= h(v('date','')) ?>"></td>
    </tr>
  </table>

  <!-- Safety -->
  <table class="tbl narrow" style="margin-top:8px">
    <colgroup><col style="width:33%"><col style="width:33%"><col style="width:34%"></colgroup>
    <tr><th class="center">SAFETY CHECKS (OPERATOR)</th><th class="center">SAFETY CHECKS (EARTHING)</th><th class="center">SAFETY COMMENTS &amp; CLEARANCE</th></tr>
    <tr>
      <td class="center">
        APRON <span class="box chk" data-target="op_apron"></span><input type="hidden" name="op_apron" value="<?= h(v('op_apron','')) ?>">
        &nbsp; GLOVES <span class="box chk" data-target="op_gloves"></span><input type="hidden" name="op_gloves" value="<?= h(v('op_gloves','')) ?>">
        &nbsp; MASK <span class="box chk" data-target="op_mask"></span><input type="hidden" name="op_mask" value="<?= h(v('op_mask','')) ?>">
      </td>
      <td class="center">
        GLOVE BOX <span class="box chk" data-target="earth_gb"></span><input type="hidden" name="earth_gb" value="<?= h(v('earth_gb','')) ?>">
        &nbsp; H PRESS <span class="box chk" data-target="earth_hp"></span><input type="hidden" name="earth_hp" value="<?= h(v('earth_hp','')) ?>">
        &nbsp; OPERATOR <span class="box chk" data-target="earth_op"></span><input type="hidden" name="earth_op" value="<?= h(v('earth_op','')) ?>">
      </td>
      <td><textarea name="safety_comments" placeholder="Comments / Clearance"><?= h(v('safety_comments','')) ?></textarea></td>
    </tr>
  </table>

  <!-- WS prep / cleanliness / hazards -->
  <table class="tbl narrow" style="margin-top:8px">
    <colgroup><col style="width:48%"><col style="width:32%"><col style="width:20%"></colgroup>
    <tr>
        <th class="sec-title">WORK STATION PREPARATION AND INITIALIZATION</th>
        <th class="sec-title">CLEANLINESS OF UTILITIES</th>
            <td rowspan="2" style="padding:0;vertical-align:top">
            <div style="display:flex; flex-direction:column;text-align:center;align-content:center;align-items:center;">
        <div style="border-bottom: 1px solid #000;">
            <ul style="padding:0;">
                <li style="list-style: none">*Convert D,AT into cm*</li>
                <li style="list-style: none">Density (g/cm³) = (1.273*W)/(D*D*AT)</li>
                <li style="list-style: none">Yield(%) = R/A * 100</li>
            </ul>
        </div>
        <div>
            <ul style="padding:0;">
                <li style="list-style: none; padding:1em;">*NOTE: ATTACH PICTURES OF PELLET TO THE LOGSHEET*</li>
            </ul>
        </div>
</div>
    </td>
    
    </tr>
    <tr>
      <td style="padding:0">
        <table class="tbl narrow s3-table" style="border:none;border-top:0;">
          <colgroup><col style="width:25%"><col style="width:25%"><col style="width:25%"><col style="width:25%"></colgroup>
          <tr class="center"><th>GLOVE BOX ID</th><th>H PRESS ID</th><th>BALANCE ID</th><th>THICKNESS GAUGE ID</th></tr>
          <tr>
            <td><input type="text" name="gb_id" value="<?= h(v('gb_id','')) ?>"></td>
            <td><input type="text" name="hp_id" value="<?= h(v('hp_id','')) ?>"></td>
            <td><input type="text" name="bal_id" value="<?= h(v('bal_id','')) ?>"></td>
            <td><input type="text" name="thk_id" value="<?= h(v('thk_id','')) ?>"></td>
          </tr>
          <tr class="center"><th>CLEANING</th><th>CLEANING</th><th>CLEANING</th><th>CLEANING</th></tr>
          <tr>
            <td class="center"><span class="box chk" data-target="cl_gb"></span><input type="hidden" name="cl_gb" value="<?= h(v('cl_gb','')) ?>"></td>
            <td class="center"><span class="box chk" data-target="cl_hp"></span><input type="hidden" name="cl_hp" value="<?= h(v('cl_hp','')) ?>"></td>
            <td class="center"><span class="box chk" data-target="cl_bal"></span><input type="hidden" name="cl_bal" value="<?= h(v('cl_bal','')) ?>"></td>
            <td class="center"><span class="box chk" data-target="cl_thk"></span><input type="hidden" name="cl_thk" value="<?= h(v('cl_thk','')) ?>"></td>
          </tr>
          <tr class="center"><th>DEW POINT</th><th>CALIBRATION</th><th>CALIBRATION</th><th>CALIBRATION</th></tr>
          <tr>
            <td class="center">
              <div class="dew-note">Min: -80&nbsp;°C<br>Max: -50&nbsp;°C</div>
            </td>
            <td class="center"><span class="box chk" data-target="cal_gb"></span><input type="hidden" name="cal_gb" value="<?= h(v('cal_gb','')) ?>"></td>
            <td class="center"><span class="box chk" data-target="cal_hp"></span><input type="hidden" name="cal_hp" value="<?= h(v('cal_hp','')) ?>"></td>
            <td class="center"><span class="box chk" data-target="cal_bal"></span><input type="hidden" name="cal_bal" value="<?= h(v('cal_bal','')) ?>"></td>
          </tr>
        </table>
      </td>
      <td style="padding:0">
        <table class="tbl narrow s3-table" style="border:none;border-top:0">
          <colgroup><col style="width:25%"><col style="width:25%"><col style="width:25%"><col style="width:25%"></colgroup>
          <tr class="center"><th>WATCH GLASS</th><th>TWEEZERS</th><th>AC</th><th>RC</th></tr>
          <tr>
            <td class="center"><span class="box chk" data-target="ut_watch"></span><input type="hidden" name="ut_watch" value="<?= h(v('ut_watch','')) ?>"></td>
            <td class="center"><span class="box chk" data-target="ut_tweez"></span><input type="hidden" name="ut_tweez" value="<?= h(v('ut_tweez','')) ?>"></td>
            <td class="center"><span class="box chk" data-target="ut_ac"></span><input type="hidden" name="ut_ac" value="<?= h(v('ut_ac','')) ?>"></td>
            <td class="center"><span class="box chk" data-target="ut_rc"></span><input type="hidden" name="ut_rc" value="<?= h(v('ut_rc','')) ?>"></td>
          </tr>
          <tr class="t2">
            <td colspan="4" style="padding:0">
              <table class="tbl narrow t2" style="border:none;border-top:0">
                <colgroup><col style="width:40%"><col style="width:20%"><col style="width:40%"></colgroup>
                <tr>
                  <th>HAZARD ID NO.</th><th>RISK RANK</th><th>CONSEQUENCE RANK</th>
                </tr>
                <tr>
                  <td class="center">
                    

    <div>
                        <table class="tbl narrow" style="border:none">
                      <tr>
                        <td>R07-15</td>
                        <td>R07-16</td>
                      </tr>
                      <tr>
                        <td>R07-17</td>
                        <td>R07-18</td>
                      </tr>
                    </table>
</div>


                  </td>
                  <td class="center"><input type="text" name="risk_rank" value="<?= h(v('risk_rank','MEDIUM')) ?>"></td>
                  <td class="center">
                    <div>
                      <table class="tbl narrow" style="border:none">
                        <tr>
                          <td>E</td>
                          <td>M</td>
                          <td>F</td>
                          <td>R</td>
                        </tr>
                    
                      <tr>
                        <td>2</td>
                        <td>2</td>
                        <td>2</td>
                        <td>2</td>
                      </tr>
                    </table>
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

  <!-- Quality Inspection (Page 1) -->
  <table class="tbl narrow" style="margin-top:8px">
    <tr><th class="sec-title" colspan="13">QUALITY INSPECTION</th></tr>
    <tr class="center">
      <th rowspan="2" style="width:10%">TIME OF INSPECTION</th>
      <th rowspan="2" style="width:20%">PELLET NO(s) (FROM - TO)</th>
      <th rowspan="2" style="width:5%">POWDER WEIGHT (g)</th>
      <th rowspan="2" style="width:7%">PELLET WEIGHT (g) (W)</th>
      <th colspan="5">PELLET THICKNESS (mm) [REF. DWG.]</th>
      <th rowspan="2" style="width:5%">AVG PELLET THK (mm) (AT)</th>
      <th rowspan="2" style="width:5%">MAX-MIN (mm)</th>
      <th rowspan="2" style="width:5%">DENSITY (g/cm³)</th>
      <th rowspan="2" style="width:7%">INSPECTION BY</th>
    </tr>
    <tr class="center">
      <th>1</th>
      <th>2</th>
      <th>3</th>
      <th>4</th>
      <th>5</th>
    </tr>
    <?php for($i=0;$i<7;$i++): ?>
    <tr>
      <td><input type="time" name="qi_time[]" value="<?= h($_POST['qi_time'][$i]??'') ?>"></td>
      <td><input type="text" name="qi_range[]" value="<?= h($_POST['qi_range'][$i]??'') ?>"></td>
      <td><input type="text" name="qi_pow[]" value="<?= h($_POST['qi_pow'][$i]??'') ?>"></td>
      <td><input type="text" name="qi_w[]" value="<?= h($_POST['qi_w'][$i]??'') ?>"></td>
      <td><input type="text" name="qi_t1[]" value="<?= h($_POST['qi_t1'][$i]??'') ?>"></td>
      <td><input type="text" name="qi_t2[]" value="<?= h($_POST['qi_t2'][$i]??'') ?>"></td>
      <td><input type="text" name="qi_t3[]" value="<?= h($_POST['qi_t3'][$i]??'') ?>"></td>
      <td><input type="text" name="qi_t4[]" value="<?= h($_POST['qi_t4'][$i]??'') ?>"></td>
      <td><input type="text" name="qi_t5[]" value="<?= h($_POST['qi_t5'][$i]??'') ?>"></td>
      <td><input type="text" name="qi_avg[]" value="<?= h($_POST['qi_avg'][$i]??'') ?>"></td>
      <td><input type="text" name="qi_mm[]" value="<?= h($_POST['qi_mm'][$i]??'') ?>"></td>
      <td><input type="text" name="qi_density[]" value="<?= h($_POST['qi_density'][$i]??'') ?>"></td>
      <td><input type="text" name="qi_insp[]" value="<?= h($_POST['qi_insp'][$i]??'') ?>"></td>
    </tr>
    <?php endfor; ?>
  </table>

  <table class="tbl narrow" style="margin-top:8px">
    <colgroup><col style="width:70%"><col style="width:30%"></colgroup>
    <tr><th>QC OBSERVATIONS/SIGNATURE</th><th rowspan="1">PREVIOUS PROCESS LOG ID / SR NO.:</th></tr>
    <tr>
              <td ><textarea name="qc_obs" style="min-height:70px"><?= h(v('qc_obs','')) ?></textarea></td>
<td> <input type="text" name="prev_log" value="<?= h(v('prev_log','')) ?>"></td>
    </tr>
    <tr>
      <!-- <td><textarea name="prev_log" style="min-height:70px"><?= h(v('prev_log',"• HAZARD ID NO\n• POWDER FILLING IN JIG - [R07-15]\n• PELLETIZATION OF POWDER - [R07-16]\n• PELLET REMOVAL FROM JIG - [R07-17]\n• PELLET WEIGHING AND VISUAL INSPECTION - [R07-18]*")) ?></textarea></td> -->
    </tr>
  </table>

  <div class="footer-stamp"><span class="page-no">Page 1 of 2</span> • <span class="ts"></span></div>
</div>

<!-- ========================= PAGE 2 ========================= -->
<div class="page" id="page2">

  <table class="tbl narrow">
    <colgroup><col style="width:18%"><col style="width:16%"><col style="width:16%"><col style="width:12%"><col style="width:14%"><col style="width:12%"><col style="width:12%"></colgroup>
    <tr class="center">
      <th>I/P PRODUCT NAME</th><th>I/P PRODUCT CODE</th><th>I/P PRODUCT LOT NO</th>
      <th>WEIGHT (g)</th><th>EXPIRY DATE</th><th>START TIME</th><th>END TIME</th>
    </tr>
    <tr>
      <td><input type="text" name="ip_name" value="<?= h(v('ip_name','ANODE POWDER')) ?>"></td>
      <td><input type="text" name="ip_code" value="<?= h(v('ip_code','ANP-06-')) ?>"></td>
      <td><input type="text" name="ip_lot" value="<?= h(v('ip_lot','')) ?>"></td>
      <td><input type="text" name="ip_weight" value="<?= h(v('ip_weight','')) ?>"></td>
      <td><input type="date" name="ip_exp" value="<?= h(v('ip_exp','')) ?>"></td>
      <td><input type="time" name="start_time" value="<?= h(v('start_time','')) ?>"></td>
      <td><input type="time" name="end_time" value="<?= h(v('end_time','')) ?>"></td>
    </tr>
  </table>
  <table class="tbl narrow" style="margin-top:8px">
    <colgroup><col style="width:12%"><col style="width:08%"><col style="width:12%"><col style="width:08%"><col style="width:14%"><col style="width:08%"><col style="width:09%"><col style="width:08%"><col style="width:13%"><col style="width:08%"></colgroup>
    <tr class="center">
      <th>PELLET WT. RANGE AS PER PID (g)
          <td>
            <input type="text" name="pellet_weight[]" value="<?= h($_POST['pellet_weight'][$i]??'') ?>">
          </td>
      </th>
      <th>PELLET DIA (mm) (D)
          <td>
            <input type="text" name="pellet_dia" value="<?= h($_POST['pellet_dia'][$i]??'') ?>">
          </td>
      </th>
      <th>PELLET THK. RANGE AS PER PID (mm)
          <td>
            <input type="text" name="pellet_thk_range[]" value="<?= h($_POST['pellet_thk_range'][$i]??'') ?>">
          </td>
      </th>
      <th>PRESSURE (kg/cm²)
          <td>
            <input type="text" name="pressure[]" value="<?= h($_POST['pressure'][$i]??'') ?>">
          </td>
      </th>
      <th>COMPRESSION TIME (sec)
          <td>
            <input type="text" name="compression[]" value="<?= h($_POST['compression'][$i]??'') ?>">
          </td>
    </tr>
  </table>
  <table class="tbl narrow" style="margin-top:8px">
    <tr class="center"><th rowspan="2" style="width:10%">TIME OF INSPECTION</th>
      <th rowspan="2" style="width:10%">PELLET NO(s) (FROM - TO)</th>
      <th rowspan="2" style="width:09%">POWDER WEIGHT (g)</th>
      <th rowspan="2" style="width:09%">PELLET WEIGHT (g) (W)</th>
      <th colspan="5" style="width:26.9%">PELLET THICKNESS (mm) [REF. DWG.]</th>
      <th rowspan="2" style="width:09%">AVG PELLET THK (mm) (AT)</th>
      <th rowspan="2" style="width:09%">MAX-MIN (mm)</th>
      <th rowspan="2" style="width:07%">DENSITY (g/cm³)</th>
      <th rowspan="2" style="width:10%">INSPECTION BY</th>
    </tr>
    <tr class="center">
      <th>1</th>
      <th>2</th>
      <th>3</th>
      <th>4</th>
      <th>5</th>
    </tr>
    <?php for($i=0;$i<7;$i++): ?>
    <tr>
      <td><input type="time" name="p2_time[]" value="<?= h($_POST['p2_time'][$i]??'') ?>"></td>
      <td><input type="text" name="p2_range[]" value="<?= h($_POST['p2_range'][$i]??'') ?>"></td>
      <td><input type="text" name="p2_pow[]" value="<?= h($_POST['p2_pow'][$i]??'') ?>"></td>
      <td><input type="text" name="p2_w[]" value="<?= h($_POST['p2_w'][$i]??'') ?>"></td>
      <td><input type="text" name="p2_t1[]" value="<?= h($_POST['p2_t1'][$i]??'') ?>"></td>
      <td><input type="text" name="p2_t2[]" value="<?= h($_POST['p2_t2'][$i]??'') ?>"></td>
      <td><input type="text" name="p2_t3[]" value="<?= h($_POST['p2_t3'][$i]??'') ?>"></td>
      <td><input type="text" name="p2_t4[]" value="<?= h($_POST['p2_t4'][$i]??'') ?>"></td>
      <td><input type="text" name="p2_t5[]" value="<?= h($_POST['p2_t5'][$i]??'') ?>"></td>
      <td><input type="text" name="p2_avg[]" value="<?= h($_POST['p2_avg'][$i]??'') ?>"></td>
      <td><input type="text" name="p2_mm[]" value="<?= h($_POST['p2_mm'][$i]??'') ?>"></td>
      <td><input type="text" name="p2_density[]" value="<?= h($_POST['p2_density'][$i]??'') ?>"></td>
      <td><input type="text" name="p2_insp[]" value="<?= h($_POST['p2_insp'][$i]??'') ?>"></td>
    </tr>
    <?php endfor; ?>
  </table>

  <!-- Process log + barcode + O/P block -->
  <table class="tbl narrow" style="margin-top:8px">
    <colgroup><col style="width:47%"><col style="width:20%"><col style="width:33%"></colgroup>
    <tr><th class="center">PROCESS LOG</th><th class="center">O/P PRODUCT BARCODE</th><th class="center">O/P PRODUCT DETAILS</th></tr>
    <tr>
      <td style="padding:0">
        <table class="tbl narrow" style="border:none;border-top:0; height:280px;">
          <colgroup><col style="width:16.66%"><col style="width:16.66%"><col style="width:16.66%"><col style="width:16.66%"><col style="width:16.66%"><col style="width:16.66%"></colgroup>
          <tr class="center"><th>TIME</th><th>DEW POINT (°C)</th><th>BOTTLE WT (g)</th><th>TIME</th><th>DEW POINT (°C)</th><th>BOTTLE WT (g)</th></tr>
          <?php for($i=0;$i<6;$i++): ?>
          <tr>
            <td><input type="time" name="pl1_time[]" value="<?= h($_POST['pl1_time'][$i]??'') ?>"></td>
            <td><input type="text" name="pl1_dew[]" value="<?= h($_POST['pl1_dew'][$i]??'') ?>"></td>
            <td><input type="text" name="pl1_bw[]" value="<?= h($_POST['pl1_bw'][$i]??'') ?>"></td>
            <td><input type="time" name="pl2_time[]" value="<?= h($_POST['pl2_time'][$i]??'') ?>"></td>
            <td><input type="text" name="pl2_dew[]" value="<?= h($_POST['pl2_dew'][$i]??'') ?>"></td>
            <td><input type="text" name="pl2_bw[]" value="<?= h($_POST['pl2_bw'][$i]??'') ?>"></td>
          </tr>
          <?php endfor; ?>
        </table>
      </td>
      <td class="center">
        <!-- <IMG src="assets/barcode.png" alt="BARCODE" style="max-width:100%;height:auto;display:block;margin:0 auto"> -->
         <div style='text-align: center;'>
  <!-- insert your custom barcode setting your data in the GET parameter "data" -->
  <img style="width:15em;"alt='Barcode Generator TEC-IT'
       src='https://barcode.tec-it.com/barcode.ashx?data=LI-PRD-RC-28A/001&translate-esc=on'/>
</div>
      </td>
      <td style="padding:0">
        <table class="tbl narrow" style="border:none;border-top:0; height:280px;">
          <tr><th>O/P PRODUCT NAME</th><td><input type="text" name="op_name" value="<?= h(v('op_name','ANODE PELLETS')) ?>"></td></tr>
          <tr><th>O/P PRODUCT CODE</th><td><input type="text" name="op_code" value="<?= h(v('op_code','ANP-28-')) ?>"></td></tr>
          <tr><th>O/P PRODUCT LOT NO.</th><td><input type="text" name="op_lot" value="<?= h(v('op_lot','')) ?>"></td></tr>
          <tr><th>QTY PRODUCED (No's)</th><td><input type="text" name="qty_produced" value="<?= h(v('qty_produced','')) ?>"></td></tr>
          <tr><th>ACCEPTED (A)</th><td><input type="text" name="accepted_a" value="<?= h(v('accepted_a','')) ?>"></td></tr>
          <tr><th>TOTAL WT OF ACCEPTED PELLETS (g)</th><td><input type="text" name="accepted_weight" value="<?= h(v('accepted_weight','')) ?>"></td></tr>
          <tr>
            <th>REJECTED (R)</th>
            <td>
              <div class="rej-div" style="display: flex; text-align: center; padding: 0;">
                <li class="rej-list" style="list-style: none; display: flex; align-items: center; padding: 0;">
                  <label> T </label>
                  <input type="number" class="rej-row"></input>
                </li>
                <li class="rej-list" style="list-style: none; display: flex; align-items: center; padding: 0;">
                  <label> W </label>
                  <input type="number" class="rej-row"></input>
          </li>
                <li class="rej-list" style="list-style: none; display: flex; align-items: center; padding: 0;">
                  <label> B </label>
                  <input type="number" class="rej-row"></input>
                </li>
              </div>
            </td>
          </tr>
          <tr><th>YIELD (%)</th><td><input type="text" name="yield_pct" value="<?= h(v('yield_pct','')) ?>"></td></tr>
        </table>
      </td>
    </tr>
  </table>

  <table class="tbl narrow" style="margin-top:8px">
    <colgroup><col style="width:15%"><col style="width:15%"><col style="width:15%"><col style="width:15%"><col style="width:40%"></colgroup>
    <tr class="center">
      <th>ACTUAL MH</th><th>LOST MH</th><th>MH/PELLET</th><th>LEFTOVER QTY (g)</th><th>STORAGE CONTAINER WITH LABEL (POWDER)</th>
    </tr>
    <tr>
      <td><input type="text" name="actual_mh" value="<?= h(v('actual_mh','')) ?>"></td>
      <td><input type="text" name="lost_mh" value="<?= h(v('lost_mh','')) ?>"></td>
      <td><input type="text" name="mh_pellet" value="<?= h(v('mh_pellet','')) ?>"></td>
      <td><input type="text" name="leftover_qty" value="<?= h(v('leftover_qty','')) ?>"></td>
      <td class="center"><span class="box chk" data-target="storage_label"></span><input type="hidden" name="storage_label" value="<?= h(v('storage_label','')) ?>"></td>
    </tr>
  </table>

  <table class="tbl narrow" style="margin-top:8px">
    <colgroup><col style="width:25%"><col style="width:75%"></colgroup>
    <tr class="center"><th>OPERATOR NAME</th><th>PRD OBSERVATIONS/SIGNATURE</th></tr>
    <tr>
      <td><input type="text" name="operator_name" value="<?= h(v('operator_name','')) ?>"></td>
      <td><textarea name="prd_obs" style="min-height:70px"><?= h(v('prd_obs','')) ?></textarea></td>
    </tr>
  </table>

  <div class="footer-stamp"><span class="page-no">Page 2 of 2</span> • <span class="ts"></span></div>
</div>

</form>

<script>
/* tick / cross tri-state boxes */
function applyState(box, state){
  box.classList.remove('tick','cross'); box.textContent = '';
  if(state==='tick'){ box.classList.add('tick'); box.textContent='✓'; }
  else if(state==='cross'){ box.classList.add('cross'); box.textContent='✗'; }
}
function initCheckboxes(){
  document.querySelectorAll('.chk').forEach(box=>{
    const name = box.dataset.target;
    const hid = document.querySelector('input[type="hidden"][name="'+name+'"]');
    if(!hid) return;
    applyState(box, hid.value);
    box.addEventListener('click', ()=>{
      let s = hid.value;
      if(s===''){ s='tick'; }
      else if(s==='tick'){ s='cross'; }
      else { s=''; }
      hid.value = s; applyState(box, s);
    });
  });
}
initCheckboxes();

/* Replace inputs with text for preview/print; stamp time; keep page numbers */
function cloneForFinal(node){
  const clone = node.cloneNode(true);
  clone.querySelectorAll('input, textarea').forEach(el=>{
    if(el.type==='hidden'){ el.remove(); return; }
    const span = document.createElement('span');
    span.className='filled';
    let val = (el.value||'').trim();
    if(el.type==='date' && val){
      try{ const dt = new Date(val+'T00:00:00'); val = dt.toLocaleDateString(undefined,{day:'2-digit',month:'short',year:'numeric'}).replace(/ /g,'-'); }catch(e){}
    }
    span.textContent = val;
    el.parentNode.replaceChild(span, el);
  });
  clone.querySelectorAll('input[type="hidden"]').forEach(h=>h.remove());
  const ts = new Date().toLocaleString(undefined,{year:'numeric',month:'short',day:'2-digit',hour:'2-digit',minute:'2-digit',second:'2-digit'});
  clone.querySelectorAll('.footer-stamp .ts').forEach(e=>e.textContent='Printed on '+ts);
  const bar = clone.querySelector('.bar'); if(bar) bar.remove();
  return clone;
}
function openPreview(){
  const pagesWrap = document.createElement('div');
  document.querySelectorAll('.page').forEach(p=>pagesWrap.appendChild(cloneForFinal(p)));
  const w = window.open('', 'PREVIEW', 'width=1200,height=900');
  const headAssets = Array.from(document.querySelectorAll('link[rel="stylesheet"], style')).map(n=>n.outerHTML).join('');
  w.document.write('<html><head><title>Preview</title>'+headAssets+'</head><body>'+pagesWrap.innerHTML+'</body></html>');
  w.document.close(); w.focus();
}
function saveForm(){ document.getElementById('__action').value='save'; document.getElementById('logForm').submit(); }

/* Auto-preview from admin */
if (new URLSearchParams(window.location.search).get('autopreview')==='1'){
  window.addEventListener('load', ()=>setTimeout(openPreview, 400));
}

// ---- Auto-calc Avg Thickness and Max-Min for Page 1 (qi_) and Page 2 (p2_) ----
function setupThicknessCalcs(prefix) {
  const t1 = document.getElementsByName(prefix + 't1[]');
  const t2 = document.getElementsByName(prefix + 't2[]');
  const t3 = document.getElementsByName(prefix + 't3[]');
  const t4 = document.getElementsByName(prefix + 't4[]');
  const t5 = document.getElementsByName(prefix + 't5[]');
  const avg = document.getElementsByName(prefix + 'avg[]');
  const mm  = document.getElementsByName(prefix + 'mm[]');

  const cols = [t1, t2, t3, t4, t5];

  function parseNum(x){
    // allow "7.35", "7,35", trim spaces
    const v = parseFloat(String(x).replace(',', '.').trim());
    return isNaN(v) ? null : v;
  }

  function recalcRow(i){
    const vals = cols.map(list => list[i] ? parseNum(list[i].value) : null);
    if (vals.every(v => v !== null)) {
      const sum = vals.reduce((a,b)=>a+b, 0);
      const a = sum / 5;
      const lo = Math.min.apply(null, vals);
      const hi = Math.max.apply(null, vals);
      if (avg[i]) avg[i].value = a.toFixed(2);
      if (mm[i])  mm[i].value  = (hi - lo).toFixed(2);
    } else {
      if (avg[i]) avg[i].value = '';
      if (mm[i])  mm[i].value  = '';
    }
  }

  for (let i = 0; i < t1.length; i++) {
    cols.forEach(list => { if(list[i]) list[i].addEventListener('input', () => recalcRow(i)); });
    cols.forEach(list => { if(list[i]) list[i].addEventListener('input', () => calculateDensity(prefix, i)); });
    // run once on load to populate if values already present
    recalcRow(i);
  }
}

window.addEventListener('DOMContentLoaded', () => {
  setupThicknessCalcs('qi_'); // page 1
  setupThicknessCalcs('p2_'); // page 2
});

// Function to calculate the density (g/cm³) for both pages
function calculateDensity(prefix, i) {
  const pelletWeight = parseFloat(document.getElementsByName(prefix + 'w[]')[i]?.value || 0);

  // Get the single pellet diameter input (shared across pages)
  const diaEl = document.querySelector('[name="pellet_dia"]'); // Target the shared pellet diameter input
  const pelletDiameter = diaEl
    ? parseFloat(String(diaEl.value).replace(',', '.').trim()) || 0
    : 0;

  const avgpelletThickness = parseFloat(document.getElementsByName(prefix + 'avg[]')[i]?.value || 0); // Use 'avg' for thickness

  if (pelletWeight > 0 && pelletDiameter > 0 && avgpelletThickness > 0) {
    pelletRadius_CM = (pelletDiameter / 20);
    avgpelletThickness_CM = (avgpelletThickness / 10);

    console.log('pelletWeight:', pelletWeight, 'pelletRadius_CM:', pelletRadius_CM, 'avgpelletThickness_CM:', avgpelletThickness_CM);
    // Apply the formula for Density: (1.273 * W) / (D * D * AT)
    density = (pelletWeight) / (3.142 * pelletRadius_CM * pelletRadius_CM * avgpelletThickness_CM); // Formula
    // set density value with 3 decimal places without rounding
    density = Math.floor(density * 1000) / 1000;
    console.log('Calculated density:', density);
    // Set the density value in the corresponding input field
    document.getElementsByName(prefix + 'density[]')[i].value = density;

  } else {
    console.log('pelletWeight:', pelletWeight, 'pelletDiameter:', pelletDiameter, 'avgpelletThickness:', avgpelletThickness);
    document.getElementsByName(prefix + 'density[]')[i].value = ''; // Clear if invalid input
  }
}

// Event listeners to calculate density whenever there is a change in pellet weight, diameter, or thickness
function setupDensityCalculations(prefix) {
  console.log('Setting up density calculations for prefix:', prefix);
  const weightInputs = document.getElementsByName(prefix + 'w[]');
  console.log('Found weight inputs:', weightInputs);

  // We use the same diameter input for both pages
  const diaEl = document.querySelector('[name="pellet_dia"]');
  console.log('Found pellet diameter input element:', diaEl);

  const thkInputs = document.getElementsByName(prefix + 'avg[]');
  console.log('Found thickness inputs:', thkInputs);

  const thkAllInputs = document.getElementsByName(prefix + 'avg[]');


  for (let i = 0; i < weightInputs.length; i++) {
    weightInputs[i].addEventListener('input', () => calculateDensity(prefix, i));
    diaEl.addEventListener('input', () => calculateDensity(prefix, i)); // Trigger calculation on diameter change
    thkInputs[i].addEventListener('input', () => calculateDensity(prefix, i));
    
    // Run once on page load to populate if values already present
    calculateDensity(prefix, i);
  }
}

window.addEventListener('DOMContentLoaded', () => {
  setupDensityCalculations('qi_'); // Page 1
  setupDensityCalculations('p2_'); // Page 2
});


</script>
</body>
</html>
