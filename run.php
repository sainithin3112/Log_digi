<?php
function safe_slug($s){ return preg_replace('~[^a-z0-9]+~i','_', $s); }
function find_template($id){
  $m = json_decode(file_get_contents(__DIR__ . '/templates/manifest.json'), true);
  foreach(($m['templates']??[]) as $t){ if($t['id']===$id) return $t; }
  return null;
}
$id = $_GET['id'] ?? '';
$t = $id ? find_template($id) : null;
if(!$t){ http_response_code(404); echo 'Template not found'; exit; }
$TEMPLATE_ID = $t['id'];
$TEMPLATE_FILE = __DIR__ . '/templates/' . $t['file'];
if(!file_exists($TEMPLATE_FILE)){ http_response_code(404); echo 'Template file missing'; exit; }
include $TEMPLATE_FILE;
