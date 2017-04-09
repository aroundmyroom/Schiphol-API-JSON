<?php
// LET OP: File moet nog hernoemd worden naar aankomst.php omdat straks met vertrek.php gewerkt kan worden. schiphol.php is dan obsolete
// 
// denk aan de aanpassing in index.php met de include !


error_reporting(E_ERROR + E_WARNING + E_STRICT);
if ($_SERVER['REMOTE_ADDR'] == '10.1.1.60') {
		ini_set("display_errors", 1);
}
date_default_timezone_set('Europe/Amsterdam');

	$scheduletime ='';
	$verwachtetijd = $_GET['scheduletime'];
	unset($scheduletime);
	$flightname = $_GET['flightnumber'];
        $dfrom = $_GET['dfrom'];
        $delayurl = $_GET['delay'];

	header('content-type: text/html; charset: utf-8');
	echo <<<EOT
	<html>
	<head>

		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta http-equiv="refresh" content="120" />
		<link rel="stylesheet" href="/schiphol/css/style.css" />
		<script type="text/javascript" src="/schiphol/js/modernizr-1.5.min.js"></script>
	</head>
	<body>

		 <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
		 <script src="/schiphol/js/jquery.ngclock.0.1.js"></script>
		 <script src="/schiphol/js/upkey.js"></script>


	</script>

	 <header>
<!--                 <h1><a href="../" style="text-decoration: none">Vertrekken</a></h1> -->

   <h1><img src="../css/airplane_departure.png" width='75' alt='Vertrekken'><a href="./" style="text-decoration: none">Vertrekken</a>  <img src="../css/departures.png" width='75'></h1> 

		
		 </header>

	<div id="date_time">
        	<p id="time"></p>
                <p id="date"></p>
	</div>

		 <script src="/schiphol/js/script.js?v=1"></script>

		<script type="text/javascript">
		if (screen.width<=950)
		$("h1").replaceWith('<h1>Departures</h1>');
	</script>

	<pre>
EOT;

	 $url = "$base_url/public-flights/flights?app_id=$app_id&app_key=$app_key&flightdirection=D&includedelays=$delayurl&page=$page&sort=%2Bscheduletime&fromdate=$dfrom&todate=$dfrom";
		if (!empty($flightname))
	$url = "$base_url/public-flights/flights?app_id=$app_id&app_key=$app_key&flightname=$flightname&flightdirection=D&includedelays=true&fromdate=$dfrom&sort=%2Bscheduletime";
		 if (!empty($verwachtetijd))
        $url = "$base_url/public-flights/flights?app_id=$app_id&app_key=$app_key&scheduletime=$verwachtetijd&flightdirection=D&includedelays=true&page=$page&sort=%2Bscheduletime&fromdate=$dfrom";
/*
eerst kijken of met één van beide url's de  waarden opgevraagd kan worden. Wellicht dat een van beide URL's gewoon weg kan (lege waarden worden toegestaan bij default door de API
Vluchten voor specifieke dag
https://api.schiphol.nl/public-flights/flights?app_id=$app_id&app_key=$app_key&&flightdirection=A&includedelays=false&page=0&sort=%2Bscheduletime&fromdate=2017-04-8&todate=2017-04-08

Vluchten voor vandaag met delays
https://api.schiphol.nl/public-flights/flights?app_id=$app_id&app_key=$app_key&&flightdirection=A&includedelays=true&page=0&sort=%2Bscheduletime&fromdate=2017-04-4&todate=2017-04-04
*/

		list($result, $headers) = doCurl($url, 'v3');
		preg_match_all('~<(.*)>; rel="(.*)"~Us', $headers['link'], $matches, PREG_SET_ORDER);
		$json = json_decode($result, true);

		if (count($json['flights']))
		        foreach ($json['flights'] as $flight)
	        {

	$eta  = substr($flight['estimatedLandingTime'], strpos($flight['estimatedLandingTime'], "T") +1,8);
	$etadate = substr($flight['estimatedLandingTime'],0,10);
	$etadateswitch = date('d-m-Y', strtotime($etadate));

	$eta1 = strtotime($eta);
	$eta2 = strtotime($flight['scheduleTime']);
	$eta3 = abs($eta1-$eta2);
	$vertraging = gmdate("H:i:s", $eta3);
	$vluchtdatum = date('d-m-Y', strtotime($flight['scheduleDate']));

	$testdatum0 = ($flight['scheduleDate']);

	$testdatum4 = $etadate ;


	$datediffarrival = abs(strtotime($testdatum4) - strtotime($testdatum0));

        // delen door 0 geeft foutmelding
	if ($datediffarrival <> 0) {
	$datediff2arrival = (24/($datediffarrival/3600));
	}
	else 
	$datediff2arrival = 0;


	echo "<table id=\"departures\">";
	echo "		<thead>";
	echo "		<tr>";
	echo "		<th>Planning</th>";
	echo "		<th>Vluchtinfo</th>";
	echo "		<th>Vertrektijd</th>";
	echo "		<th>Vertrek</th>";
	echo "		<th>Check-in<br>balies</th>";
	echo "		<th>Bestemming</th>";
	echo "		</tr>";
	echo "		</thead>";

	echo "<tbody>";
	echo "<td>$vluchtdatum<br />";

	 if ($datediff2arrival<1) {

	echo "Volgens planning<br />";
	}

	 else {

	echo "<span style=\"color: red;\">Wijziging:  $etadateswitch</span><br />"; /* +$datediff2arrival dag</span><br />"; */
	}

	echo "Gepland Vertrek ??: {$flight['scheduleTime']}";
 echo "<br />";
	echo "Gateopen: ??   {$flight['expectedTimeGateOpen']}";
 echo "<br />";

	echo "Boarding: ?? {$flight['expectedTimeBoarding']}";
 echo "<br />";

     	echo "Gateclose: ?? {$flight['expectedTimeGateClosing']}";


	echo "<br />";


	$airline = $flight['prefixICAO'];

	list($results, $headers) = doCurl("$base_url/public-flights/airlines/$airline?app_id=$app_id&app_key=$app_key&page=0");


        $json2 = json_decode($results, true);

	$airlineName = ($json2{'publicName'});


		 echo "$airlineName";

	$iatamain= $flight['aircraftType']['iatamain'];
	$iatasub = $flight['aircraftType']['iatasub'];

		echo "<br />";

	 list($results, $headers) = doCurl("$base_url/public-flights/aircrafttypes?app_id=$app_id&app_key=$app_key&iatamain=$iatamain&iatasub=$iatasub&page=0");
                $json4 = json_decode($results, true);

	$vliegtuigen = ($json4['aircraftTypes']);
	$vliegtuig = $vliegtuigen[0]['longDescription'];
	$vliegtuigtype = $vliegtuigen[0]['shortDescription'];

        echo "<a href='https://www.google.nl/search?q=$vliegtuig&source=lnms&tbm=isch' target='_blank' title='toon vliegtuigfotos in nieuwe pagina'>$vliegtuig</a> <br />"; 
//	echo "$vliegtuig <br /></td>";
		

//	echo "<td><span style=\"color: blue;\">Code: {$flight['flightName']}</span>";
	$vluchtnummer = $flight['flightName'];
	echo "<td><a href='?p=".($page)."&scheduletime=&flightnumber=$vluchtnummer' title='Klik op de vluchtcode om deze vlucht in de gaten te houden'>Code: {$flight['flightName']}</a>";
        echo "<br />";

        echo "Main: {$flight['mainFlight']}";

	if (isset($flight['codeshares']['codeshares']))
        	foreach ($flight['codeshares']['codeshares'] as $joinedwith)
		{
                       	echo "<span style=\"color: grey;\"><br />Shared: {$joinedwith}</span>";
                }

                $typevlucht = $flight['serviceType'];
			echo "<br />";
                if (empty($typevlucht)) {
                        echo "Type is onbekend <br />";
                }
                $vluchttypes = array(
                        "J" => "Lijnvlucht",
                        "F" => "<span style=\"color: LightCoral;\">Cargo</span>",
                        "C" => "Charter",
                        "H" => "Cargo Charter",
                        "P" => "Herpositionering / Ferry Vlucht"
                );
                $typevlucht = 'Type is onbekend';
                if (isset($flight['serviceType']))
                        if (array_key_exists($flight['serviceType'], $vluchttypes))
                                $typevlucht =  $vluchttypes[$flight['serviceType']];
                echo "$typevlucht<br />";



                $registratiecode = $flight['aircraftRegistration'];
                echo "Registratie: <a href='https://hiveminer.com/Tags/$registratiecode' target='_blank'>$registratiecode</a><br />";
//              echo "Registratie: {$flight['aircraftRegistration']}<br />";
		echo "Code: {$flight['airlineCode']}<br /></td>";

//Vertrektijd

		 echo "<td>$etadateswitch - $eta <br />";
		 echo "Nieuwe vertrektijd: {$flight['publicEstimatedOffBlockTime']}";
		 echo "<br />";
		 echo "Vlucht vertrokken om: {$flight['actualOffBlockTime']}";
		 echo "<br />";

	if ($datediff2arrival>0):
		echo "<span style=\"color: red;\">Grote vertraging:</span><br />"; /* $vertraging) <br />"; */
	
	elseif ($eta1 > $eta2):

		echo "<span style=\"color: red;\">Vertraging: $vertraging</span><br />";
	
	 elseif ($eta1 == $eta2):
		echo "<span style=\"color: blue;\">Op tijd</span> <br />";

	  else:
		 echo "<span style=\"color: green;\">Landt eerder: $vertraging</span> <br />";
	
	endif;


	$actuallandingtime = substr($flight['actualLandingTime'], strpos($flight['actualLandingTime'], "T") +1,8);
	if (empty($actuallandingtime)) {
     		echo "Geen landingstijd bekend<br /></td>";
	}

	else {
		echo "<span style=\"color: green;\">Geland om: $actuallandingtime</span> <br /></td>";
	}

	echo "<td>Gate: {$flight['gate']} <br />";

	$terminal = "Vertrekhal: onbekend";
                if (isset($flight['terminal']))
                        $terminal = "Vertrekhal: ".$flight['terminal'];
                echo "$terminal<br />";



	$status_old = isset($flight['publicFlightState']['flightStates'][1]) ? $flight['publicFlightState']['flightStates'][1] : "onbekend";

                $status_new = "verwacht";
                $statuswaardes = array(
                        "SCH" => "Gaat vertrekken $status_new <br />Was: $status_old",
                        "LND" => "Vlucht is geland",
                        "FIR" => "Boven Nederland",
                        "AIR" => "Buiten Nederland ",
                        "CNX" => "<span style=\"color: red;\" />Gecancelled</span>",
                        "FIB" => "<span style=\"color: orange;\" />Bagage verwacht</span>",
                        "ARR" => "Afgehandeld",
                        "TOM" => "Komt op andere datum",
                        "DIV" => "Wijkt uit",
			"EXP" => "Gaat landen",
			"BRD" => "Boarding",
			"GCH" => "Gate Change",
			"GCL" => "Gate Closing",
			"GTD" => "Gate Closed",
			"DEL" => "Delayed",
			"WIL" => "Wait in lounge",
			"GTO" => "Gate open",
			"DEP" => "Vertrokken"
                );
                if (isset($flight['publicFlightState']['flightStates'][0])) {
                        if (array_key_exists($flight['publicFlightState']['flightStates'][0], $statuswaardes))
                                $status_new = $statuswaardes[$flight['publicFlightState']['flightStates'][0]];
                        echo "$status_new<br />";
                } else
                        echo "Einde van Vluchtinformatie <br /><br /></td>\n";



	 echo "          <td>";



//	$len = count($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']);
//	if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
//	foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
//    	if ($desk == 0) {
//	echo "Balie rij: {$position['position']}";
//	echo "desks:  {$position['position']}, {$position['desks']['desks']['0']['checkinClass']['description']}<br /> ";
        // first
//	    } else if ($desk == $len - 1) {
        // last
//	echo " - {$position['position']}";
//    }
//}

  if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
        foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
        echo "  {$position['position']} - {$position['desks']['desks']['0']['checkinClass']['description']}<br /> ";

        }




//		foreach($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk) {
//	if ($desk === reset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
//		echo "Balie rij: {$desk['position']}";
//
//        if ($desk === end ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
//        echo " - {$desk['position']}<br />";

//}

//		 foreach($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'][0]['desks']['desks'] as $checkinclass) {
//	if ($checkinclass === reset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'][0]['desks']['desks']))
//                 echo "Check-in Class: {$checkinclass['checkinClass']['code']}<br />";

//         if ($checkinclass === end($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'][0]['desks']['desks']))
//	 	echo " {$checkinclass['checkinClass']['code']}<br />";
//}


echo "<td>";
// print_r($flight['checkinAllocations']['checkinAllocations']);


/*
                if (isset($flight['checkinAllocations']['checkinAllocations']))
                        foreach ($flight['checkinAllocations']['checkinAllocations'] as $checkin)
                                        echo "Check-in: {$checkin} <br />";
                $checkintimebegin  = substr($flight['checkinAllocations']['checkinAllocations'], strpos($flight['checkinAllocations']['checkinAllocations'], "T") +1,8);
                if (empty($checkintime))
                        echo "Tijd onbekend<br /></td>\n";
                else
                        echo "Om: $bagagetijd <br /></td>\n";

			echo "<td>";
*/


	foreach ($flight['route']['destinations'] as $departure)
	{

	list($results, $headers) = doCurl("$base_url/public-flights/destinations/$departure?app_id=$app_id&app_key=$app_key");
        $json2 = json_decode($results, true);

	$city = ($json2{'city'});
	$country = ($json2{'country'});
	$nlcity = ($json2['publicName']{'dutch'});
       
        echo "$nlcity<br />$departure<br />";
        echo "$country<br />";

	    }
	echo "</td>";


	}

	echo "                        </tbody>";
	echo "                </table>";


	if (!empty($flightname)) {


	echo "<div id=\"data_invoer\">";
	echo "<p id=\"invoer1\"></p>";
	echo "<br /><br />";

		echo "<h1><a href='?p=".($page)."&scheduletime=$verwachtetijd&flightnumber=$flightname'>Ververs Pagina</a></h1>";
	echo "<br />";
        echo "</div>";

	}
        
	else {

	echo "<a href='?p=".($page-1)."&scheduletime=$verwachtetijd&flightnumber=$flightname&dfrom=$dfrom&delay=$delayurl'>Vorige Pagina</a>";
        echo "   |   ";
        echo "<a href='?p=".($page+1)."&scheduletime=$verwachtetijd&flightnumber=$flightname&dfrom=$dfrom&delay=$delayurl'>Volgende Pagina</a>";
	echo "<br />";
        }



// debug information [turn on when needed]



/*
echo "<h1>Controle <br>";
echo "<br />";
echo "tijd: $verwachtetijd";
echo "<br />";
echo "of";
echo " <br />";
echo "Vluchtnaam: $flightname</h1>";
echo "<br  />";
echo "$url";

*/

//$eta2a = ($flight['scheduleTime']);
//echo "$eta2a";
//echo "<br />";
//echo "eta: $eta";
echo "<br />";
echo "testdatum 0 $testdatum0";
echo "testdatum 4 $testdatum4";

// indien kleiner dan 0

echo "debuggen eerder aankomen over de dag terug en over de dag heen (laatste is vetraging)";
echo "<br />";
$negatief = strtotime($testdatum4) - strtotime($testdatum0);
echo "uitrekenen van negatief $negatief";
echo "<br />";
if ($negatief <0) {
	echo "De vlucht komt eerder aan";
	}
	else
	echo "de vlucht komt later of is op tijd";

echo "<br />";
echo "<br />";

$drop1 = ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']);

echo "<pre>";
//print_r ($drop1);
echo "dit was de drop1 <br />";
echo "</pre>";

$key1 = $drop1['0']['desks']['desks']['0']['checkinClass']['description'];
$key2 = $drop1['0']['desks']['desks']['0']['position'];
$key3 = $drop1['position'];

echo "<br /> een test<br />";

	//$len = count($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']); // drop1
        if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
 	foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
	echo "desks:  {$position['position']}, {$position['desks']['desks']['0']['checkinClass']['description']}<br /> ";

	}
	// else if ($desk == $len - 1) {
        // last
	//echo " waar komt dit?";
        //echo " - {$position['position']}"; 
//    }

echo " <br />Dit niet meenmenen als array <br />";
//var_dump($newarray);


//echo "key1: $key1";
//echo "<br />key2: $key2";
//echo "<br />key3: $key3";



    $len = count($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']);
        if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
        foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
        if ($desk == 0) {
        echo "Balie rij: {$position['position']}";
        // first
            } else if ($desk == $len - 1) {
        // last
        echo " - {$position['position']}";
    }
}

echo "wat gaat hieronder gebeuren !!!!!!!!!!!!!!!!!! <br />";

 echo "<br />Checkin: ";
 if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
        foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
	$output = [$position['position']];
	$output2 = [$position['desks']['desks']['0']['checkinClass']['description']];

	$news = array_combine($output2, $output);
        echo "{$news['Check-in']} {$news['check-in']} {$news['economy']} {$news['FLYBE']} {$news['AerClub']}";
}

echo "<br />Baggage drop-off:";

if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
        foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
        $output = [$position['position']];
        $output2 = [$position['desks']['desks']['0']['checkinClass']['description']];

        $news = array_combine($output2, $output);
        echo "{$news['Baggage drop-off']} {$news['Bag Drop Only']} ";
}

echo "<br />Self-Service: ";
if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
        foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
        $output = [$position['position']];
        $output2 = [$position['desks']['desks']['0']['checkinClass']['description']];

        $news = array_combine($output2, $output);
        echo "{$news['SSDOP']} ";
}

echo "<br />Bag Drop:";
if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
        foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
        $output = [$position['position']];
        $output2 = [$position['desks']['desks']['0']['checkinClass']['description']];

        $news = array_combine($output2, $output);
        echo "{$news['Bagdrop']} ";
}

echo "<br />Incheck Hulp:";
if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
        foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
        $output = [$position['position']];
        $output2 = [$position['desks']['desks']['0']['checkinClass']['description']];

        $news = array_combine($output2, $output);
        echo "{$news['Checkin Assistance']} ";
}

echo "<br />Vlucht controle:";
if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
        foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
        $output = [$position['position']];
        $output2 = [$position['desks']['desks']['0']['checkinClass']['description']];

        $news = array_combine($output2, $output);
        echo "{$news['Flight Control']} ";
}


echo "<br />Business / Priority: ";
if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
        foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
        $output = [$position['position']];
        $output2 = [$position['desks']['desks']['0']['checkinClass']['description']];

        $news = array_combine($output2, $output);
        echo "{$news['Business/Priority']} {$news['First/Business']} {$news['Priority']}";
}


?>
