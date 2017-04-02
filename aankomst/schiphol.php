<?php

$scheduletime ='';
$verwachtetijd = $_POST['scheduletime'];
unset($scheduletime);
$flightname = $_POST['flightnumber'];



$datefrom=
$dateto=

// initial test getting simple arrival data from API SCHIPHOL
// author Dennis Slagers has no coding skills, so any hickup is due to this
// Credits to [DAOS] from tweakers.net fixing the json reading  it was all about [] and {}
// 
// 26-3-2017: a lot of date and time stuff modified and changed.
// 
// 01-04-2017: 
// start of implementation CSS derived from https://github.com/nckg/flightboard
// This code is part of: https://github.com/aroundmyroom/Schiphol-API-JSON
// most part of CSS finished
//
//
// Following issue: how to use pagination when Link: is found in header when JSON is retrieved

require_once("../config.php");
// not needed anymore, for debug purposes only: require_once("../nicejson.php");

header('content-type: text/html; charset: utf-8');
echo <<<EOT
<html>
<head>

  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" href="/schiphol/css/style.css" />
  <script type="text/javascript" src="/schiphol/js/modernizr-1.5.min.js"></script>
</head>
<body>
 <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
 <script src="/schiphol/js/jquery.ngclock.0.1.js"></script>
 <script src="/schiphol/js/upkey.js"></script>

 <header>
                <h1><a href="../index.php" style="text-decoration: none">Aankomsten</a></h1>
    </header>

<div id="date_time">
                        <p id="time">
</p>

                        <p id="date"></p>
                </div>



 <script src="/schiphol/js/script.js?v=1"></script>

<script type="text/javascript">
if (screen.width<=950)
$("h1").replaceWith('<h1>Arrivals</h1>');

</script>

  <pre>
EOT;


$ch = curl_init();
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

if (!empty($verwachtetijd)) {

curl_setopt($ch, CURLOPT_URL, "$base_url/public-flights/flights?app_id=$app_id&app_key=$app_key&scheduletime=$verwachtetijd&flightdirection=A&includedelays=true&page=0&sort=%2Bscheduletime");
 }

else {
curl_setopt($ch, CURLOPT_URL, "$base_url/public-flights/flights?app_id=$app_id&app_key=$app_key&flightname=$flightname&flightdirection=A&includedelays=true&page=0&sort=%2Bscheduletime");

 }

curl_setopt($ch, CURLOPT_RETURNTRANSFER, -1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

///// Onderste 3 regels hier zijn om de JSON op te vragen ////
$headers = array();
$headers[] = "Accept: application/json";
$headers[] = "Resourceversion: v3";

/////// Get headers for pagination with Link: ///////////

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

$result = curl_exec($ch);

curl_close($ch);

if (substr($result, 0, 3) == "\xEF\xBB\xBF") {
  $result = substr($result, 3);
}

$url = ($headers1{'link'});
preg_match_all('~<(.*)>; rel="(.*)"~Us', $url, $matches, PREG_SET_ORDER);

$json = json_decode($result, true);


   if (count($json['flights'])) 

foreach ($json['flights'] as $flight)
{

// datum wordt nu weggefilterd maar is hier en daar toch nodig (over de dag heen)
// bij verwachte landingstijd de datum en tijd los weergeven
// onderstaande is om wat dingen om te zetten en wat uit te rekenen

$eta  = substr($flight['estimatedLandingTime'], strpos($flight['estimatedLandingTime'], "T") +1,8);
$etadate = substr($flight['estimatedLandingTime'],0,10);
$etadateswitch = date('d-m-Y', strtotime($etadate));

$eta1 = strtotime($eta);
$eta2 = strtotime($flight['scheduleTime']);
$eta3 = abs($eta1-$eta2);
$vertraging = gmdate("H:i:s", $eta3);
$vluchtdatum = date('d-m-Y', strtotime($flight['scheduleDate']));

$testdatum0 = ($flight['scheduleDate']);
$testdatum1 = strtotime($testdatum0);

$testdatum2 = strtotime($vluchtdatum);
$testdatum3 = ($etadate - $testdatum1);

$testdatum4 = $etadate ;

$omzet4 = strtotime($etadate);

$datediffarrival = abs(strtotime($testdatum4) - strtotime($testdatum0));
$datediff2arrival = (24/($datediffarrival/3600));

// echo "$scheduletime <br /><br />";
// echo "$verwachtetijd <br /><br />";
// echo "$flightname <br /><br />";


echo "<table id=\"departures\">";
echo "                        <thead>";
echo "                                <tr>";
echo "                                        <th>Planning</th>";
echo "                                        <th>Vluchtinfo</th>";
echo "                                        <th>Aankomsttijd</th>";
echo "                                        <th>Aankomst</th>";
echo "					      <th>Bagage</th>";
echo "					      <th>Vertrek</th>";
echo "                                </tr>";
echo "                        </thead>";





echo "<tbody>";
    echo "<td>$vluchtdatum<br />";

 if ($datediff2arrival<1) {

     echo "Komt vandaag<br />";
	}

 else {

    echo "Nieuwe datum:  $etadateswitch +($datediff2arrival)<br />";
	}

    echo "Landing: {$flight['scheduleTime']}";
    echo "<br />";


$airline = $flight['prefixICAO'];

$ch3 = curl_init();

curl_setopt($ch3, CURLOPT_URL, "$base_url/public-flights/airlines/$airline?app_id=$app_id&app_key=$app_key&page=0");

curl_setopt($ch3, CURLOPT_RETURNTRANSFER, -1);
curl_setopt($ch3, CURLOPT_CUSTOMREQUEST, "GET");

$headers = array();
$headers[] = "Accept: application/json";
$headers[] = "Resourceversion: v1";
curl_setopt($ch3, CURLOPT_HTTPHEADER, $headers);

$results3 = curl_exec($ch3);
curl_close($ch3);

if (substr($results3, 0, 3) == "\xEF\xBB\xBF") {
  $results = substr($results3, 3);
}

$json2 = json_decode($results3, true);

$airlineName = ($json2{'publicName'});


 echo "$airlineName";

$iatamain= $flight['aircraftType']['iatamain'];
$iatasub = $flight['aircraftType']['iatasub'];

echo "<br />";

$ch4 = curl_init();

curl_setopt($ch4, CURLOPT_URL, "$base_url/public-flights/aircrafttypes?app_id=$app_id&app_key=$app_key&iatamain=$iatamain&iatasub=$iatasub&page=0");


curl_setopt($ch4, CURLOPT_RETURNTRANSFER, -1);
curl_setopt($ch4, CURLOPT_CUSTOMREQUEST, "GET");

$headers = array();
$headers[] = "Accept: application/json";
$headers[] = "Resourceversion: v1";
curl_setopt($ch4, CURLOPT_HTTPHEADER, $headers);

$results4 = curl_exec($ch4);
curl_close($ch4);

if (substr($results4, 0, 3) == "\xEF\xBB\xBF") {
  $results = substr($results4, 3);
}

$json4 = json_decode($results4, true);


$vliegtuigen = ($json4['aircraftTypes']);
$vliegtuig = $vliegtuigen[0]['longDescription'];
$vliegtuigtype = $vliegtuigen[0]['shortDescription'];

echo "$vliegtuig <br /></td>";
//echo " $vliegtuigtype <br /></td>";


//    echo "Vluchtdatum: date('d-m-Y', strtotime({$flight['scheduleDate']}) <br />";

    echo "<td>Code 1: {$flight['flightName']} <br />";

   foreach ($flight['codeshares']['codeshares'] as $joinedwith)
    {
     echo "Code 2: {$joinedwith}<br /> ";
    }

$typevlucht = $flight['serviceType'];


if (empty($typevlucht)) {
    echo "Type is onbekend <br />";
	}

 if(!empty($typevlucht)){
     switch($typevlucht) {

      case "": 
        Echo "NA";
       break;

      case "J":
         echo "Lijnvlucht<br />";
       break;

      case "F":
         echo "Cargo <br />";
       break;
  
      case "C":
         echo "Charter <br />";
       break;

      case "H":
         echo "Cargo Charter <br />";
         break;

      case "P":
        echo "Herpositionering / Ferry Vlucht <br />";
        break;

//      default: 
//         echo "Verschillende type vluchten<br /> <br />";

		}
 
    }

// deze snap ik nog even niet

else{

    echo "Geen informatie";
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

// echo "Type main: {$flight['aircraftType']['iatamain']}<br />";
//echo "Type sub: {$flight['aircraftType']['iatasub']}<br />";
echo "Registratie: {$flight['aircraftRegistration']}<br />";
echo "Code: {$flight['airlineCode']}<br /></td>";
	

// hoort hier niet:	echo "Geroosterde landingstijd:$etadateswitch {$flight['scheduleTime']} <br />";

        echo "<td>$etadateswitch - $eta <br />";


if ($datediff2arrival>0):

echo "Andere datum, ($vertraging) <br />";

elseif ($eta1 > $eta2):

  echo "Vertraging: $vertraging<br />";

 elseif ($eta1 == $eta2):
    echo "Op tijd <br />";

  else:
   echo "Landt eerder ($vertraging) <br />";

endif;


$actuallandingtime = substr($flight['actualLandingTime'], strpos($flight['actualLandingTime'], "T") +1,8);

  if (empty($actuallandingtime)) {

     echo "Landingstijd onbekend <br /></td>";
}

 else {

    echo "Geland om: $actuallandingtime <br /></td>";
}


    echo "<td>Gate: {$flight['gate']} <br />";

$terminal = $flight['terminal'];


if(!empty($terminal)){
     switch($terminal) {
      case "":
        Echo "Hal anders <br />";
       break;

      case "1":
         echo "Hal: 1 <br />";
       break;

      case "2":
         echo "Hal: 2<br />";
       break;

      case "3":
        echo "Hal: 3 <br />";
       break;

       case "4":
        echo "Hal: 4 <br />";
       break;

		}
	}

else{

    echo "Hal is onbekend<br />";

        }

// indien er geen index 1 is dan volgt er nog een PHP error
// moet nog wat op verzonnen worden, iets met if empt of zo

$status_new = $flight['publicFlightState']['flightStates'][0];
$status_old = $flight['publicFlightState']['flightStates'][1];

if (empty($status_new)) {
    echo "N/A<br /></td>";
}

 if(!empty($status_new)){
     switch($status_new) {
       case "":
        Echo "N/A2<br /></td>";
       break;

      case "SCH":
         echo "Verwacht: ($status_new) <br />";
           echo "Was: $status_old <br /></td>";
       break;

      case "LND":
         echo "Vlucht is geland <br /></td>";
       break;

      case "FIR":
        echo "Boven Nederland<br /></td>";
       break;

      case "AIR":
         echo "Is onderweg <br /></td>";
       break;

      case "CNX":
         echo "Gecancelled <br /></td>";
        break;

      case "FIB";
         echo "Bagage verwacht<br/></td>";
         break;

      case "ARR":
         echo "Afgehandeld<br /></td>";
         break;

       case "TOM":
         echo "Komt op andere datum<br /></td>";
         break;

        case "DIV":
         echo "Wijkt uit <br /></td>";
         break;

  }

    }

else{

    echo "Einde van Vluchtinformatie <br /><br /></td>";

        }


//////////////////////////////////

    echo "<td>";
    foreach ($flight['baggageClaim']['belts'] as $belt)
    {
        echo "Bagageband: {$belt} <br />";
    }

   $bagagetijd  = substr($flight['expectedTimeOnBelt'], strpos($flight['expectedTimeOnBelt'], "T") +1,8);

   if (empty($bagagetijd)) {

     echo "Tijd onbekend<br /></td>";
}

 else {

    echo "Om: $bagagetijd <br /></td>";
}



///
// 2e aanroep API om extra informatie van vertrek luchthaven op te halen
///

echo "<td>";

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

$city = ($json2{'city'});
$country = ($json2{'country'});
$nlcity = ($json2['publicName']{'dutch'});

       
       echo "$nlcity ($departure)<br />";
       echo "$country<br />";

//       echo "Vertrokken uit: $country <br />";
//       echo "City code: $departure <br />";

    }
echo "</td>";

// indien er geen index 1 is dan volgt er nog een PHP error
// moet nog wat op verzonnen worden, iets met if empt of zo


}

echo "                        </tbody>";
echo "                </table>";

echo "<br />";
echo "<h1>Controle <br>";
echo "<br />";
echo "tijd: $verwachtetijd";
echo "<br />";
echo "of";
echo " <br />";
echo "Vluchtnaam: $flightname</h1>";


$last = ($matches[0][1]);
$next = ($matches[1][1]);
$first = ($matches[2][1]);
$previous = ($matches[3][1]);

print_r($matches);
 

Echo "Eerste pagina: $first";
echo "<br /><br />";
echo "Laatste pagina: $last";
echo "<br /><br />";
echo "volgende pagina: $next";
echo "<br /><br />";
echo "vorige pagina: $previous"; 
echo "<br />";
echo "<br/></br>";


?>
