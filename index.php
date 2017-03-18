<?php

// initial test getting simple arrival data from API SCHIPHOL
// author Dennis Slagers has no coding skills, so any hickup is due to this

$app_key='enter here api key';
$app_id='enter here app id';
$scheduletime=date("H:i");  

require_once("nicejson.php");

header('content-type: text/html; charset: utf-8');
echo <<<EOT
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
  <h1>JSON dump Schiphol Arrival Data Hour:MInute</h1>
  <pre>
EOT;

// Example usage

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.schiphol.nl/public-flights/flights?app_id=$app_id&app_key=$app_key&scheduletime=$scheduletime&flightdirection=A&includedelays=false&page=0&sort=%2Bscheduletime");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, -1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");


$headers = array();
$headers[] = "Accept: application/json";
$headers[] = "Resourceversion: v3";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
curl_close($ch);

if (substr($result, 0, 3) == "\xEF\xBB\xBF") {
  $result = substr($result, 3);
}
echo htmlspecialchars(json_format($result));

//var_dump(json_decode($result, true));

?>
