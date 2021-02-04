<?php
include("../session.inc");

// Author: Paul Aidukas KN2R (Copyright) July 15, 2013
// For ham radio use only, NOT for comercial use!
// Be sure to allow popups from your Allmon web server to your browser!!
// Changes: KB4FXC 2018-02-04

if ($_SESSION['sm61loggedin'] !== true) {
    die ("<br><h3>ERROR: You Must login to use the 'Edit' function!</h3>");
}
$file = $_POST["file"];
echo $file;
$fh = fopen($file,'r') or die('<br><br> ERROR: Could not open file: ' . $file. '<br><br> Does not exist or is Protected.');
$data = fread($fh, filesize($file)) or die('<br><br> ERROR: Could not read file!');
fclose($fh);
$nldata = nl2br ($data);

if (is_writable($file))
{  $write_ok = 1;
?>
<form style="display:inline;" action="save.php" method="post" name="savefile" target="_self">
<link type='text/css' rel='stylesheet' href='/supermon/supermon.css'>
<textarea name="edit" style="width:100%; height:94%;" wrap="off"><?php echo $data;?></textarea>
<input name="filename" type="hidden" value="<?php echo $file;?>">
<br><input name="Submit" type="submit" class=submit value=" WRITE your Edits ">
</form>
<?php
   print "<form style='display:inline;' name=REFRESH method=POST action='./configeditor.php'>\n";
   print "<link type='text/css' rel='stylesheet' href='/supermon/supermon.css'>";
   print "<input name=return tabindex=50 TYPE=SUBMIT class=\"submit\" Value=\"Return to Index without Writing\"></form></h1>\n";

} else {
   print "<p> File is <b>READ ONLY</b> - To edit, use vi or nano in a Linux shell</p>";
   print "<form name=REFRESH method=POST action='./configeditor.php'>\n";
   print "<link type='text/css' rel='stylesheet' href='/supermon/supermon.css'>";
   print "<input name=return tabindex=50 TYPE=SUBMIT class=\"submit\" Value=\"Return to Index\"></form></h1>\n";
   $write_ok = 0;
?>
<form action="save.php" method="post" name="savefile" target="_self">
<link type='text/css' rel='stylesheet' href='/supermon/supermon.css'>
<textarea name="edit" style="width:100%; height:87%;" wrap="off"><?php echo $data;?></textarea>
<input name="filename" type="hidden" value="<?php echo $file;?>">
<br><br>
</form></h1>
<?php
}
