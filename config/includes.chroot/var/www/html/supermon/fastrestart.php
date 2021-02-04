<?php
include("session.inc");

// Author: Paul Aidukas KN2R (Copyright) July 15, 2013
// For ham radio use only, NOT for comercial use!
// Be sure to allow popups from your Allmon web server to your browser!!

if ($_SESSION['sm61loggedin'] === true) {

   if (`cat /etc/asterisk/rpt.conf |egrep -c ^"outstreamcmd"` > 0) {
       print "<b> Unsafe to RESTART while streaming audio! Use REBOOT instead. Aborting.</b>";
       exit;
   }

   $out = array();
   print "<b> Fast Restarting Asterisk Now! </b>";

   $statcmd = 'sync; sync; sync; export TERM=vt100 && sudo /usr/sbin/asterisk -rx "restart now"';
   exec($statcmd);

   // exec($statcmd, $out);
   // print_r ($out);

} else {
   print "<br><h3>ERROR: You Must login to use the 'RESTART' function!</h3>";
}

?>
