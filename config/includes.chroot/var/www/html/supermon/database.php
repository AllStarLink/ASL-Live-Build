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
<title>AllStar database.txt file contents</title>
</head>
<body style="background-color:powderblue;">
<pre>
<?php
	if ($_SESSION['sm61loggedin'] === true) {
                $statcmd = "/bin/date > $DATABASE_TXT";
                exec($statcmd);

                $statcmd = "echo >> $DATABASE_TXT";
                exec($statcmd);

                $statcmd = "export TERM=vt100 && /bin/sudo /usr/sbin/asterisk -rx 'database show' >> $DATABASE_TXT";
                exec($statcmd);

		$file = $DATABASE_TXT;			// Defined in global.inc
		echo "File: $file\n-----------------------------------------------------------------\n";
		echo file_get_contents($file);
	} else
		echo ("<br><h3>ERROR: You Must login to use this function!</h3>");
?>
</pre>
</body>
</html>
