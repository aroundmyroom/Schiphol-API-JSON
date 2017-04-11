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

session_start();
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

	echo "Ingeroosterd<br />";
	}

	 else {

	echo "<span style=\"color: red;\">Wijziging:  $etadateswitch</span><br />"; /* +$datediff2arrival dag</span><br />"; */
	}


	$plandeparttime = substr($flight['scheduleTime'], strpos($flight['scheduleTime'], "T") +0,5);
 	$gateopentime = substr($flight['expectedTimeGateOpen'], strpos($flight['expectedTimeGateOpen'], "T") +1,5);
	$gateclosetime = substr($flight['expectedTimeGateClosing'], strpos($flight['expectedTimeGateClosing'], "T") +1,5);
	$expboardingtime = substr($flight['expectedTimeBoarding'], strpos($flight['expectedTimeBoarding'], "T") +1,5);

//	echo "<br />Gepland Vertrek ??: {$flight['scheduleTime']}";
	echo "Vertrek: $plandeparttime"; 
echo "<br />";
//	echo "Gateopen: ??   {$flight['expectedTimeGateOpen']}";
	echo "Gate open: $gateopentime";
 echo "<br />";
	echo "Gate dicht: $gateclosetime";
 echo "<br />";
	echo "Boarding: $expboardingtime";
 echo "<br />";
//	echo "Boarding: ?? {$flight['expectedTimeBoarding']}";
 echo "<br />";

//     	echo "Gateclose: ?? {$flight['expectedTimeGateClosing']}";


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
// Informatie niet tonen als waarden leeg zijn (nog doen).
 
    	        $offblocktime= substr($flight['publicEstimatedOffBlockTime'], strpos($flight['publicEstimatedOffBlockTime'], "T") +1,5);
		$actualblocktime = substr($flight['actualOffBlockTime'], strpos($flight['actualOffBlockTime'], "T") +1,5); 
                echo "<td>";
 		echo "Nieuwe vertrektijd: $offblocktime <br />"; 
// 		echo "<td>$etadateswitch - $eta <br />";
//      	echo "Nieuwe vertrektijd: {$flight['publicEstimatedOffBlockTime']}";
//		echo "<br />";
		echo "geen tijd als vlucht nog niet vertrokken is";
		echo "Vlucht vertrokken: $actualblocktime";
//		 echo "Vlucht vertrokken om: {$flight['actualOffBlockTime']}";
		 echo "<br />";

//	if ($datediff2arrival>0):
//		echo "<span style=\"color: red;\">Grote vertraging:</span><br />"; /* $vertraging) <br />"; */
	
//	elseif ($eta1 > $eta2):

//		echo "<span style=\"color: red;\">Vertraging: $vertraging</span><br />";
	
//	 elseif ($eta1 == $eta2):
//		echo "<span style=\"color: blue;\">Op tijd</span> <br />";

//	  else:
//		 echo "<span style=\"color: green;\">Landt eerder: $vertraging</span> <br />";
	
//	endif;


//	$actuallandingtime = substr($flight['actualLandingTime'], strpos($flight['actualLandingTime'], "T") +1,8);
//	if (empty($actuallandingtime)) {
//     		echo "Geen landingstijd bekend<br /></td>";
//	}
//
//	else {
//		echo "<span style=\"color: green;\">Geland om: $actuallandingtime</span> <br /></td>";
//	}
echo "</td>";
	echo "<td>Gate: {$flight['gate']} <br />";

	$terminal = "Vertrekhal: onbekend";
                if (isset($flight['terminal']))
                        $terminal = "Vertrekhal: ".$flight['terminal'];
                echo "$terminal<br />";



//	$status_old = isset($flight['publicFlightState']['flightStates'][1]) ? $flight['publicFlightState']['flightStates'][1] : "onbekend";

			$statusold="";
			$statusoldwaardes =  array (
			"SCH" => "Gepland voor vertrek <br />Was: $status_old", //$status_new <br />Was: $status_old",
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
                        "GCH" => "Gate verandering",
                        "GCL" => "Gate gaat sluiten",
                        "GTD" => "Gate gesloten",
                        "DEL" => "Vertraagd",
                        "WIL" => "Blijf wachten in de lounge",
                        "GTO" => "Gate open",
                        "DEP" => "Vertrokken"
                );

//		if (isset($flight['publicFlightState']['flightStates'][1]) 
			if (array_key_exists($flight['publicFlightState']['flightStates'][0], $statusoldwaardes))
			$status_old = $statusoldwaardes[$flight['publicFlightState']['flightStates'][1]];

                $status_new = "verwacht";
                $statuswaardes = array(
                        "SCH" => "Gepland voor vertrek <br />$status_old", //$status_new <br />Was: $status_old",
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
			"GCH" => "Gate verandering",
			"GCL" => "Gate gaat sluiten",
			"GTD" => "Gate gesloten",
			"DEL" => "Vertraagd",
			"WIL" => "Blijf wachten in de lounge",
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

	$checktimestart = substr($flight['checkinAllocations']['checkinAllocations'][0]['startTime'], strpos($flight['checkinAllocations']['checkinAllocations'][0]['startTime'], "T") +1,5);
	$checktimeend =   substr($flight['checkinAllocations']['checkinAllocations'][0]['endTime'], strpos($flight['checkinAllocations']['checkinAllocations'][0]['endTime'], "T") +1,5);

	$offblocktime= substr($flight['publicEstimatedOffBlockTime'], strpos($flight['publicEstimatedOffBlockTime'], "T") +1,5);

	echo "Begintijd: $checktimestart ";
	echo "<br />";
	echo "Eindtijd: $checktimeend";
//	echo "<br />";


	ob_start();
	if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
    foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
    $output = [$position['position']];
    $output2 = [$position['desks']['desks']['0']['checkinClass']['description']];
    $news = array_combine($output2, $output);
    echo "{$news['Business/Priority']} {$news['First/Business']} {$news['Priority']} {$news['Premium/Star']} {$news['Business Class']} {$news['Bus.Class/Prem Eco']} ";
	}

	$oboutput = ob_get_clean();
	if (ContainsNumbers($oboutput)) {
	echo "<br />Business / Priority: $oboutput ";
	}

	ob_start();
	if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
    foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
    $output = [$position['position']];
    $output2 = [$position['desks']['desks']['0']['checkinClass']['description']];
    $news = array_combine($output2, $output);
    echo "{$news['Check-in']} {$news['check-in']} {$news['economy']} {$news['FLYBE']} {$news['AerClub']} {$news['Economy Class']} {$news['Economy class']} {$news['Economy/Dropoff']} {$news['Checkin Assistance']} {$news['Economy']} {$news['Economy/Drop off']}";
	}

	$oboutput = ob_get_clean();
	if (ContainsNumbers($oboutput)) {
	echo "<br />";
	echo "Check-in: $oboutput";
	}


	ob_start();
	if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
    foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
    $output = [$position['position']];
    $output2 = [$position['desks']['desks']['0']['checkinClass']['description']];
    $news = array_combine($output2, $output);
    echo "{$news['Baggage drop-off']} {$news['Bag Drop Only']} {$news['Bagdrop']} {$news['SSDOP']} {$news['baggage drop off']} {$news['Web Checkin']} {$news['Baggage Drop off']} ";
	}

	$oboutput = ob_get_clean();
	if (ContainsNumbers($oboutput)) {
	echo "<br />";
	echo "Baggage drop-off: $oboutput";
	}


	ob_start();
	if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
    foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
    $output = [$position['position']];
    $output2 = [$position['desks']['desks']['0']['checkinClass']['description']];
    $news = array_combine($output2, $output);
    echo "{$news['Flight Control']} ";
	}

	$oboutput = ob_get_clean();
	if (ContainsNumbers($oboutput)){
	echo "<br />";
	echo "<br />Vlucht controle: $oboutput"; 
	}

echo "<br />";
echo "<br />";

// voor debug  informatie als we checkin informatie missen ivm  wijziging schrijfwijze
/*
	if (isset($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows']))
    foreach ($flight['checkinAllocations']['checkinAllocations'][0]['rows']['rows'] as $desk => $position) {
    echo "  {$position['position']} - {$position['desks']['desks']['0']['checkinClass']['description']}<br /> ";

        }
*/
	echo "<td>";


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


?>
