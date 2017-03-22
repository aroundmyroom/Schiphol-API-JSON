<?php

// initial test getting simple arrival data from API SCHIPHOL
// author Dennis Slagers has no coding skills, so any hickup is due to this

require_once("config.php");
//require_once("nicejson.php");

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "$base_url/public-flights/flights?app_id=$app_id&app_key=$app_key&scheduletime=$scheduletime&flightdirection=A&includedelays=false&page=0&sort=%2Bscheduletime");

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
// echo htmlspecialchars(json_format($result));

echo $result;

//var_dump (json_decode($result));


?>
