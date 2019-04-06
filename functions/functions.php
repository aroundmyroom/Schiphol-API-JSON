<?php

$page = $_GET['p'];


// aanpassingen op de functie, 

function doCurl($url, $page = null) {

$ch = curl_init();

// Eigenlijk moet de config.php de variabelen doorgeven, todo !
// als dit weg is zou de info van de config.php hier correct ingeladen moeten worden nog niet getest ivm eerdere issues

$app_key='vul hier je app_key in';
$app_id='vull hier je app_id in';


$ch = curl_init();
$ch = curl_init($url);

// debug informatie hieronder om te zien of data correct wordt aangeroepen en verwerkt

//$ch = curl_init('https://api.schiphol.nl/public-flights/flights');
//$ch = curl_init('https://api.schiphol.nl/public-flights/flights?flightdirection=A&includedelays=false&page=&sort=%2Bscheduletime=&fromScheduleDate=2019-04-04&toScheduleDate=2019-04-04');

// de opbouw om JSON op te vragenv van de Schiphol API, belangrijk hier is dat de API sleutels in de headers worden meegestuurd en niet meer in de URL

curl_setopt_array($ch, array(
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_URL, $url.($page === null ? '' : "&p=$page"),
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'resourceversion: v4',
        'app_id: ' . $app_id,
        'app_key: ' . $app_key,
        'Accept: application/json'
		)
	)
);
        /////// Get headers for pagination with Link: ///////////
//         curl_setopt($ch, CURLOPT_HTTPHEADER);
        $returnheader = array();
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
        function($ch, $header) use (&$returnheader) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) <2)
                        return $len;
                $returnheader[strtolower(trim($header[0]))] = trim($header[1]);
                return $len;
        });
        $result = curl_exec($ch);
//        echo curl_error ( $ch );
// voor debug purposes
// onderstaande regel de // weghalen dan wordt de URL die gebruikt worden naar de API getoond, zo kun je zien of eea goed gaat
//        echo $url;
        curl_close($ch);
        if (substr($result, 0, 3) == "\xEF\xBB\xBF") {
                $result = substr($result, 3);
        }
        return array($result, $returnheader);
}


function ContainsNumbers($String){
    return preg_match('/\\d/', $String) > 0;
}


?>
