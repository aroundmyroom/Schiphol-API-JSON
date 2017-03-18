<?php

// initial test getting simple arrival data from API SCHIPHOL
// author Dennis Slagers has no coding skills, so any hickup is due to this

require_once("config.php");
require_once("nicejson.php");

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
//echo htmlspecialchars(json_format($result));

// var_dump(json_decode($result, true));

header('content-type: text/html; charset: utf-8');
echo <<<EOT
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
</body>
  <h1>JSON dump Schiphol Arrival Data Hour:Minute</h1>
<!-- <pre> -->
EOT;

    $data =  json_decode($result);

    if (count($data->flights)) {
        // Open the table

        echo "<table>";

        // Cycle through the array
        foreach ($data->flights as $idx => $flights) {



            // Output a row
            echo "<tr>";
//            echo "<td>db ID: $flights->id<td>";
//            echo "<td><b><h3>Aankomst Vlucht:</h3></b>$flights->flightDirection</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><b>Aankomst Vluchtnummer:</b><br /> $flights->mainFlight</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><b>Aankomstdatum:</b><br /> $flights->scheduleDate</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><b>Aankomst:</b><br /> $flights->scheduleTime</td>";
            echo "</tr>";
            echo "<tr>";            
            echo "<td><b>Verwachte landing:</b><br /> $flights->estimatedLandingTime</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><b>Is geland op:</b><br /> $flights->actualLandingTime</td>";
            echo "<br /></tr>";
        }

        // Close the table
        echo "</table>";
    }

?>
