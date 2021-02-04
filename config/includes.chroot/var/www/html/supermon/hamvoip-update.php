<?php
include("session.inc");
include("common.inc");

if ($_SESSION['sm61loggedin'] !== true || $system_type !== 'hamvoip') {
    die ("<br><h3>ERROR: You Must login and be using HamVoIP V1.5+ to use the 'Update' function!</h3>");
}

?>

<html>
<!-- Program to Update hamvoip V1.5+ code
     D. Crompton, WA3DSP 3/2018
     For use with hamvoip/supermon
-->

<head>
<link type="text/css" rel="stylesheet" href="supermon.css">

<style>
/* Center the loader */
#loader {
  position: absolute;
  left: 50%;
  top: 45%;
  z-index: 1;
  width: 150px;
  height: 150px;
  margin: -75px 0 0 -75px;
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid blue;
  border-right: 16px solid green;
  border-bottom: 16px solid red;
  border-left: 16px solid pink;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Add animation to "page content" */
.animate-bottom {
  position: relative;
  -webkit-animation-name: animatebottom;
  -webkit-animation-duration: 1s;
  animation-name: animatebottom;
  animation-duration: 1s
}

@-webkit-keyframes animatebottom {
  from { bottom:-100px; opacity:0 } 
  to { bottom:0px; opacity:1 }
}

@keyframes animatebottom { 
  from{ bottom:-100px; opacity:0 } 
  to{ bottom:0; opacity:1 }
}

#myDiv {
  display: none;
  text-align: center;
}

</style>
</head>

<body style="background-color:powderblue;">

<script>
var myVar;

function myFunction() {
    myVar = setTimeout(showPage, 60000);
}

function closePageTimer() {
    myVar = setTimeout(closePage,65000);
}

function closePage() {
	refreshParent();
        window.open('','_parent',''); 
        window.close(); 
}

function showPage() {
  document.getElementById("loader").style.display = "none";
  document.getElementById("myDiv").style.display = "block";
}

function refreshParent() {
  window.opener.location.reload();
}
</script>

<center>
<p style="font-size:1.5em;"><b>HamVoIP Update Status</b></p>
</center>

<?php

function liveExecute($cmd)
{
    while (@ ob_end_flush()); // no output buffers

    $proc = popen("$cmd", 'r');
    $live_output     = "";
    $complete_output = "";
    echo "<pre style='margin-left:4em;'>";
    while (!feof($proc))
    {
        $live_output     = fread($proc, 4096);
        $complete_output = $complete_output . $live_output;
        echo "$live_output";
        @ flush();
    }
    echo "</pre>";
    pclose($proc);
    return $complete_output;
}

if(! isset($_POST['update']) && ! isset($_POST['status']) && ! isset($_POST['log'])){

?>

	<p style="margin-left:4em;margin-right:4em;margin-top:3em;">
	This program checks for updates to your HamVoIP version 1.5+ image.
	It is important to regularly check for updates as they not only improve the 
	performance of your server but also adds additional features. Some updates require
	a reboot. After an update you should refresh your browser screen.
        </p>
	<p style="margin-left:4em;margin-right:4em;"> 
	Use the [Status] button to check if there are any updates available for your system. 
	</p>
	<center>
	<form method="post">
	<input type="submit" class="submit" name="status" value="Status">
	&nbsp; <input type="button" class="submit" Value="Close Window" onclick="self.close()">
	</form>
	</center>

<?php
} else if (isset($_POST['status'])) {

	print "<center>";
	print "<p>Current version - RPi2-3 $version</p>";
	print "<p>Checking update status</p>";

	$IP=exec('curl -s http://myip.hamvoip.org/ 2>&1');
	$PACMAN="/usr/local/hamvoip-pacman/bin/pacman";

	//echo "<p>$IP</p>";
	//echo "<p>$PACMAN</p>";

	if ($IP != "") {
	   echo "</center>";
           liveexecute("export TERM=vt100 && sudo $PACMAN -Sy");
	   $result=liveexecute("export TERM=vt100 && sudo $PACMAN -Qu");
	   echo "<center>";
	   if($result === ""){
   	     echo "<p>Server is up to date</p>";
	   } else {
	     echo "<p>Packages waiting for update</p>";
             echo "<p>You should login using PuTTY or SSH and use the Admin menu item 1 to perform an update at your earliest convenience.</p>";
	   } 
        } else {
           print "<p>No Internet connectivity - cannot check update status</p>";
        }
?>
	<form action="hamvoip-update.php">
	<input type="submit" class="submit" value="Continue">
	</form>
	</center>

<?php

} else if (isset($_POST['update'])) {

	echo "<center>";
	$IP=exec('curl -s http://myip.hamvoip.org/ 2>&1');

	// Test for Internet Connectivity
	if ($IP != "") { 

	liveexecute("echo '<p style=\"font-size:1.3em;font-weight:bold;\">Retrieving the latest system updates<br>DO NOT power cycle or leave this screen until complete.<br>Updating - Please wait!</p>'"); 

	liveexecute("export TERM=vt100 && sudo /usr/local/sbin/hamvoip-sys-update.sh > /dev/null 2>/dev/null &");

?>
	<div id="loader"></div>

	<div style="display:none;" id="myDiv" class="animate-bottom">
	<p><b>Update Completed</b></p>
	<p><b>This page will close in 10 seconds and refresh your browser</b></p>
	</div>

	<script type="text/javascript">
	myFunction();
	</script>


 <script type="text/javascript">
    closePageTimer();
    </script>


<!--
    <script type="text/javascript">
    refreshParent();
    </script>


<button onclick="self.close()">Close</button>
-->

<?php

	} else {
        echo "<p>No Internet connectivity - cannot update packages</p>";               
	}
?>
        </center>
<?php

} else if (isset($_POST['log'])) {

	echo "<center>";
	echo "<p>Update Log</p>";
	$Data = file_get_contents('/tmp/update.log');
	if (empty($Data)) {
		echo "<p>The Log File is Empty</p>";
	} else {
	echo "<pre>$Data</pre>";
	}

?>
        <form action="hamvoip-update.php">
        <input type="submit" class="submit" value="Continue">
        </form>
        </center>
<?php
}
	
?>

</body>
</html>

