<?php

// initial test getting simple arrival data from API SCHIPHOL
// author Dennis Slagers has no coding skills, so any hickup is due to this
// Credits to [DAOS] from tweakers.net fixing the json reading  it was all about [] and {}



require_once("config.php");
require_once("nicejson.php");

header('content-type: text/html; charset: utf-8');
echo <<<EOT
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
  <h1>Schiphol Arrivals: $scheduletime</h1>
  <pre>
EOT;

// Example usage

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

$json = json_decode($result, true);


   if (count($json['flights'])) 
//   foreach ($json['flights'] as $flights)  

foreach ($json['flights'] as $flight)
{

    echo "Vluchtdatum: {$flight['scheduleDate']} <br />";
    echo "Vluchtnaam: {$flight['flightName']} <br />";
    echo "Geroosterde landingstijd: {$flight['scheduleTime']} <br />";
    echo "Verwachte landingstijd: {$flight['estimatedLandingTime']} <br />";
    echo "Daadwerkelijke landingstijd: {$flight['actualLandingTime']} <br />";
    echo "Geparkeerd aan Gate: {$flight['gate']} <br />";

    foreach ($flight['baggageClaim']['belts'] as $belt)
    {
        echo "Bagageband: {$belt} <br />";
    }

    foreach ($flight['route']['destinations'] as $departure)
    {
       echo "Vertrek luchthaven: {$departure} <br />";
    }

    foreach ($flight['codeshares']['codeshares'] as $joinedwith)
    {
      echo "Ook bekend onder Vluchtnummer: {$joinedwith} <br />";
    }

  
    $status_new = $flight['publicFlightState']['flightStates'][0];
    $status_old = $flight['publicFlightState']['flightStates'][1];

	   echo "Status van de vlucht is: $status_new <br />";
	   echo "Status van de vlucht was: $status_old <br />";


       echo "<br />";


}

 echo "<br />";

?>
