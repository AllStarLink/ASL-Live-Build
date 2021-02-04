<?php
include("../session.inc");

// Author: Paul Aidukas KN2R (Copyright) July 15, 2013
// For ham radio use only, NOT for comercial use!
// Be sure to allow popups from your Supermon web server to your browser!!

// Changes: KB4FXC 2017-03-18
// Changes: KB4FXC 2018-02-04
// Updates: KN2R 2019-04-25

$SUPERMON_DIR="/var/www/html/supermon";

include("../global.inc");
print "<html>\n<body style=\"background-color:powderblue;\">\n";
print "<font face=Arial size=2>\n";
if ($_SESSION['sm61loggedin'] === true) {
   print "<form name=REFRESH method=POST action='./configeditor.php'>";
   print "<link type='text/css' rel='stylesheet' href='/supermon/supermon.css'>";
   print "<h2> <i>$CALL</i> - AllStar Link / IRLP / EchoLink - Configuration File Editor </h2>";
   print "<p><b>Please use caution when editing files, misconfiguration can cause problems!</b></p><br>";
   print "<input name=refresh tabindex=50 class=submit TYPE=SUBMIT Value=Refresh> ";
   print " &nbsp; <input type=\"button\" class=\"submit\" Value=\"Close Window\" onclick=\"self.close()\"></form>";
   print "<form target=\"blank\" action=\"simpleusb-control-intro.php\">";
   print "<input type=\"submit\" class=\"submit\" value=\"SimpleUSB-Tune-Control\"></form>";
   print "<form action=edit.php method=post name=select>\n";
   print "<select name=file class=submit>\n";
   if (file_exists("$SUPERMON_DIR/global.inc"))
      print "<option value=\"$SUPERMON_DIR/global.inc\">Supermon - global.inc </option>\n";
   if (file_exists("$SUPERMON_DIR/allmon.ini"))
      print "<option value=\"$SUPERMON_DIR/allmon.ini\">Supermon - allmon.ini </option>\n";
   if (file_exists("$SUPERMON_DIR/favorites.ini"))
      print "<option value=\"$SUPERMON_DIR/favorites.ini\">Supermon - favorites.ini </option>\n";
   if (file_exists("$SUPERMON_DIR/controlpanel.ini"))
      print "<option value=\"$SUPERMON_DIR/controlpanel.ini\">Supermon - controlpanel.ini </option>\n";
   if (file_exists("$SUPERMON_DIR/common.inc"))
      print "<option value=\"$SUPERMON_DIR/common.inc\">Supermon - common.inc </option>\n";
   if (is_writable("$SUPERMON_DIR/footer.inc"))
      print "<option value=\"$SUPERMON_DIR/footer.inc\">Supermon - footer.inc </option>\n";
   if (file_exists("$SUPERMON_DIR/supermon.css"))
      print "<option value=\"$SUPERMON_DIR/supermon.css\">Supermon - supermon.css </option>\n";
   if (is_writable("$SUPERMON_DIR/style.css"))
      print "<option value=\"$SUPERMON_DIR/style.css\">Supermon - style.css </option>\n";
   if (is_writable("$SUPERMON_DIR/astlookup.css"))
      print "<option value=\"$SUPERMON_DIR/astlookup.css\">Supermon - astlookup.css </option>\n";
   if (is_writable("/var/www/html/lsnodes/lsnodes-form.css"))
      print "<option value=\"/var/www/html/lsnodes/lsnodes-form.css\">Supermon - lsnodes-form.css </option>\n";
   if (file_exists("/etc/asterisk/local/privatenodes.txt"))
      print "<option value=\"/etc/asterisk/local/privatenodes.txt\">Supermon - privatenodes.txt </option>\n";
   if (file_exists("/usr/local/etc/allstar.env"))
      print "<option value=\"/usr/local/etc/allstar.env\">AllStar - allstar.env </option>\n";
   if (file_exists("/etc/asterisk/http.conf"))
      print "<option value=\"/etc/asterisk/http.conf\">AllStar - http.conf </option>\n";
   if (file_exists("/etc/asterisk/ezstream.xml"))
      print "<option value=\"/etc/asterisk/ezstream.xml\">AllStar - ezstream.xml </option>\n";
   if (file_exists("/etc/asterisk/rpt.conf"))
      print "<option value=\"/etc/asterisk/rpt.conf\">AllStar - rpt.conf </option>\n";
   if (file_exists("/etc/asterisk/iax.conf"))
      print "<option value=\"/etc/asterisk/iax.conf\">AllStar - iax.conf </option>\n";
   if (file_exists("/etc/asterisk/extensions.conf"))
      print "<option value=\"/etc/asterisk/extensions.conf\">AllStar - extensions.conf </option>\n";
   if (file_exists("/etc/asterisk/dnsmgr.conf"))
      print "<option value=\"/etc/asterisk/dnsmgr.conf\">AllStar - dnsmgr.conf </option>\n";
   if (file_exists("/etc/asterisk/voter.conf"))
      print "<option value=\"/etc/asterisk/voter.conf\">AllStar - voter.conf </option>\n";
   if (file_exists("/etc/asterisk/manager.conf"))
      print "<option value=\"/etc/asterisk/manager.conf\">AllStar - manager.conf </option>\n";
   if (file_exists("/etc/asterisk/asterisk.conf"))
      print "<option value=\"/etc/asterisk/asterisk.conf\">AllStar - asterisk.conf </option>\n";
   if (file_exists("/etc/asterisk/modules.conf"))
      print "<option value=\"/etc/asterisk/modules.conf\">AllStar - modules.conf </option>\n";
   if (file_exists("/etc/asterisk/logger.conf"))
      print "<option value=\"/etc/asterisk/logger.conf\">AllStar - logger.conf </option>\n";
   if (file_exists("/etc/asterisk/usbradio.conf"))
      print "<option value=\"/etc/asterisk/usbradio.conf\">AllStar - usbradio.conf </option>\n";
   if (file_exists("/etc/asterisk/simpleusb.conf"))
      print "<option value=\"/etc/asterisk/simpleusb.conf\">AllStar - simpleusb.conf </option>\n";
   if (file_exists("/etc/asterisk/irlp.conf"))
      print "<option value=\"/etc/asterisk/irlp.conf\">AllStar - irlp.conf </option>\n";
   if (file_exists("/home/irlp/custom/environment"))
      print "<option value=\"/home/irlp/custom/environment\">IRLP - environment </option>\n";
   if (file_exists("/home/irlp/custom/custom_decode"))
      print "<option value=\"/home/irlp/custom/custom_decode\">IRLP - custom_decode </option>\n";
   if (file_exists("/home/irlp/custom/custom.crons"))
      print "<option value=\"/home/irlp/custom/custom.crons\">IRLP - custom.crons </option>\n";
   if (file_exists("/home/irlp/noupdate/scripts/irlp.crons")) {
      print "<option value=\"/home/irlp/noupdate/scripts/irlp.crons\">IRLP - irlp.crons </option>\n";
   } else {
      if (file_exists("/home/irlp/scripts/irlp.crons")) {
         print "<option value=\"/home/irlp/scripts/irlp.crons\">IRLP - irlp.crons </option>\n";
      }
   }
   if (file_exists("/home/irlp/custom/lockout_list"))
      print "<option value=\"/home/irlp/custom/lockout_list\">IRLP - lockout_list </option>\n";
   if (file_exists("/home/irlp/custom/timing"))
      print "<option value=\"/home/irlp/custom/timing\">IRLP - timing </option>\n";
   if (file_exists("/home/irlp/custom/timeoutvalue"))
      print "<option value=\"/home/irlp/custom/timeoutvalue\">IRLP - timeoutvalue </option>\n";
   if (file_exists("/etc/asterisk/echolink.conf"))
      print "<option value=\"/etc/asterisk/echolink.conf\">EchoLink - echolink.conf </option>\n";
   if (file_exists("/usr/local/bin/AUTOSKY/AutoSky.ini"))
      print "<option value=\"/usr/local/bin/AUTOSKY/AutoSky.ini\">AutoSky - AutoSky.ini </option>\n";
   if (file_exists("/usr/local/bin/AUTOSKY/AutoSky-log.txt"))
      if (filesize("/usr/local/bin/AUTOSKY/AutoSky-log.txt")) {
         print "<option value=\"/usr/local/bin/AUTOSKY/AutoSky-log.txt\">AutoSky - AutoSky-log.txt </option>\n";
      }
   if (file_exists("$SUPERMON_DIR/README"))
      print "<option value=\"$SUPERMON_DIR/README\">Allmon - README </option>\n";
   print "</select> &nbsp <input name=Submit type=submit class=submit value=\" Edit File \"></form>\n";
print "</form>";
} else {
print "<br><h3>ERROR: You Must login to use the 'Configuration Editor' tool!</h3>";
}
print "</font>\n</body>\n</html>";
?>

