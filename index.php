<?php

// initial test getting simple arrival data from API SCHIPHOL
// author Dennis Slagers has no coding skills, so any hickup is due to this
// Credits to [DAOS] from tweakers.net fixing the json reading  it was all about [] and {}
// 
// 26-3-2017: a lot of date and time stuff modified and changed.
// 
// This code is part of: https://github.com/aroundmyroom/Schiphol-API-JSON


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

$json = json_decode($result, true);


   if (count($json['flights'])) 

foreach ($json['flights'] as $flight)
{

// datum wordt nu weggefilterd maar is hier en daar toch nodig (over de dag heen)
// bij verwachte landingstijd de datum en tijd los weergeven

$eta  = substr($flight['estimatedLandingTime'], strpos($flight['estimatedLandingTime'], "T") +1,8);
$eta1 = strtotime($eta);
$eta2 = strtotime($flight['scheduleTime']);
$eta3 = abs($eta1-$eta2);
$vertraging = gmdate("H:i:s", $eta3);

    echo "Vluchtdatum: {$flight['scheduleDate']} <br />";
    echo "Vluchtnaam: {$flight['flightName']} <br />";

$typevlucht = $flight['serviceType'];


if (empty($typevlucht)) {
    echo "Vluchttype is onbekend <br />";
}


 if(!empty($typevlucht)){
     switch($typevlucht) {

	   case "": 
        Echo "Geen geldige waarde gevonden";
       break;

      case "J":
         echo "Vluchttype: Passagiers<br />";
       break;

      case "F":
         echo "Vluchttype: Cargo <br />";
       break;
  
//      default: 
//         echo "Verschillende type vluchten<br /> <br />";
}
 
    }

// deze snap ik nog even niet
else{

    echo "Geen extra type vlucht informatie";
    echo "<br />";

}	

// eerste opzet om het vliegtuigtype uit te lezen
// https://api.schiphol.nl/public-flights/aircrafttypes?app_id=$app_id&app_key=$app_key&iatamain=74F&iatasub=74Y&page=0&sort=%2Biatamain

// variabele voor test en json example output om uit te lezen

// {
//  "aircraftTypes": [
//    {
//      "longDescription": "BOEING 747-400F FREIGHTER",
//      "shortDescription": "B747-400F",
//      "schemaVersion": "1",
//      "iatamain": "74F",
//      "iatasub": "74Y"
//    }
//  ],
//  "schemaVersion": "1"
// }

$iatamain="74F";
$iatasub="74Y";

echo "Vliegtuigtype main: {$flight['aircraftType']['iatamain']}<br />";
echo "Vliegtuigtype sub: {$flight['aircraftType']['iatasub']}<br />";




	
	echo "Geroosterde landingstijd: {$flight['scheduleTime']} <br />";
        echo "Verwachte landingstijd: $eta <br />";


if ($eta1 > $eta2):

  echo "Vlucht heeft nieuwe aankomsttijd. (Verschil: $vertraging)<br />";

 elseif ($eta1 == $eta2):
    echo "Perfect op tijd <br />";

  else:
   echo "Vlucht landt waarschijnlijk eerder ($vertraging) <br />";

endif;

$actuallandingtime = substr($flight['actualLandingTime'], strpos($flight['actualLandingTime'], "T") +1,8);

    echo "Vliegtuig is geland om: $actuallandingtime <br />";
    echo "Geparkeerd aan Gate: {$flight['gate']} <br />";
    echo "Aankomsthal: {$flight['terminal']} <br />";

    foreach ($flight['baggageClaim']['belts'] as $belt)
    {
        echo "Bagageband: {$belt} <br />";
    }

   $bagagetijd  = substr($flight['expectedTimeOnBelt'], strpos($flight['expectedTimeOnBelt'], "T") +1,8);

    echo "Bagage verwacht om: $bagagetijd <br />";

    foreach ($flight['codeshares']['codeshares'] as $joinedwith)
    {
     echo "Ook bekend onder Vluchtnummer: {$joinedwith} <br />";
    }

    foreach ($flight['route']['destinations'] as $departure)
    {

// 2e aanroep API om extra informatie van vertrek luchthaven op te halen

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

    }

// indien er geen index 1 is dan volgt er nog een PHP error

    $status_new = $flight['publicFlightState']['flightStates'][0];
    $status_old = $flight['publicFlightState']['flightStates'][1];

if (empty($status_new)) {
    echo "Geen vluchtstatus momenteel <br />";
}

// Er zijn verschillende statussen. Niet alles ingegeven

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
        echo "Vlucht is in Nederlands luchtruim <br /> <br />";
       break;

      case "AIR":
         echo "Vlucht is nog onderweg <br /><br />";
       break;

       case "CNX":
         echo "Vlucht is gecancelled <br /><br />";     
        break;
  
      case "FIB";
         echo "Eerste bagage is verwacht op de bagageband <br/><br />";
         echo "Bagage verwacht om: {$flight['expectedTimeOnBelt']}<br />";
//      default: 
//         echo "Hier mag wat staan als default<br /> <br />";
}
 
    }
else{

    echo "Einde van Vluchtinformatie <br />";
    echo "<br />";

	}
}

       echo "Met dank aan de vrije API van Schiphol<br />";
 echo "<br />";

?>
