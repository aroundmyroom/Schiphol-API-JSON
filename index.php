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

curl_setopt($ch, CURLOPT_URL, "$base_url/public-flights/flights?app_id=$app_id&app_key=$app_key&scheduletime=$scheduletime&flightdirection=A&includedelays=true&page=0&sort=%2Bscheduletime");

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

$eta = substr($flight['estimatedLandingTime'], strpos($flight['estimatedLandingTime'], "T") +1,8);
$eta1=strtotime($eta);
$eta2=strtotime($flight['scheduleTime']);
$eta3 = abs($eta1-$eta2);
$vertraging = gmdate("H:i:s", $eta3);
//$vertraging = gmdate("H:i:s", ($eta1-$eta2));

// debug en test info nog even laten staan

//if ($eta1 > $eta2):

//  echo "Vlucht is vertraagd ($vertraging)<br />";

// elseif ($eta1 == $eta2):
 
//    echo "Perfect op tijd <br />";

//  else:

//   echo "Vlucht eerder geland ($vertraging) <br />";
// endif;


//echo strtotime($eta);
//echo "<br />";
//echo strtotime($flight['scheduleTime']);
//echo "<br /><br />";

    echo "Vluchtdatum: {$flight['scheduleDate']} <br />";
    echo "Vluchtnaam: {$flight['flightName']} <br />";
    echo "Geroosterde landingstijd: {$flight['scheduleTime']} <br />";
//    echo "Verwachte landingstijd: {$flight['estimatedLandingTime']} <br />";
    echo "Verwachte landingstijd: $eta <br />";


if ($eta1 > $eta2):

  echo "Vlucht heeft nieuwe aankomsttijd. (Verschil: $vertraging)<br />";

 elseif ($eta1 == $eta2):

    echo "Perfect op tijd <br />";

  else:

   echo "Vlucht land waarschijnlijk eerder ($vertraging) <br />";
endif;


    echo "Daadwerkelijke landingstijd: {$flight['actualLandingTime']} <br />";
    echo "Geparkeerd aan Gate: {$flight['gate']} <br />";
    echo "Aankomsthal: {$flight['terminal']} <br />";



    foreach ($flight['baggageClaim']['belts'] as $belt)
    {
        echo "Bagageband: {$belt} <br />";
    }

    foreach ($flight['codeshares']['codeshares'] as $joinedwith)
    {
     echo "Ook bekend onder Vluchtnummer: {$joinedwith} <br />";
    }

    foreach ($flight['route']['destinations'] as $departure)
    {

$ch2 = curl_init();

curl_setopt($ch2, CURLOPT_URL, "$base_url/public-flights/destinations/$departure?app_id=$app_id&app_key=$app_key");

curl_setopt($ch2, CURLOPT_RETURNTRANSFER, -1);
curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "GET");

$headers = array();
$headers[] = "Accept: application/json";
$headers[] = "Resourceversion: v1";
curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);

$results2 = curl_exec($ch2);
curl_close($ch2);

if (substr($results2, 0, 3) == "\xEF\xBB\xBF") {
  $results = substr($results2, 3);
}

$json2 = json_decode($results2, true);

// foreach ($json2{'city'} as $city)

$city = ($json2{'city'});
$country = ($json2{'country'});
$nlcity = ($json2['publicName']{'dutch'});

       echo "Vertrokken van luchthaven: $nlcity <br />";
       echo "Vertrokken uit: $country <br />";
       echo "City code: $departure <br />";

//       echo "Vertrek luchthaven: {$departure} <br />";

    }


    $status_new = $flight['publicFlightState']['flightStates'][0];
    $status_old = $flight['publicFlightState']['flightStates'][1];

if (empty($status_new)) {
    echo "Geen vluchtstatus momenteel <br />";
}


 if(!empty($status_new)){
     switch($status_new) {
       case "": 
        Echo "Geen geldige waarde gevonden";
       break;

      case "SCH":
         echo "Vlucht volgens Schema ($status_new) <br />";
           echo "Status van de vlucht was: $status_old <br /><br />";  
       break;

      case "LND":
         echo "Vlucht is geland <br /><br />";
       break;

      case "FIR":
        echo "Vlucht is in nederlands luchtruim <br /> <br />";
       break;

      case "AIR":
         echo "Vlucht is nog onderweg <br /><br />";
       break;

       case "CNX":
         echo "Vlucht is gecancelled <br /><br />";     
        break;
  
      default: 
         echo "Hier mag wat staan als default<br /> <br />";
}
 
    }
else{

    echo "Einde van Vluchtinformatie <br />";
    echo "<br />";

}

}

//	   echo "Status van de vlucht is: $status_new <br />";
//	   echo "Status van de vlucht was: $status_old <br />";


       echo "<br />";
 echo "<br />";

?>
