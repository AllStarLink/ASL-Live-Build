<?php
include("session.inc");

// Author: Paul Aidukas KN2R (Copyright) July 15, 2013
// For ham radio use only, NOT for comercial use!
// Be sure to allow popups from your Allmon/SuperMon web server to your browser!!

if ($_SESSION['sm61loggedin'] === true) {

   $button = @trim(strip_tags($_POST['button']));
   $out = array();

   if ($button == 'astaron') {

      print "<b>Starting up AllStar... </b> ";

      exec('sudo /usr/local/sbin/astup.sh', $out);
      print_r ($out);

   } elseif ($button == 'astaroff') {

      print "<b>Shutting down AllStar... </b> ";

      exec('sudo /usr/local/sbin/astdn.sh', $out);
      print_r ($out);
   }

} else {
   print "<br><h3>ERROR: You Must login to use the 'AST START' or 'AST STOP' functions!</h3>";
}

?>
