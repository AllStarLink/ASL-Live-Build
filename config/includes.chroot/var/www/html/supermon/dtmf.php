<?php
include("session.inc");
include('amifunctions.inc');

// Author: Paul Aidukas KN2R (Copyright) January 15, 2013
// For ham radio use only, NOT for comercial use!
// Major update by KB4FXC 02/2018

	$dtmf = @trim(strip_tags($_POST['node']));
	$localnode = @trim(strip_tags($_POST['localnode']));

	if ($_SESSION['sm61loggedin'] === true) {

		if ($dtmf == '')
			die("Please provide a DTMF command.\n");

		if (!file_exists('allmon.ini'))					 // Read supermon INI file
			die("Couldn't load allmon ini file.\n");

		$config = parse_ini_file('allmon.ini', true);

		if (!isset($config[$localnode]))				 // Check if node exists in ini
			die("Node $node is not in allmon ini file.");

		if (($fp = AMIconnect($config[$localnode]['host'])) === FALSE)	// Set up Asterisk manager connection
			die("Could not connect to Asterisk Manager.");

		if (AMIlogin($fp, $config[$localnode]['user'], $config[$localnode]['passwd']) === FALSE)	// Login to Asterisk manager
			die("Could not login to Asterisk Manager.");

		do_dtmf_cmd($fp, $localnode, $dtmf);

	} else
		print "<br><h3>ERROR: You Must login to use the 'DTMF' function!</h3>";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function do_dtmf_cmd($fp, $localnode, $dtmf)
{
	$AMI1 = AMIcommand ($fp, "rpt fun $localnode $dtmf");

	print "<b>Executing DTMF command '$dtmf' on node $localnode</b>";

}



?>
