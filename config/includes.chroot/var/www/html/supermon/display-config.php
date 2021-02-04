<?php
//include("session.inc");

//if ($_SESSION['sm61loggedin'] !== true) {
//    die ("<br><h3>ERROR: You Must login to use the 'Display Config' function!</h3>");
//}

?>

<html>

<!-- Program to Set Supermon Display format
     Runs in both logged and unlogged modes
     Data stored in cookies for any connected user
     D. Crompton, WA3DSP 2/2018
-->

<head>
<link type="text/css" rel="stylesheet" href="supermon.css">

<script>
<!-- window.onunload = refreshParent;
-->
    function refreshParent() {
        window.opener.location.reload();
    }
</script>

</head>

<body style="background-color:powderblue;">
<center>
<p style="font-size:1.5em;margin-bottom:0;"><b>Supermon Display Settings</b></p>

<?php

$update=0;

if ( (isset($_GET["number_displayed"])) && ($_GET["number_displayed"] != "" )) {

  $update=1;

  $ndisp=$_GET["number_displayed"];
  $snum=$_GET["show_number"];
  $sall=$_GET["show_all"];

// print "<p style='margin-left:40px;margin-bottom:0;'>Number Displayed - <b>$ndisp</b>&nbsp;&nbsp;&nbsp;&nbsp; Show Number - <b>$snum</b>&nbsp;&nbsp;&nbsp;&nbsp; Show All - <b>$sall</b></p>";

$expiretime=2147483645;  // never expire  

setcookie("display-data[number-displayed]", $ndisp, $expiretime);
setcookie("display-data[show-number]", $snum, $expiretime);
setcookie("display-data[show-all]", $sall, $expiretime);

?>
    <script type="text/javascript">
    refreshParent();
    </script>
<?php

}
?>
<center>
<form action="display-config.php" method="get">
<table cellspacing="15" style="margin-top:0;">
<tr>
<td valign="top">
 Show the number of connections (Displays x of y)<br>
 <input type="radio" class="submit" name="show_number" value="1" checked> YES<br>
 <input type="radio" class="submit" name="show_number" value="0"> NO<br>
</td>
</tr><tr>
<td valign="top">
 Show ALL Connections (NO omits NEVER Keyed)<br>
 <input type="radio" class="submit" name="show_all" value="1" checked> YES<br>
 <input type="radio" class="submit" name="show_all" value="0"> NO<br>
</td>
</tr><tr>
<td valign="top">
 Maximum Number of Connections to Display<br>in Each Node (0=ALL)<br><br>
 <input type="text" name="number_displayed" value="0" maxlength="4" size="3">
</select>
</td>
</tr>
<tr>
<?php
if ($update == 0) {
  if (isset($_COOKIE['display-data'])) {
    foreach ($_COOKIE['display-data'] as $name => $value) {
        $name = htmlspecialchars($name);
        $value = htmlspecialchars($value);
        switch ($name) {
            case "number-displayed";
               $ndisp=$value;
               break;
            case "show-number";
               $snum=$value; 
	       break;
            case "show-all";
               $sall=$value;
	       break;
        }
    }
  } else {

    $ndisp=0;
    $snum=0;
    $sall=1;
  }
} 

echo '<tr><td><span style="text-decoration: underline;font-weight: bold;">Current Values:</span><br><br>';
echo "Show the Number Displayed - ";
if ($snum == 0) {
  echo "NO";
} else {
  echo "YES";
}
echo "<br>";
echo "Show ALL Connections - ";
if ($sall == 0) {
  echo "NO";
} else {
  echo "YES";
}
echo "<br>Maximum Connections Displayed/Node - " . $ndisp;
echo "</td></tr>";

?>
<td colspan="4" align="center">
<input type="submit" class="submit" value="Update">
 &nbsp; 
<input type="button" class="submit" Value="Close Window" onclick="self.close()">
</td>
</tr>
</table>
</form>
</body>
</html>

