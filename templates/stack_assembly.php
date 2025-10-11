<?php
// Template: Stack Assembly (LI-PRD-RC-13/2S4P)
// Self-contained form that saves to data/<safe(TEMPLATE_ID)>.json
date_default_timezone_set('Asia/Kolkata');

// Template ID and target file
$TEMPLATE_ID = 'LI-PRD-RC-13/2S4P';
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function v($name, $def=''){ return isset($_POST[$name]) ? $_POST[$name] : $def; }
// function safe_slug($s){ return preg_replace('~[^a-z0-9]+~i','_', $s); }
$SAFE = safe_slug($TEMPLATE_ID);
$logfile = __DIR__ . '/../data/' . $SAFE . '.json';

// Load an existing entry by uid to rehydrate the form
if (isset($_GET['load']) && $_GET['load']!==''){
  $uid = $_GET['load'];
  $arr = json_decode(@file_get_contents($logfile), true) ?: [];
  foreach($arr as $e){
    if(($e['meta']['uid']??'') === $uid){

      // Fill POST to repopulate fields
      $_POST['wi_name'] = $e['header1']['wi_name']??'STACK ASSEMBLY';
      $_POST['wi_no'] = $e['header1']['wi_no']??'WI-PRD-13';
      $_POST['log_id'] = $e['header1']['log_id']??$TEMPLATE_ID;
      $_POST['log_sr_no'] = $e['header1']['log_sr_no']??'';
      $_POST['hazard_ids'] = $e['header1']['hazard_ids']??'R07-20, R07-21, R07-22';
      $_POST['risk_rank'] = $e['header1']['risk_rank']??'MEDIUM';
      // sec1
      foreach(($e['sec1']['operator_checks']??[]) as $k=>$val){ $_POST['chk_'.$k]=$val; }
      foreach(($e['sec1']['earthing']??[]) as $k=>$val){ $_POST['chk_e_'.$k]=$val; }
      $_POST['chk_clearance'] = $e['sec1']['safety_clearance']??'';
      // sec2
      $_POST['gb_id'] = $e['sec2']['ids']['glove_box_id']??'';
      $_POST['bal_id'] = $e['sec2']['ids']['balance_id']??'';
      $_POST['hp_id'] = $e['sec2']['ids']['hpress_id']??'';
      $_POST['ver_id'] = $e['sec2']['ids']['vernier_id']??'';
      $_POST['cl_gb'] = $e['sec2']['cleaning']['gb']??'';
      $_POST['cl_bal'] = $e['sec2']['cleaning']['bal']??'';
      $_POST['cl_hp'] = $e['sec2']['cleaning']['hp']??'';
      $_POST['cl_ver'] = $e['sec2']['cleaning']['ver']??'';
      $_POST['ppm_val'] = $e['sec2']['ppm']??'';
      $_POST['cal_bal'] = $e['sec2']['calibration']['bal']??'';
      $_POST['cal_hp'] = $e['sec2']['calibration']['hp']??'';
      $_POST['cal_ver'] = $e['sec2']['calibration']['ver']??'';
      $_POST['u_clean'] = $e['sec2']['utilities']['cleaning']??'';
      $_POST['u_tray'] = $e['sec2']['utilities']['tray']??'';
      $_POST['u_forceps'] = $e['sec2']['utilities']['forceps']??'';
      $_POST['u_ram'] = $e['sec2']['utilities']['ram']??'';
      $_POST['u_bpa'] = $e['sec2']['utilities']['brace_plate_adapter']??'';
      $_POST['u_hit'] = $e['sec2']['utilities']['hitting_bit']??'';
      // sec3
      $_POST['mat_name'] = array_column($e['sec3_materials']??[], 'name');
      $_POST['mat_code'] = array_column($e['sec3_materials']??[], 'code');
      $_POST['mat_lot']  = array_column($e['sec3_materials']??[], 'lot');
      $_POST['mat_qty']  = array_column($e['sec3_materials']??[], 'qty');
      $_POST['mat_exp']  = array_column($e['sec3_materials']??[], 'expiry');
      // sec4
      $_POST['pl_time'] = array_column($e['sec4_process_log']??[], 'time');
      $_POST['pl_ppm']  = array_column($e['sec4_process_log']??[], 'ppm');
      $_POST['pl_stack']= array_column($e['sec4_process_log']??[], 'stack');
      $_POST['pl_clamp']= array_column($e['sec4_process_log']??[], 'clamp');
      // sec5
      foreach(($e['sec5_output']??[]) as $k=>$v){ $_POST[$k] = $v; }
      break;
    }
  }
}

$save_message = '';

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
      'wi_name'=>v('wi_name','STACK ASSEMBLY'),
      'wi_no'=>v('wi_no','WI-PRD-13'),
      'log_id'=>v('log_id',$TEMPLATE_ID),
      'log_sr_no'=>v('log_sr_no',''),
      'hazard_ids'=>v('hazard_ids','R07-20, R07-21, R07-22'),
      'risk_rank'=>v('risk_rank','MEDIUM'),
      'consequence_rank'=>['E'=>2,'M'=>2,'F'=>2,'R'=>2],
    ],
    'header2'=>[
      'op_name'=>v('op_name',''),
      'op_code'=>v('op_code',''),
      'op_lot'=>v('op_lot',''),
      'qty'=>v('qty',''),
      'std_time'=>v('std_time',''),
      'operator'=>v('operator',''),
      'date'=>v('date',''),
    ],
    'sec1'=>[
      'operator_checks'=>[
        'apron'=>v('chk_apron',''),
        'gloves'=>v('chk_gloves',''),
        'mask'=>v('chk_mask',''),
        'glove_box'=>v('chk_gb',''),
        'h_press'=>v('chk_hp',''),
        'operator'=>v('chk_op',''),
      ],
      'earthing'=>[
        'glove_box'=>v('chk_e_gb',''),
        'h_press'=>v('chk_e_hp',''),
        'operator'=>v('chk_e_op',''),
      ],
      'safety_clearance'=>v('chk_clearance','')
    ],
    'sec2'=>[
      'ids'=>[
        'glove_box_id'=>v('gb_id',''),
        'balance_id'=>v('bal_id',''),
        'hpress_id'=>v('hp_id',''),
        'vernier_id'=>v('ver_id',''),
      ],
      'cleaning'=>[
        'gb'=>v('cl_gb',''),
        'bal'=>v('cl_bal',''),
        'hp'=>v('cl_hp',''),
        'ver'=>v('cl_ver',''),
      ],
      'ppm'=>v('ppm_val',''),
      'calibration'=>[
        'bal'=>v('cal_bal',''),
        'hp'=>v('cal_hp',''),
        'ver'=>v('cal_ver',''),
      ],
      'utilities'=>[
        'cleaning'=>v('u_clean',''),
        'tray'=>v('u_tray',''),
        'forceps'=>v('u_forceps',''),
        'ram'=>v('u_ram',''),
        'brace_plate_adapter'=>v('u_bpa',''),
        'hitting_bit'=>v('u_hit',''),
      ]
    ],
    'sec3_materials'=>[],
    'sec4_process_log'=>[],
    'sec5_output'=>[
      'stack_qty'=>v('stack_qty',''),
      'accepted'=>v('accepted',''),
      'rejected'=>v('rejected',''),
      'yield'=>v('yield',''),
      'mh_stack'=>v('mh_stack',''),
      'material_loss'=>v('mat_loss',''),
      'pct_loss'=>v('pct_loss',''),
      'actual_mh'=>v('actual_mh',''),
      'lost_mh'=>v('lost_mh',''),
      'observations'=>v('observations',''),
    ]
  ];

  // Section 3 materials
  $mat_names = $_POST['mat_name'] ?? [];
  $mat_code  = $_POST['mat_code'] ?? [];
  $mat_lot   = $_POST['mat_lot'] ?? [];
  $mat_qty   = $_POST['mat_qty'] ?? [];
  $mat_exp   = $_POST['mat_exp'] ?? [];
  $N = max(count($mat_names), count($mat_code), count($mat_lot), count($mat_qty), count($mat_exp));
  for ($i=0; $i<$N; $i++) {
    $entry['sec3_materials'][] = [
      'name'=>$mat_names[$i]??'',
      'code'=>$mat_code[$i]??'',
      'lot'=>$mat_lot[$i]??'',
      'qty'=>$mat_qty[$i]??'',
      'expiry'=>$mat_exp[$i]??'',
    ];
  }

  // Section 4 process log
  $t = $_POST['pl_time'] ?? [];
  $p = $_POST['pl_ppm'] ?? [];
  $s = $_POST['pl_stack'] ?? [];
  $c = $_POST['pl_clamp'] ?? [];
  $n = max(count($t),count($p),count($s),count($c));
  for ($i=0; $i<$n; $i++) {
    if (($t[$i]??'')!=='' || ($p[$i]??'')!=='' || ($s[$i]??'')!=='' || ($c[$i]??'')!=='') {
      $entry['sec4_process_log'][] = ['time'=>$t[$i]??'', 'ppm'=>$p[$i]??'', 'stack'=>$s[$i]??'', 'clamp'=>$c[$i]??''];
    }
  }

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
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>STACK ASSEMBLY – <?= h($TEMPLATE_ID) ?></title>
<style>
  :root{ --ink:#111; --blue:#0A66FF; --bd2:2px solid #000; --bd1:1px solid #000; --ok:#0a8a00; --bad:#c1121f; }
  *{box-sizing:border-box}
  html,body{margin:0;padding:0;background:#fff;color:var(--ink);font:13px/1.25 "Segoe UI", Arial, Helvetica, sans-serif}
  .page{width:1100px;margin:16px auto;padding:8px;border:var(--bd2)}
  .tbl{width:100%;border-collapse:collapse;border:var(--bd2);table-layout:fixed}
  .tbl th,.tbl td{border:var(--bd1);padding:6px;vertical-align:middle}
  .narrow td,.narrow th{padding:4px}
  .center{text-align:center}
  .bold{font-weight:700}
  input[type="text"], input[type="number"], input[type="date"], textarea{ width:100%;border:none;outline:none;padding:2px 4px;font:inherit;background:#fff;text-align:center; }
  textarea{resize:vertical;min-height:84px}
  .sec-title{background:#f3f6ff;font-weight:700}
  .logo-cell{padding:0;vertical-align:middle}
  .logo{display:flex;align-items:center;gap:8px;padding-left:8px}
  .logo svg{height:34px}
  .urow{display:flex;flex-wrap:wrap;gap:12px;align-items:center}
  .urow .itm{display:flex;align-items:center;gap:6px;margin-right:6px}
  .box{display:inline-flex;align-items:center;justify-content:center;min-width:24px;height:20px;border:var(--bd1);user-select:none;cursor:pointer}
  .chk.tick{color:var(--ok);font-weight:900}
  .chk.cross{color:var(--bad);font-weight:900}
  .note{font-size:12px}
  .bar{position:sticky;top:0;background:#fff;padding:8px 0;display:flex;gap:10px;justify-content:flex-end}
  .btn{border:1px solid #444;background:#0a0a0a;color:#fff;padding:8px 12px;border-radius:6px;cursor:pointer}
  .btn.secondary{background:#e9eefb;color:#111;border-color:#9bb2ff}
  .btn.light{background:#fff;color:#111;border-color:#999}
  .msg{margin:8px auto;width:1100px}
  .ok{background:#e8f7ea;border:1px solid #9bd3a0;padding:8px;border-radius:6px}
  .err{background:#fde8e9;border:1px solid #f29ca3;padding:8px;border-radius:6px}
  .footer-stamp{margin-top:6px;text-align:center;font-size:12px;color:#000}
  @media print{ .bar{display:none} .page{margin:0;border:none;width:auto} }
  .filled{display:block;min-height:18px;text-align:center;white-space:pre-wrap}
  .consq .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:0}
  .consq .grid div{border:var(--bd1);text-align:center;padding:4px 0}
  .help{font-size:12px;color:#555;margin-left:auto;margin-right:0}
</style>
</head>
<body>

<?php if($save_message): ?>
  <div class="msg <?php echo (strpos($save_message,'ERROR')===false?'ok':'err'); ?>"><?php echo h($save_message); ?></div>
<?php endif; ?>

<form method="post" id="logForm">
<input type="hidden" name="__action" id="__action" value="">
<div class="page" id="pageRoot">

  <div class="bar">
    <span class="help">Tip: Click any small box to toggle ◻ → ✓ (green) → ✗ (red)</span>
    <a class="btn light" href="../index.php">Home</a>
    <a class="btn light" href="../admin.php" target="_blank">Admin</a>
    <button type="button" class="btn light" onclick="openPreview()">Preview</button>
    <button type="button" class="btn" onclick="saveForm()">Save</button>
    <button type="button" class="btn secondary" onclick="window.print()">Print (as-is)</button>
  </div>

  <!-- HEADER BLOCK 1 -->
  <table class="tbl narrow">
    <colgroup>
      <col style="width:10%"><col style="width:22%"><col style="width:10%"><col style="width:16%">
      <col style="width:10%"><col style="width:12%"><col style="width:10%"><col style="width:10%">
    </colgroup>
    <tr>
      <th class="center"> </th>
      <th class="center">NAME OF WORK INSTRUCTION (WI)</th>
      <th class="center">WI NO.</th>
      <th class="center">LOG ID</th>
      <th class="center">LOG SR NO.</th>
      <th class="center">HAZARD ID NO.</th>
      <th class="center">RISK RANK</th>
      <th class="center">CONSEQUENCE RANK</th>
    </tr>
    <tr>
      <td class="logo-cell">
        <div class="logo">
          <svg viewBox="0 0 120 40" aria-label="RES Logo">
            <polygon points="0,5 84,5 110,20 84,35 0,35" fill="#0A66FF"></polygon>
            <text x="18" y="27" font-size="20" font-family="Segoe UI, Arial" font-weight="800" fill="#fff" >R E S</text>
          </svg>
        </div>
      </td>
      <td class="center"><input type="text" name="wi_name" value="<?php echo h(v('wi_name','STACK ASSEMBLY')); ?>"></td>
      <td class="center"><input type="text" name="wi_no" value="<?php echo h(v('wi_no','WI-PRD-13')); ?>"></td>
      <td class="center"><input type="text" name="log_id" value="<?php echo h(v('log_id',$TEMPLATE_ID)); ?>"></td>
      <td class="center"><input type="text" name="log_sr_no" value="<?php echo h(v('log_sr_no','')); ?>"></td>
      <td class="center"><input type="text" name="hazard_ids" value="<?php echo h(v('hazard_ids','R07-20, R07-21, R07-22')); ?>"></td>
      <td class="center"><input type="text" name="risk_rank" value="<?php echo h(v('risk_rank','MEDIUM')); ?>"></td>
      <td class="center consq">
        <div class="grid">
          <div class="bold">E</div><div class="bold">M</div><div class="bold">F</div><div class="bold">R</div>
          <div>2</div><div>2</div><div>2</div><div>2</div>
        </div>
      </td>
    </tr>
  </table>

  <!-- HEADER BLOCK 2 -->
  <table class="tbl narrow" style="margin-top:8px">
    <colgroup>
      <col style="width:17%"><col style="width:13%"><col style="width:15%"><col style="width:10%"><col style="width:12%"><col style="width:13%"><col style="width:10%">
    </colgroup>
    <tr class="center">
      <th>O/P PRODUCT NAME</th><th>O/P PRODUCT CODE</th><th>O/P PRODUCT LOT NO</th><th>QTY (No's)</th><th>STD PROCESS TIME</th><th>OPERATOR</th><th>DATE</th>
    </tr>
    <tr>
      <td><input type="text" name="op_name" value="<?php echo h(v('op_name','')); ?>"></td>
      <td><input type="text" name="op_code" value="<?php echo h(v('op_code','')); ?>"></td>
      <td><input type="text" name="op_lot" value="<?php echo h(v('op_lot','')); ?>"></td>
      <td><input type="number" name="qty" value="<?php echo h(v('qty','')); ?>"></td>
      <td><input type="text" name="std_time" value="<?php echo h(v('std_time','')); ?>"></td>
      <td><input type="text" name="operator" value="<?php echo h(v('operator','')); ?>"></td>
      <td><input type="date" name="date" value="<?php echo h(v('date','')); ?>"></td>
    </tr>
  </table>

  <!-- SECTION 1 -->
  <table class="tbl" style="margin-top:8px">
    <colgroup><col style="width:45%"><col style="width:30%"><col style="width:25%"></colgroup>
    <tr><th class="sec-title">1&nbsp;&nbsp;SAFETY CHECKS (OPERATOR)</th><th class="sec-title">SAFETY CHECKS (EARTHING)</th><th class="sec-title center">COMMENTS</th></tr>
    <tr>
      <td>
        <div class="urow">
          <div class="itm"><span class="bold">APRON</span><span class="box chk" data-target="chk_apron"></span><input type="hidden" name="chk_apron" value="<?php echo h(v('chk_apron','')); ?>"></div>
          <div class="itm"><span class="bold">GLOVES</span><span class="box chk" data-target="chk_gloves"></span><input type="hidden" name="chk_gloves" value="<?php echo h(v('chk_gloves','')); ?>"></div>
          <div class="itm"><span class="bold">MASK</span><span class="box chk" data-target="chk_mask"></span><input type="hidden" name="chk_mask" value="<?php echo h(v('chk_mask','')); ?>"></div>
          <div class="itm"><span class="bold">GLOVE BOX</span><span class="box chk" data-target="chk_gb"></span><input type="hidden" name="chk_gb" value="<?php echo h(v('chk_gb','')); ?>"></div>
          <div class="itm"><span class="bold">H PRESS</span><span class="box chk" data-target="chk_hp"></span><input type="hidden" name="chk_hp" value="<?php echo h(v('chk_hp','')); ?>"></div>
          <div class="itm"><span class="bold">OPERATOR</span><span class="box chk" data-target="chk_op"></span><input type="hidden" name="chk_op" value="<?php echo h(v('chk_op','')); ?>"></div>
        </div>
      </td>
      <td>
        <div class="urow">
          <div class="itm"><span class="bold">GLOVE BOX</span><span class="box chk" data-target="chk_e_gb"></span><input type="hidden" name="chk_e_gb" value="<?php echo h(v('chk_e_gb','')); ?>"></div>
          <div class="itm"><span class="bold">H PRESS</span><span class="box chk" data-target="chk_e_hp"></span><input type="hidden" name="chk_e_hp" value="<?php echo h(v('chk_e_hp','')); ?>"></div>
          <div class="itm"><span class="bold">OPERATOR</span><span class="box chk" data-target="chk_e_op"></span><input type="hidden" name="chk_e_op" value="<?php echo h(v('chk_e_op','')); ?>"></div>
        </div>
      </td>
      <td><div class="urow"><div class="bold">SAFETY CLEARANCE</div><span class="box chk" style="min-width:60px" data-target="chk_clearance"></span><input type="hidden" name="chk_clearance" value="<?php echo h(v('chk_clearance','')); ?>"></div></td>
    </tr>
  </table>

  <!-- SECTION 2 & 3 -->
  <table class="tbl narrow" style="margin-top:8px">
    <colgroup><col style="width:50%"><col style="width:50%"></colgroup>
    <tr><th class="sec-title">2&nbsp;&nbsp;WORK STATION PREPARATION AND INITIALIZATION</th><th class="sec-title">3&nbsp;&nbsp;INPUT MATERIAL IDENTIFICATION</th></tr>
    <tr>
      <td style="padding:0">
        <table class="tbl narrow" style="border:none;border-top:0">
          <colgroup><col style="width:25%"><col style="width:25%"><col style="width:25%"><col style="width:25%"></colgroup>
          <tr class="center"><th>GLOVE BOX ID</th><th>BALANCE ID</th><th>H PRESS ID</th><th>VERNIER ID</th></tr>
          <tr>
            <td><input type="text" name="gb_id" value="<?php echo h(v('gb_id','')); ?>"></td>
            <td><input type="text" name="bal_id" value="<?php echo h(v('bal_id','')); ?>"></td>
            <td><input type="text" name="hp_id" value="<?php echo h(v('hp_id','')); ?>"></td>
            <td><input type="text" name="ver_id" value="<?php echo h(v('ver_id','')); ?>"></td>
          </tr>
          <tr class="center"><th>CLEANING</th><th>CLEANING</th><th>CLEANING</th><th>CLEANING</th></tr>
          <tr>
            <td class="center"><span class="box chk" data-target="cl_gb"></span><input type="hidden" name="cl_gb" value="<?php echo h(v('cl_gb','')); ?>"></td>
            <td class="center"><span class="box chk" data-target="cl_bal"></span><input type="hidden" name="cl_bal" value="<?php echo h(v('cl_bal','')); ?>"></td>
            <td class="center"><span class="box chk" data-target="cl_hp"></span><input type="hidden" name="cl_hp" value="<?php echo h(v('cl_hp','')); ?>"></td>
            <td class="center"><span class="box chk" data-target="cl_ver"></span><input type="hidden" name="cl_ver" value="<?php echo h(v('cl_ver','')); ?>"></td>
          </tr>
          <tr class="center"><th>PPM : &lt; 40</th><th>CALIBRATION</th><th>CALIBRATION</th><th>CALIBRATION</th></tr>
          <tr>
            <td><input type="text" name="ppm_val" value="<?php echo h(v('ppm_val','')); ?>"></td>
            <td class="center"><span class="box chk" data-target="cal_bal"></span><input type="hidden" name="cal_bal" value="<?php echo h(v('cal_bal','')); ?>"></td>
            <td class="center"><span class="box chk" data-target="cal_hp"></span><input type="hidden" name="cal_hp" value="<?php echo h(v('cal_hp','')); ?>"></td>
            <td class="center"><span class="box chk" data-target="cal_ver"></span><input type="hidden" name="cal_ver" value="<?php echo h(v('cal_ver','')); ?>"></td>
          </tr>
          <tr class="center"><th colspan="4">UTILITIES</th></tr>
          <tr>
            <td class="center">CLEANING OF UTILITIES <span class="box chk" data-target="u_clean"></span><input type="hidden" name="u_clean" value="<?php echo h(v('u_clean','')); ?>"></td>
            <td class="center">TRAY <span class="box chk" data-target="u_tray"></span><input type="hidden" name="u_tray" value="<?php echo h(v('u_tray','')); ?>"></td>
            <td class="center">FORCEPS <span class="box chk" data-target="u_forceps"></span><input type="hidden" name="u_forceps" value="<?php echo h(v('u_forceps','')); ?>"></td>
            <td class="center">RAM <span class="box chk" data-target="u_ram"></span><input type="hidden" name="u_ram" value="<?php echo h(v('u_ram','')); ?>">  BRACE PLATE ADAPTER <span class="box chk" data-target="u_bpa"></span><input type="hidden" name="u_bpa" value="<?php echo h(v('u_bpa','')); ?>">  HITTING BIT <span class="box chk" data-target="u_hit"></span><input type="hidden" name="u_hit" value="<?php echo h(v('u_hit','')); ?>"></td>
          </tr>
        </table>
      </td>
      <td style="padding:0">
        <table class="tbl narrow" style="border:none;border-top:0">
          <colgroup><col style="width:40%"><col style="width:15%"><col style="width:15%"><col style="width:15%"><col style="width:15%"></colgroup>
          <tr class="center"><th>NAME</th><th>CODE</th><th>LOT NO.</th><th>QTY (No's)</th><th>EXPIRY DATE</th></tr>
          <?php $mat_rows = ['ANODE PELLETS','DRIED CATHODE PELLETS','DRIED ELECTROLYTE PELLETS','DRIED HEAT PELLET - 1','DRIED HEAT PELLET - 2','DRIED HEAT PELLET - 3']; for($i=0;$i<count($mat_rows);$i++): ?>
          <tr>
            <td class="bold center"><?php echo h($mat_rows[$i]); ?><input type="hidden" name="mat_name[]" value="<?php echo h($mat_rows[$i]); ?>"></td>
            <td><input type="text" name="mat_code[]" value="<?php echo h($_POST['mat_code'][$i]??''); ?>"></td>
            <td><input type="text" name="mat_lot[]" value="<?php echo h($_POST['mat_lot'][$i]??''); ?>"></td>
            <td><input type="text" name="mat_qty[]" value="<?php echo h($_POST['mat_qty'][$i]??''); ?>"></td>
            <td><input type="date" name="mat_exp[]" value="<?php echo h($_POST['mat_exp'][$i]??''); ?>"></td>
          </tr>
          <?php endfor; ?>
        </table>
      </td>
    </tr>
  </table>

  <!-- SECTION 4: PROCESS LOG -->
  <table class="tbl narrow" style="margin-top:8px">
    <colgroup><col style="width:15%"><col style="width:15%"><col style="width:35%"><col style="width:35%"></colgroup>
    <tr><th class="sec-title" colspan="4">4&nbsp;&nbsp;PROCESS LOG</th></tr>
    <tr class="center"><th>TIME</th><th>PPM</th><th>STACK PRESSURE (kg/cm²)</th><th>CLAMP PRESSURE (kg/cm²)</th></tr>
    <?php for($i=0;$i<7;$i++): ?>
      <tr>
        <td><input type="text" name="pl_time[]" value="<?php echo h($_POST['pl_time'][$i]??''); ?>"></td>
        <td><input type="text" name="pl_ppm[]" value="<?php echo h($_POST['pl_ppm'][$i]??''); ?>"></td>
        <td><input type="text" name="pl_stack[]" value="<?php echo h($_POST['pl_stack'][$i]??''); ?>"></td>
        <td><input type="text" name="pl_clamp[]" value="<?php echo h($_POST['pl_clamp'][$i]??''); ?>"></td>
      </tr>
    <?php endfor; ?>
    <tr>
      <td colspan="4" class="note">
        <strong>* NOTE:</strong><br>
        1. ATTACH PICTURES OF STACK TO THE LOG SHEET<br>
        2. PELLETS WEIGHT INSPECTION – [R07-20]<br>
        3. PELLETS STACKING – [R07-21]<br>
        4. STACK PRESS – [R07-22]*
      </td>
    </tr>
  </table>

  <!-- SECTION 5: OUTPUT IDENTIFICATION -->
  <table class="tbl narrow" style="margin-top:8px">
    <colgroup><col style="width:12%"><col style="width:12%"><col style="width:12%"><col style="width:12%"><col style="width:12%"><col style="width:12%"><col style="width:14%"><col style="width:14%"></colgroup>
    <tr><th class="sec-title" colspan="8">5&nbsp;&nbsp;OUTPUT MATERIAL IDENTIFICATION</th></tr>
    <tr class="center"><th>STACK QTY</th><th>ACCEPTED</th><th>REJECTED</th><th>YIELD</th><th>MH/STACK</th><th>MATERIAL LOSS</th><th>% LOSS</th><th>ACTUAL MH</th></tr>
    <tr>
      <td><input type="text" name="stack_qty" value="<?php echo h(v('stack_qty','')); ?>"></td>
      <td><input type="text" name="accepted" value="<?php echo h(v('accepted','')); ?>"></td>
      <td><input type="text" name="rejected" value="<?php echo h(v('rejected','')); ?>"></td>
      <td><input type="text" name="yield" value="<?php echo h(v('yield','')); ?>"></td>
      <td><input type="text" name="mh_stack" value="<?php echo h(v('mh_stack','')); ?>"></td>
      <td><input type="text" name="mat_loss" value="<?php echo h(v('mat_loss','')); ?>"></td>
      <td><input type="text" name="pct_loss" value="<?php echo h(v('pct_loss','')); ?>"></td>
      <td><input type="text" name="actual_mh" value="<?php echo h(v('actual_mh','')); ?>"></td>
    </tr>
    <tr class="center"><th colspan="6">PRD OBSERVATIONS/SIGNATURE</th><th>LOST MH</th><th> </th></tr>
    <tr>
      <td colspan="6"><textarea name="observations"><?php echo h(v('observations','')); ?></textarea></td>
      <td><input type="text" name="lost_mh" value="<?php echo h(v('lost_mh','')); ?>"></td>
      <td> </td>
    </tr>
  </table>

  <div id="printStamp" class="footer-stamp"></div>
</div>
</form>

<script>
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
  const stamp = clone.querySelector('#printStamp');
  if(stamp){
    const now = new Date();
    const s = now.toLocaleString(undefined,{year:'numeric',month:'short',day:'2-digit',hour:'2-digit',minute:'2-digit',second:'2-digit'});
    stamp.textContent = 'Printed on ' + s;
  }
  const bar = clone.querySelector('.bar'); if(bar) bar.remove();
  return clone;
}
function openPreview(){
  const root = document.getElementById('pageRoot');
  const finalNode = cloneForFinal(root);
  const w = window.open('', 'PREVIEW', 'width=1200,height=900');
  const css = document.querySelector('style').outerHTML;
  w.document.write('<html><head><title>Preview</title>'+css+'</head><body>'+finalNode.outerHTML+'</body></html>');
  w.document.close(); w.focus();
}
function saveForm(){ document.getElementById('__action').value='save'; document.getElementById('logForm').submit(); }

// Auto-preview if requested (used by admin "Open")
if (new URLSearchParams(window.location.search).get('autopreview')==='1'){
  window.addEventListener('load', ()=>setTimeout(openPreview, 400));
}
</script>

</body>
</html>
