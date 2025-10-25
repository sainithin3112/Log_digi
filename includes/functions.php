<?php
$api_key= '9edd806e-173d-4ef5-8956-d3c1073de42d';
// $api_url= 'https://apis-funds.xweber.in';
$api_url = 'http://localhost/apis.digi';

function pr($arr){
    echo'<pre>';
    print_r($arr);
}
function prx($arr){
    echo'<pre>';
    print_r($arr);
    die();
}

function get_api_data($url, $api_key = null) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  // Add headers if an API Key is provided
  if ($api_key) {
      $headers = array(
          'X-API-Key: ' . $api_key  // Set the X-API-Key header 
      );
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  }

  $output = curl_exec($ch);
  curl_close($ch);
  return $output;
}

function get_api_data_post($url, $postData, $api_key, $fileData='') {
  $ch = curl_init();
  
  // Prepare the POST data
  if ($fileData == '') {
      $postData = http_build_query($postData);
  } else {
      $postData = array_merge($postData, array('prescriptionfile' => new CURLFile($fileData['tmp_name'], $fileData['type'], $fileData['name'])));
  }

  // Set the CURL options
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'X-API-Key: ' . $api_key, // Add X-API-Key header
  ));
  
  $output = curl_exec($ch);
  
  // Check for CURL errors
  if (curl_errno($ch)) {
      echo 'CURL Error: ' . curl_error($ch);
  }
  
  curl_close($ch);
  
  return $output;
}

?>