<?php

$page = $_GET['p'];
function doCurl($url, $version = 'v1', $page = null) {


$ch = curl_init();

	///// Onderste 3 regels hier zijn om de JSON op te vragen ////
	$headers = array();
	$headers[] = "Accept: application/json";
	$headers[] = "Resourceversion: $version";

	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_URL, $url.($page === null ? '' : "&p=$page"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, -1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	/////// Get headers for pagination with Link: ///////////
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$returnheader = array();
	curl_setopt($ch, CURLOPT_HEADERFUNCTION, 
	function($curl, $header) use (&$returnheader) {
		$len = strlen($header);
		$header = explode(':', $header, 2);
		if (count($header) <2)
			return $len;
		$returnheader[strtolower(trim($header[0]))] = trim($header[1]);
		return $len;
	});
	$result = curl_exec($ch);
	//echo curl_error ( $ch );
	curl_close($ch);
	if (substr($result, 0, 3) == "\xEF\xBB\xBF") {
		$result = substr($result, 3);
	}
	return array($result, $returnheader);
}

?>
