<?php
include("session.inc");
include("global.inc");
include("common.inc");

// Author: Paul Aidukas KN2R (Copyright) July 15, 2013
// For ham radio use only, NOT for comercial use!
// Be sure to allow popups from your Allmon web server to your browser!!
// Major update by KB4FXC 02/03/2018

?>
<html>
<head>
<title>IRLP messages Log</title>
</head>
<body>
<pre>
<?php
	if ($_SESSION['sm61loggedin'] === true) {
		$file = $IRLP_LOG;			// Defined in global.inc
                echo "File: $file\n-----------------------------------------------------------------\n";
		if (file_exists ("$file"))
			echo file_get_contents($file);
		else
			echo "\n\nIRLP Log is not available.\n";
	} else
		echo ("<br><h3>ERROR: You Must login to use this function!</h3>");
?>
</pre>
</body>
</html>
