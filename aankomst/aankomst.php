<?php


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
<!--                 <h1><a href="../" style="text-decoration: none">Aankomsten</a></h1> -->

   <h1><img src="../css/airplane_arrival.png" width='75' alt='Aankomsten'><a href="./" style="text-decoration: none">Aankomsten</a>  <img src="../css/arrivals_low.png" width='75'></h1> 

		
		 </header>

	<div id="date_time">
        	<p id="time"></p>
                <p id="date"></p>
	</div>

		 <script src="/schiphol/js/script.js?v=1"></script>

		<script type="text/javascript">
		if (screen.width<=950)
		$("h1").replaceWith('<h1>Arrivals</h1>');
	</script>

	<pre>
EOT;

	 $url = "$base_url/public-flights/flights?app_id=$app_id&app_key=$app_key&flightdirection=A&includedelays=$delayurl&page=$page&sort=%2Bscheduletime&fromdate=$dfrom&todate=$dfrom";
		if (!empty($flightname))
	$url = "$base_url/public-flights/flights?app_id=$app_id&app_key=$app_key&flightname=$flightname&flightdirection=A&includedelays=true&fromdate=$dfrom&sort=%2Bscheduletime";
		 if (!empty($verwachtetijd))
        $url = "$base_url/public-flights/flights?app_id=$app_id&app_key=$app_key&scheduletime=$verwachtetijd&flightdirection=A&includedelays=true&page=$page&sort=%2Bscheduletime&fromdate=$dfrom";

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
	echo "		<th>Aankomsttijd</th>";
	echo "		<th>Aankomst</th>";
	echo "		<th>Bagage</th>";
	echo "		<th>Vertrek</th>";
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

	echo "Landing: {$flight['scheduleTime']}";
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
		echo "<td>$etadateswitch - $eta <br />";

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

	$terminal = "Hal: onbekend";
                if (isset($flight['terminal']))
                        $terminal = "Hal: ".$flight['terminal'];
                echo "$terminal<br />";



	$status_old = isset($flight['publicFlightState']['flightStates'][1]) ? $flight['publicFlightState']['flightStates'][1] : "onbekend";

                $status_new = "verwacht";
                $statuswaardes = array(
                        "SCH" => "Wordt $status_new <br />Was: $status_old",
                        "LND" => "Vlucht is geland",
                        "FIR" => "Boven Nederland",
                        "AIR" => "Buiten Nederland ",
                        "CNX" => "<span style=\"color: red;\" />Gecancelled</span>",
                        "FIB" => "<span style=\"color: orange;\" />Bagage verwacht</span>",
                        "ARR" => "Afgehandeld",
                        "TOM" => "Komt op andere datum",
                        "DIV" => "Wijkt uit",
			"EXP" => "Gaat landen"
                );
                if (isset($flight['publicFlightState']['flightStates'][0])) {
                        if (array_key_exists($flight['publicFlightState']['flightStates'][0], $statuswaardes))
                                $status_new = $statuswaardes[$flight['publicFlightState']['flightStates'][0]];
                        echo "$status_new<br />";
                } else
                        echo "Einde van Vluchtinformatie <br /><br /></td>\n";



	 echo "          <td>";
                if (isset($flight['baggageClaim']['belts']))
                        foreach ($flight['baggageClaim']['belts'] as $belt)
                                        echo "Bagageband: {$belt} <br />";
                $bagagetijd  = substr($flight['expectedTimeOnBelt'], strpos($flight['expectedTimeOnBelt'], "T") +1,8);
                if (empty($bagagetijd))
                        echo "Tijd onbekend<br /></td>\n";
                else
                        echo "Om: $bagagetijd <br /></td>\n";

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




?>
