<?php
include("session.inc");
include("amifunctions.inc");
include("global.inc");
include("common.inc");

// Author: Paul Aidukas KN2R (Copyright) July 15, 2013
// For ham radio use only, NOT for comercial use!
// Be sure to allow popups from your Allmon web server to your browser!!
// Major update by KB4FXC 02/2018

	$node = trim(strip_tags($_GET['node']));
	$localnode = trim(strip_tags($_GET['localnode']));
	$intnode = (int)$node;
	$perm = @trim(strip_tags($_GET['perm']));
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="astlookup.css" />
<title>Opening information for: <?php echo "$node"; ?></title>
</head>
<body>
<pre>
<?php
	if ($_SESSION['sm61loggedin'] === true) {

		/////////////   Connect to AMI ////////////////////////////

                if (!file_exists('allmon.ini'))						// Read supermon INI file
                        die("Couldn't load allmon ini file.\n");

                $config = parse_ini_file('allmon.ini', true);

                if (!isset($config[$localnode]))					// Check if node exists in ini
                        die("Node $localnode is not in allmon ini file.");

                if (($fp = AMIconnect($config[$localnode]['host'])) === FALSE)		// Set up Asterisk manager connection
                        die("Could not connect to Asterisk Manager.");

                if (AMIlogin($fp, $config[$localnode]['user'], $config[$localnode]['passwd']) === FALSE)  // Login to Asterisk manager
                        die("Could not login to Asterisk Manager.");

		/////////////   Perform specified Lookup //////////////////

		echo "</pre><font face='Courier New' > <table width='100%' size='1'>\n";

		if ("$intnode" != "$node") {				// Do lookup by callsign

			do_allstar_callsign_search($fp, $node, $localnode);

			if ($perm != "on") {
				do_echolink_callsign_search($fp, $node);
				do_irlp_callsign_search($node);
			}

		} else if ($intnode > 80000 && $intnode < 90000) {	// Lookup by IRLP node number

			do_irlp_number_search($intnode);

		} else if ($intnode > 3000000) {			// Lookup by echolink node number

			do_echolink_number_search($fp, $intnode);

		} else {						// Lookup by AllStar node number

			do_allstar_number_search($fp, $intnode, $localnode);
		}

		echo "</table>\n";

	} else 
		print "<br><h3>ERROR: You Must login to use this function!</h3>";

?>
</pre>
</body>
</html>

<?php		// Local Functions...

function do_allstar_callsign_search($fp, $lookup, $localnode) {

	global $ASTDB_TXT, $CAT, $AWK;

	$text = "AllStar Callsign Search for: \"$lookup\"  ";
	$i = strlen ($text);
	$dashes = substr("--------------------------------------------------------------------------------------------", 0, 80 - $i);
	echo "<tr><td colspan=5><pre>$text$dashes\n</td></tr>";

	$res = `$CAT $ASTDB_TXT | $AWK '-F|' 'BEGIN{IGNORECASE=1} $2 ~ /$lookup/ {printf ("%s\x18", $0);}'`;
	//echo "<!-- astdb: $res -->\n";

	process_allstar_result ($fp, $res, $localnode);

}

function do_allstar_number_search($fp, $lookup, $localnode) {

	global $ASTDB_TXT, $CAT, $AWK;

	$text = "AllStar Node Number Search for: \"$lookup\"  ";
	$i = strlen ($text);
	$dashes = substr("--------------------------------------------------------------------------------------------", 0, 80 - $i);
	echo "<tr><td colspan=5><pre>$text$dashes\n</td></tr>";

	$res = `$CAT $ASTDB_TXT | $AWK '-F|' 'BEGIN{IGNORECASE=1} $1 ~ /$lookup/ {printf ("%s\x18", $0);}'`;
	//echo "<!-- astdb: $res -->\n";

	process_allstar_result ($fp, $res, $localnode);

}

function process_allstar_result ($fp, $res, $localnode) {

	global $HEAD, $SED, $AWK, $GREP;

	if ("$res" == "") {
		echo "<tr><td colspan=5><pre>\n\n....Nothing Found....\n</td></tr>";
		return;
	}

	$table = explode ("\x18", $res);
	array_pop ($table);

	foreach ($table as $row) {
		echo "<tr>";

		$column = explode ("|", $row);
		$node = trim($column[0]);
		$call = trim($column[1]);
		$desc = trim($column[2]);
		$qth = trim($column[3]);

		$AMI2 = AMIcommand ($fp, "rpt lookup $node");           // Retrieve node data if available
		$N = trim (`echo -n "$AMI2" | $HEAD --lines=-1 | $AWK ' $2 ~ /$localnode,/ {printf ("%s %s %s", $4,$6,$0)}' | $SED 's/, / /g' | $SED 's/NOT FOUND/NOT-FOUND/g' | $AWK '{printf ("%5s %5s %s", $1,$2,$(NF))}' `);
		//echo "<!-- $N -->\n";

		$G = `echo -n "$N" | $GREP 'NOT-FOUND'`;

		if (strlen ("$G") >= 9)
			$N = "          NOT FOUND";

		echo "<td><pre>$node</pre></td> <td><pre>$call</pre></td> <td><pre>$desc</pre></td> <td><pre>$qth</td> <td><pre>$N</pre></td>";
		echo "</tr>\n";
	}
}

function do_echolink_callsign_search($fp, $lookup) {

	global $AWK, $GREP, $MBUFFER;

	$AMI = AMIcommand ($fp, "echolink dbdump");		// Retrieve echolink data if available

	$descriptorspec = array(
		0 => array("pipe", "r"),		// stdin 
		1 => array("pipe", "w"),		// stdout
		2 => array("file", "/dev/null", "w")	// stderr -- dump to /dev/null
	);

	$cmd = "$GREP 'No such command' | $MBUFFER -q -Q -m 1M";

	$process = proc_open($cmd, $descriptorspec, $pipes);

	fwrite($pipes[0], $AMI);
	fclose($pipes[0]);

	$G = stream_get_contents($pipes[1]);
	fclose($pipes[1]);

	proc_close($process);

	if (strlen ("$G") < 14) {				// echolink commands found...


		$text = "EchoLink Callsign Search for: \"$lookup\"  ";
		$i = strlen ($text);
		$dashes = substr("--------------------------------------------------------------------------------------------", 0, 80 - $i);
		echo "<tr><td colspan=5><pre>\n\n$text$dashes\n</td></tr>";

		$cmd = "$AWK '-F|' 'BEGIN{IGNORECASE=1} $2 ~ /$lookup/ {printf (\"%s\x18\", $0);}' | $MBUFFER -q -Q -m 1M";

		$process = proc_open($cmd, $descriptorspec, $pipes);

		fwrite($pipes[0], $AMI);
		fclose($pipes[0]);

		$res = stream_get_contents($pipes[1]);
		fclose($pipes[1]);

		proc_close($process);

		//echo "<!-- echodb: $AMI -->\n";
		
		process_echolink_result ($res);

	}


}


function do_echolink_number_search($fp, $echonode) {

	global $AWK, $GREP, $MBUFFER;

	$lookup = (int)substr("$echonode", 1);  // Strips off the leading "3" and leading zeros.

	$AMI = AMIcommand ($fp, "echolink dbdump");		// Retrieve echolink data if available

	$descriptorspec = array(
		0 => array("pipe", "r"),		// stdin 
		1 => array("pipe", "w"),		// stdout
		2 => array("file", "/dev/null", "w")	// stderr -- dump to /dev/null
	);

	$cmd = "$GREP 'No such command' | $MBUFFER -q -Q -m 1M";

	$process = proc_open($cmd, $descriptorspec, $pipes);

	fwrite($pipes[0], $AMI);
	fclose($pipes[0]);

	$G = stream_get_contents($pipes[1]);
	fclose($pipes[1]);

	proc_close($process);

	if (strlen ("$G") < 14) {				// echolink commands found...

		$text = "EchoLink Node Number Search for: \"$lookup\"  ";
		$i = strlen ($text);
		$dashes = substr("--------------------------------------------------------------------------------------------", 0, 80 - $i);
		echo "<tr><td colspan=5><pre>\n\n$text$dashes\n</td></tr>";

		$cmd = "$AWK '-F|' 'BEGIN{IGNORECASE=1} $1 ~ /$lookup/ {printf (\"%s\x18\", $0);}' | $MBUFFER -q -Q -m 1M";

		$process = proc_open($cmd, $descriptorspec, $pipes);

		fwrite($pipes[0], $AMI);
		fclose($pipes[0]);

		$res = stream_get_contents($pipes[1]);
		fclose($pipes[1]);

		proc_close($process);
		//echo "<!-- echodb: $res -->\n";

		process_echolink_result ($res);

	}

}

function process_echolink_result ($res) {

	if ("$res" == "") {
		echo "<tr><td colspan=5><pre>\n\n....Nothing Found....\n</td></tr>";
		return;
	}

	$table = explode ("\x18", $res);
	array_pop ($table);

	foreach ($table as $row) {
		echo "<tr>";

		$column = explode ("|", $row);
		$node = trim($column[0]);
		$call = trim($column[1]);
		$ipaddr = trim($column[2]);

		echo "<td colspan=2><pre>$node</pre></td> <td colspan=2><pre>$call</pre></td> <td colspan=1><pre>$ipaddr</pre></td>";
		echo "</tr>\n";
	}
}


function do_irlp_callsign_search($lookup) {

	global $IRLP_CALLS, $IRLP, $ZCAT, $AWK;

	if ($IRLP) {			// See common.inc

		$text = "IRLP Callsign Search for: \"$lookup\"  ";
		$i = strlen ($text);
		$dashes = substr("--------------------------------------------------------------------------------------------", 0, 80 - $i);
		echo "<tr><td colspan=5><pre>\n\n$text$dashes\n</td></tr>";

		$res = `$ZCAT $IRLP_CALLS | $AWK '-F|' 'BEGIN{IGNORECASE=1} $2 ~ /$lookup/ {printf ("%s\x18", $0);}'`;
		//echo "<!-- irlpdb: $res -->\n";

		process_irlp_result ($res);

	}
}


function do_irlp_number_search($irlpnode) {

	global $IRLP_CALLS, $IRLP, $ZCAT, $AWK;

	if ($IRLP) {			// See common.inc

		$lookup = (int)substr("$irlpnode", 1);  // Strips off the leading "8" and leading zeros.

		$text = "IRLP Node Number Search for: \"$lookup\"  ";
		$i = strlen ($text);
		$dashes = substr("--------------------------------------------------------------------------------------------", 0, 80 - $i);
		echo "<tr><td colspan=5><pre>\n\n$text$dashes\n</td></tr>";

		$res = `$ZCAT $IRLP_CALLS | $AWK '-F|' 'BEGIN{IGNORECASE=1} $1 ~ /$lookup/ {printf ("%s\x18", $0);}'`;
		//echo "<!-- irlpdb: $res -->\n";

		process_irlp_result ($res);

	}
}

function process_irlp_result ($res) {

	if ("$res" == "") {
		echo "<tr><td colspan=5><pre>\n\n....Nothing Found....\n</td></tr>";
		return;
	}

	$table = explode ("\x18", $res);
	array_pop ($table);

	foreach ($table as $row) {
		echo "<tr>";

		$column = explode ("|", $row);
		$node = trim($column[0]);
		$call = trim($column[1]);

		$qth = trim ($column[2] . ", " . $column[3] . " " . $column[4]);

		echo "<td colspan=2><pre>$node</pre></td> <td colspan=2><pre>$call</pre></td> <td colspan=1><pre>$qth</pre></td>";
		echo "</tr>\n";
	}
}


?>


