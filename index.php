<?php
error_reporting(E_ERROR + E_WARNING + E_STRICT);
ini_set("display_errors", 1);
date_default_timezone_set('Europe/Amsterdam');
require_once("./config.php");
require_once("./functions/functions.php");


if (count($_POST)) {
                $url = "?p=0&scheduletime=".$_POST['scheduletime']."&flightnumber=".$_POST['flightnumber']."&dfrom=".$_POST['dfrom']."&delay=".$_POST['$delay'];

                header("Location: $url");
                exit;
}
if ($_GET['scheduletime'] || $_GET['flightnumber'] || $_GET['dfrom']) {
                include_once("./aankomst/aankomst.php");
                exit;
}

?>
<!doctype html>

<html>
<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8">

<link rel="apple-touch-icon" sizes="180x180" href="css/ico/apple-touch-icon.png">
<link rel="icon" type="image/png" href="css/ico/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="css/ico/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="css/ico/manifest.json">
<link rel="mask-icon" href="css/ico/safari-pinned-tab.svg" color="#5bbad5">
<meta name="theme-color" content="#ffffff">


  <link rel="stylesheet" href="/schiphol/css/style.css" />
  <script type="text/javascript" src="/schiphol/js/modernizr-1.5.min.js"></script>
</head>
<body>
 <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
 <script src="/schiphol/js/jquery.ngclock.0.1.js"></script>
 <script src="/schiphol/js/upkey.js"></script>
<div id="container">
    <header>
                <h1>Schiphol Info</h1>
    </header>

            <div id="date_time">
                        <p id="time"></p>
                        <p id="date"></p>
                </div>

                <table id="departures">
                        <thead>
                                <tr>
<th></th>                                </tr>
                        </thead>
                        <tbody>
                        </tbody>
                </table>
    </div>

    <footer>

    </footer>
  </div> <!-- end of #container -->

 <script src="/schiphol/js/script.js?v=1"></script>

<script type="text/javascript">
if (screen.width<=950)
$("h1").replaceWith('<h1>Schiphol info</h1>');

</script>


<br />

<div id="data_invoer">
<p id="invoer1"></p>

<h1><a href="./aankomst/">Aankomsten</a>
<br />
<br />
<br />
<a href="./vertrek/">Vertrekkende vliegtuigen</a><h1>


</body>
