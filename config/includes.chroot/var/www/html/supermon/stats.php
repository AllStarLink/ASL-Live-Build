<?php

include("session.inc");
include("amifunctions.inc");
include("common.inc");

// Author: Paul Aidukas KN2R (Copyright) July 15, 2013
// For ham radio use only, NOT for comercial use!
// Be sure to allow popups from your Allmon web server to your browser!!
// Major update by KB4FXC 02/2018
// Minor updates by KN2R 04/2019

?>
<html>
<head>
<title>AllStar Status</title>
</head>
<body>
<pre>
<?php
	if ($_SESSION['sm61loggedin'] === true) {

		if (!file_exists('allmon.ini'))					 // Read supermon INI file
			die("Couldn't load allmon ini file.\n");

		$config = parse_ini_file('allmon.ini', true);

		$node = @trim(strip_tags($_GET['node']));
		$localnode = @trim(strip_tags($_GET['localnode']));

		if (!isset($config[$node]))					 // Check if node exists in ini
			die("Node $node is not in allmon ini file.");

		if (($fp = AMIconnect($config[$node]['host'])) === FALSE)	// Set up Asterisk manager connection
			die("Could not connect to Asterisk Manager.");

		if (AMIlogin($fp, $config[$node]['user'], $config[$node]['passwd']) === FALSE)	// Login to Asterisk manager
			die("Could not login to Asterisk Manager.");

		page_header();
		show_all_nodes($fp);
		show_peers($fp);
		show_channels($fp);
		show_netstats($fp);

	} else
		echo ("<br><h3>ERROR: You Must login to use this function!</h3>");
?>
</pre>
</body>
</html>


<?php		// Local Functions...

function page_header()
{
	global $HOSTNAME, $AWK, $DATE;

	echo "#################################################################\n";
	$host = `$HOSTNAME | $AWK -F. '{printf ("%s", $1);}'`;
	$date = trim(`$DATE`);
	echo " $host AllStar Status: $date\n";
	echo "#################################################################\n";
	echo "\n";
}

function show_all_nodes($fp)
{
	global $TAIL, $HEAD, $GREP, $SED;

	$nodes = AMIcommand ($fp, "rpt localnodes");
	$nodelist = explode ("\n", $nodes);

	$end = count($nodelist) - 2;

	for ($i = 3; $i < $end; $i++) { 

		$node = $nodelist[$i];

		$AMI1 = AMIcommand ($fp, "rpt xnode $node");		// Retrieve data

		$CNODES3 = trim (`echo -n "$AMI1" |$GREP "^RPT_ALINKS" |$SED 's/,/: /' |$SED 's/[a-zA-Z\=\_]//g'`);
		echo "Node $node connections => $CNODES3\n";

		echo "\n************************* CONNECTED NODES *************************\n";

		$N3 = trim (`echo -n "$AMI1" | $TAIL --lines=+3 | $HEAD --lines=1`);
		$res = explode (", ", $N3);
		$CNODES2 = count ($res);
		$tmp = trim($res[0]);
		if ("$tmp" != "<NONE>") {
      			printf (" %3s node(s) total:\n     ", "$CNODES2");;
			$k = 0;
			for ($j = 0; $j < $CNODES2 - 1; $j++) {		// Pretty print!
				printf ("%8s, ", trim ($res[$j]));
				if ($k >= 10) {
					$k = 0;
					echo "\n     ";
				} 
				else $k++;
			}
			printf ("%8s\n\n", trim ($res[$j]));

		} else
      			echo htmlspecialchars("<NONE>") . "\n\n";

		echo "***************************** LSTATS ******************************\n";

		$AMI2 = AMIcommand ($fp, "rpt lstats $node");		// Retrieve data

		$N = trim (`echo -n "$AMI2" | $HEAD --lines=-1`);
		echo "$N\n\n\n";
   }
}

function show_channels($fp)
{
	global $HEAD;

	$AMI1 = AMIcommand ($fp, "iax2 show channels");

	echo "**************************** CHANNELS *****************************\n";

	$channels = trim (`echo -n "$AMI1" | $HEAD --lines=-1`);

	echo "$channels\n\n";
}

function show_netstats($fp)
{
	global $HEAD; $EGREP;

	$AMI1 = AMIcommand ($fp, "iax2 show netstats");

	echo "**************************** NETSTATS *****************************\n";

	$channels = `echo -n "$AMI1" | $HEAD --lines=-1`;

	echo "$channels\n\n";
}

function show_peers($fp)
{
	global $HEAD, $EGREP;

	$AMI1 = AMIcommand ($fp, "iax2 show peers");

	echo "*************************** OTHER PEERS ***************************\n";

	$peers = trim (`echo -n "$AMI1" | $HEAD --lines=-1 | $EGREP -v '^Name|iax2 peers|Unspecified|^$'`);

	if ("$peers")
		echo "$peers\n\n\n";
	else
		echo htmlspecialchars("<NONE>") . "\n\n\n";
}

?>

