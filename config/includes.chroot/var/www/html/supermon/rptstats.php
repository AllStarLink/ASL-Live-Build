<?php


include("session.inc");
include("amifunctions.inc");

// Author: Paul Aidukas KN2R (Copyright) July 15, 2013
// For ham radio use only, NOT for comercial use!
// Be sure to allow popups from your Allmon web server to your browser!!
// Major update by KB4FXC 02/2018

	$node = (int)trim(strip_tags($_GET['node']));
	$localnode = (int)trim(strip_tags($_GET['localnode']));

	if ($_SESSION['sm61loggedin'] === true && $node > 0)
		$title = "AllStar 'rpt stats' for node: $node";
	else
		$title = "AllStar 'rpt stats' for node: $localnode";

	if ($_SESSION['sm61loggedin'] === true) {

		if ($node) {
			echo file_get_contents("http://stats.allstarlink.org/nodeinfo.cgi?node=$node");

		} else {

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css" />
<title><?php echo "$title"; ?></title>
</head>
<body>
<pre>
<?php
			if (!file_exists('allmon.ini'))					 // Read supermon INI file
				die("Couldn't load allmon ini file.\n");

			$config = parse_ini_file('allmon.ini', true);

			if (!isset($config[$localnode]))				 // Check if node exists in ini
				die("Node $node is not in allmon ini file.");

			if (($fp = AMIconnect($config[$localnode]['host'])) === FALSE)	// Set up Asterisk manager connection
				die("Could not connect to Asterisk Manager.");

			if (AMIlogin($fp, $config[$localnode]['user'], $config[$localnode]['passwd']) === FALSE)	// Login to Asterisk manager
				die("Could not login to Asterisk Manager.");

			show_rpt_stats($fp, $localnode);
?>
</pre>
</body>
</html>
<?php
		}

	} else
		echo ("<br><h3>ERROR: You Must login to use this function!</h3>");
?>


<?php

function show_rpt_stats($fp, $node)
{
	$AMI1 = AMIcommand ($fp, "rpt stats $node");

	$stats = trim (`echo -n "$AMI1" | head --lines=-1`);
	if ("$stats")
		echo "$stats\n\n";
	else
		echo htmlspecialchars("<NONE>") . "\n";
}



?>

