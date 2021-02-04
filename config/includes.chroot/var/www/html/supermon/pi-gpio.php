<?php
include("session.inc");

if ($_SESSION['sm61loggedin'] !== true) {
    die ("<br><h3>ERROR: You Must login to use the 'Pi GPIO' function!</h3>");
}
?>

<html>
<!-- Example program to control and view RPi2/3 GPIO
     D. Crompton, WA3DSP 3/2017
     For use with hamvoip/supermon
-->

<head>
<link type="text/css" rel="stylesheet" href="supermon.css">
</head>
<body style="background-color:powderblue;">
<center>
<p style="font-size:1.5em;"><b>RPi GPIO Status</b></p>

<?php
if ( (isset($_GET["direction"])) && ($_GET["direction"] != "" )) {
  $Direction=$_GET["direction"];
  $Bit=$_GET["bit"];
  $State=$_GET["state"];
  $Pullup=$_GET["pullup"];
  print "<p style='margin-left:40px;margin-bottom:0;'>Direction - <b>$Direction</b>&nbsp;&nbsp;&nbsp;&nbsp; Bit - <b>$Bit</b>&nbsp;&nbsp;&nbsp;&nbsp;Pullup - <b>$Pullup</b>&nbsp;&nbsp;&nbsp;&nbsp;State - <b>$State</b></p>";
  if ( $Direction == "In" ) {
     exec("gpio mode $Bit input");
     if ( $Pullup == "Yes" ) {
        exec("gpio mode $Bit up");
     } else {
        exec("gpio mode $Bit down");
     } 
  } else {
     exec("gpio mode $Bit output");
     exec("gpio write $Bit $State");
  }     
 } else {
 print "<br>";
}
?>
<form action="pi-gpio.php" method="get">
<table cellspacing="30">
<tr>
<td valign="top">
 Select Input or Output<br>
 <input type="radio" class="submit" name="direction" value="In" checked> Input<br>
 <input type="radio" class="submit" name="direction" value="Out"> Output<br>
</td>
<td valign="top">
 Pullup<br>
 <input type="radio" class="submit" name="pullup" value="No" checked> No<br>
 <input type="radio" class="submit" name="pullup" value="Yes"> Yes<br>
</td>
<td valign="top">
  Select Bit
<select name="bit" class="submit">
  <option value="0"> 0</option>
  <option value="1"> 1</option>
  <option value="2"> 2</option>
  <option value="3"> 3</option>
  <option value="4"> 4</option>
  <option value="5"> 5</option>
  <option value="6"> 6</option>
  <option value="7"> 7</option>
  <option value="21">21</option>
  <option value="22">22</option>
  <option value="23">23</option>
  <option value="24">24</option>
  <option value="25">25</option>
  <option value="26">26</option>
  <option value="27">27</option>
  <option value="28">28</option>
  <option value="29">29</option>
</select>
</td>
<td valign="top">
 State<br>
<input type="radio" class="submit" name="state" value="0" checked> 0<br>
<input type="radio" class="submit" name="state" value="1"> 1<br>
</td>
</tr>
<tr>
<td colspan="4" align="center">
<input type="submit" class="submit" value="Update">
 &nbsp; 
<input type="button" class="submit" Value="Close Window" onclick="self.close()">
</td>
</tr>
</table>
</form>
<?php
$data=`gpio readall`;
print "<pre>$data</pre>";

?>
<input type="button" class="submit" Value="View GPIO howto" onclick=window.open("https://www.hamvoip.org/GPIO_how-to.pdf")
</body>
</html>
