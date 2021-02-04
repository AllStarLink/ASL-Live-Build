<?php
include("session.inc");

if ($_SESSION['sm61loggedin'] !== true) {
    die ("<br><h3>ERROR: You Must login to use the 'Allow/Restrict function' function!</h3>");
}
?>

<html>
<!-- Ban/Allow nodes in AllStar
     Copyright - D. Crompton, WA3DSP 2/2018
     For use with hamvoip/supermon

     This intro calls node-ban-allow.php
-->

<head>
<link type="text/css" rel="stylesheet" href="supermon.css">
</head>
<body style="background-color:powderblue;">

<p style="text-align:center;font-size:1.5em;"><b>Allow/Restrict AllStar Nodes</b></p>
<p>
   This function can be used to temporarily or permanently block or
   allow remote nodes to connect to your node. Commonly called a 
   blacklist or whitelist. Only one list can be in effect but both
   could be defined. You can either specify a list to allow (whitelist)
   or ban (blacklist). The blacklist is useful when there is an issue
   with a node that is causing problems at your end. This could be a
   node that has some technical issue that is keying or hanging up your
   node or perhaps someone who is not abiding by FCC or your rules.
   You should always try to contact the person you are blocking but in
   some cases that may not be possible. On the other hand a whitelist
   allows you to specify a list of nodes that can connect to your node.
   Only those nodes in the whitelist will be able to connect. In most
   situations you would use the blacklist blocking one or several nodes.
   If the blacklist is empty and active all nodes can connect, if the
   whitelist is empty and active no nodes can connect.
</p>
<p>
   The database name is either "whitelist" or "blacklist" You <b>MUST</b>
   configure your extension.conf and iax.conf files as described at
   this URL in order for this to work.
</p>
<p>   
   <a href="http://wiki.allstarlink.org/wiki/Blacklist_or_whitelist">http://wiki.allstarlink.org/wiki/Blacklist_or_whitelist</a> 
</p>
<form action="node-ban-allow.php">
<center>
<input type="submit" class="submit" value="Continue">
 &nbsp; 
<input type="button" class="submit" Value="Close Window" onclick="self.close()">
</center>
</form>
</body>
</html>



