<?php
//	Some modifications. KB4FXC 02/2018
include("session.inc");
include("common.inc");

if ($_SESSION['sm61loggedin'] !== true) {
    die ("<br><h3>ERROR: You Must login to use the 'Restrict' function!</h3>");
}
?>

<html>
<!-- Allow/Restrict nodes in AllStar
     Copyright - D. Crompton, WA3DSP 2/2018
     Script modified for use with hamvoip/supermon
-->

<head>
<link type="text/css" rel="stylesheet" href="supermon.css">
</head>
<body style="background-color: powderblue;">

<p style="text-align:center;font-size: 1.5em;"><b>Allow/Restrict AllStar Nodes</b></p>

<?php
if ( (isset($_GET["whiteblack"])) && ($_GET["whiteblack"] != "" )) {
  $whiteblack=$_GET["whiteblack"];
  $node=$_GET["node"];
  $comment=$_GET["comment"];
  $deleteadd=$_GET["deleteadd"];

if ( $whiteblack == "whitelist" ) {
    $DBname = "whitelist";
 } else {
    $DBname= "blacklist";
}

if ( $deleteadd == "add" ) {
    $cmd = "put";
    $ret=`$SUDO $ASTERISK -rx "database $cmd $DBname $node \"$comment\""`;
 } else {
    $cmd = "del";
    $ret=`$SUDO $ASTERISK -rx "database $cmd $DBname $node"`;
}

}
?>

<center>
<form action="node-ban-allow.php" method="get">
<table cellspacing="20">
<tr>
<td align="top">
 <input type="radio" class="submit" name="whiteblack" value="blacklist" checked> Restricted - blacklist<br>
 <input type="radio" class="submit" name="whiteblack" value="whitelist"> Allowed - whitelist<br>
</td></tr>
<tr><td>
Enter Node number -  
 <input type="text" name="node" maxlength="7" size="5">
</td></tr>
<td>
Enter comment -
 <input type="text" name="comment" maxlength="30" size="22">
</td></tr>
<tr>
<td>
 <input type="radio" class="submit" name="deleteadd" value="add" checked> Add<br>
 <input type="radio" class="submit" name="deleteadd" value="delete"> Delete<br>
</td>
</tr>
<tr>
<td>Current Nodes in the Restricted - blacklist:
<?php
$data=`$SUDO $ASTERISK -rx "database show blacklist"`;
if ( $data == "" ) {
   print "<p>---NONE---</p>";
} else {
   print "<pre>$data</pre>";
} 
?>
</td></tr>
<p>
<input type="submit" class="submit" value="Update">
 &nbsp; 
<input type="button" class="submit" Value="Close Window" onclick="self.close()">
</p>
<tr>
<td>Current Nodes in the Allowed - whitelist:
<?php
$data=`$SUDO $ASTERISK -rx "database show whitelist"`; 
if ( $data == "" ) {
    print "<p>---NONE---</p>";
} else {
    print "<pre>$data</pre>";
}
//print "database $cmd $DBname $node \"$comment\"";
//print $ret;

$blist=`$GREP -oP '^\s*context\s*=\s*blacklist' /etc/asterisk/iax.conf`;
$wlist=`$GREP -oP '^\s*context\s*=\s*whitelist' /etc/asterisk/iax.conf`;
print "<p>System currently setup to use - <b>";
if ( $blist != "" ) {
   print "BLACKLIST";
} elseif ( $wlist != "" ) {
   print "WHITELIST";
} else {
   print "NONE DEFINED";
}
print "</b></p>";

?>
</td></tr>
</table
</center>
</form>
</body>
</html>

