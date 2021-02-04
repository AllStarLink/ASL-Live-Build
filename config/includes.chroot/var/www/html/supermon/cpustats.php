<?php
include("session.inc");

// Author: Paul Aidukas KN2R (Copyright) July 15, 2013
// For ham radio use only, NOT for comercial use!
// Be sure to allow popups from your Allmon web server to your browser!!
// Major update by KB4FXC 02/03/2018

?>
<html>
<head>
<title>CPU and System Status</title>
</head>
<body>
<pre>
<?php
	if ($_SESSION['sm61loggedin'] === true) {

		$cmd = "/bin/date";
		echo "Command: $cmd\n-----------------------------------------------------------------\n";
		passthru ($cmd);
		echo "\n\n";

		$cmd = "export TERM=vt100 && sudo /usr/local/sbin/supermon/ssinfo - ";
		echo "Command: $cmd\n-----------------------------------------------------------------\n";
		passthru ($cmd);
		echo "\n\n";

		$cmd = "/bin/ifconfig";
		echo "Command: $cmd\n-----------------------------------------------------------------\n";
		passthru ($cmd);
		echo "\n\n";

		$cmd = "/usr/local/sbin/supermon/din";
		echo "Command: $cmd\n-----------------------------------------------------------------\n";
		passthru ($cmd);
		echo "\n\n";

		$cmd = "/bin/df -hT";
		echo "Command: $cmd\n-----------------------------------------------------------------\n";
		passthru ($cmd);
		echo "\n\n";

		$cmd = "export TERM=vt100 && sudo /bin/top -b -n1";
		echo "Command: $cmd\n-----------------------------------------------------------------\n";
		passthru ($cmd);


	} else
		echo ("<br><h3>ERROR: You Must login to use this function!</h3>");
?>
</pre>
</body>
</html>
