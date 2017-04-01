<?php

// this script is example how to get the HeaderLinks [E X A M P L E]
//
// so a lot of trying and debugging


require_once("config.php");
require_once("nicejson.php");

header('content-type: text/html; charset: utf-8');
echo <<<EOT
<html>
EOT;

$ch = curl_init();
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);


// Geef hier de juiste URL in voor de API met je eigen gegevens, deze URL kan en moet dus wijzigen wanneer je HeaderLink gaat toepassen

curl_setopt($ch, CURLOPT_URL, "https://api.schiphol.nl/public-flights/flights?app_id=%app_id&app_key=$app_key&scheduledate=[date with yyy-mm-dd]&flightdirection=A&includedelays=false&page=1&sort=%2Bscheduletime");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, -1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

$headers = array();
$headers[] = "Accept: application/json";
$headers[] = "Resourceversion: v3";

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$headers1 = [];
curl_setopt($ch, CURLOPT_HEADERFUNCTION, 
    function($curl, $header) use(&$headers1)
{
$len = strlen($header);
$header = explode(':', $header, 2);
   if (count($header) <2)
   return $len;

  $headers1[strtolower(trim($header[0]))] = trim($header[1]);
  return $len;
}
);

///////

$result = curl_exec($ch);

curl_close($ch);

if (substr($result, 0, 3) == "\xEF\xBB\xBF") {
  $result = substr($result, 3);
}

// de JSON hebben we hier niet nodig, het gaat namelijk om de header informatie die we ergens moeten gaan toepassen

// $json = json_decode($result, true);
// print_r ($json);

$url = ($headers1{'link'});

// kijken wat we uit de URL variabele krijgen

print_r($url);

// even reg-exxen (geen idee wat het hier allemaal doet, maar bedankt @Zaph (Tweakers)
// we krijgen dan een mooie array terug

preg_match_all('~<(.*)>; rel="(.*)"~Us', $url, $matches, PREG_SET_ORDER);

// en die array komt dan eruit via de print_r

print_r($matches);


?>
