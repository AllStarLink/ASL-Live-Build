<?php
include("session.inc");

// Author: Paul Aidukas KN2R (Copyright) July 15, 2013
// For ham radio use only, NOT for comercial use!
// Be sure to allow popups from your Allmon web server to your browser!!

if ($_SESSION['sm61loggedin'] === true) {
   $out = array();

   print "<b> Rebooting Server! ";
   print "</b>";

   $statcmd = "sudo /bin/killall watchdog; sync; sync; sync; sudo /bin/reboot";
   exec($statcmd);

} else {
   print "<br><h3>ERROR: You Must login to use the 'Server REBOOT' function!</h3>";
}

?>
