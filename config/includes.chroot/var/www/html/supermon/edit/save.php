<?php
include("../session.inc");

// Author: Paul Aidukas KN2R (Copyright) July 15, 2013
// For ham radio use only, NOT for comercial use!
// Be sure to allow popups from your Allmon web server to your browser!!
// Changes: KB4FXC 2018-02-04

if ($_SESSION['sm61loggedin'] !== true) {
    die ("<br><h3>ERROR: You Must login to use the 'Save' function!</h3>");
}

$edit = $_POST["edit"];
$filename = $_POST["filename"];
$edit = str_replace ("\r","",$edit);
print "<form name=REFRESH method=POST action='configeditor.php'>\n";
print "<link type='text/css' rel='stylesheet' href='/supermon/supermon.css'>";
print "<input name=return tabindex=50 TYPE=SUBMIT class=\"submit\" Value=\"Return to Index\"></form></h1>\n";
if (is_writable($filename)) {
   if (copy($filename, "$filename.bak")) {
      echo "<strong>Success, backup file created <em>($filename.bak)</em></strong><br>";
   }
   if (!$handle = fopen($filename, 'w')) {
        echo "<strong>Cannot open file <em>($filename)</em></strong>";
        exit;
   }
   if (fwrite($handle, $edit) === FALSE) {
       echo "<strong>Cannot write to file <em>($filename)</em></strong>";
       exit;
   }
   fclose($handle);
   $edit = nl2br ($edit);
   echo "<strong>Success, wrote edits to file <em>($filename)</em>:</strong><br><br>$edit<br>";
} else {
   echo "<strong>The file <em>($filename)</em> is not writable</strong>";
}
?>

