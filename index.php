<?php
error_reporting(E_ERROR + E_WARNING + E_STRICT);
ini_set("display_errors", 1);
date_default_timezone_set('Europe/Amsterdam');
require_once("./config.php");
if (count($_POST)) {
    include_once("./aankomst/schiphol.php");
    exit;
}
?>
<!doctype html>

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
<div id="container">
    <header>
                <h1>Aankomsten</h1>
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
$("h1").replaceWith('<h1>Arrivals</h1>');

</script>


<br />

<div id="data_invoer">
<p id="invoer1"></p>

<h1>Geef globale Landingstijd in</h1>

<?php
// 1e form in PHP gezet omdat ik een default tijd gebaseerd op de huidige tijd wilde hebben
// waarden tussen ""  moeten een escape krijgen wat via \ gedaan wordt

$formdate = date("H:i");

echo "<br />";

echo "<form name=\"form1\" method=\"post\">";
echo "<input type=\"time\" name=\"scheduletime\" value=\"$formdate\" size=\"5\">";
echo "<input type=\"submit\" name=\"submit\" value=\"submit\">";
echo " </form>";
echo "</div>";
?>

<br /><br />
<div id="data_invoer">
<p id="invoer1"></p>

<h1>of</h1>
<h1>Geef vluchtnummer in</h1>
<br />
<form name="form1" action="/schiphol/aankomst/schiphol.php" method="post">
<input type="text" name="flightnumber" value="" size="6" onkeydown="upperCaseF(this)">
<input type="submit" name="submit" value="submit">
 </form>

    </div>



<?php
unset($scheduletime);
?>


</body>
