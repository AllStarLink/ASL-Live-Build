<?php
include('session.inc');
include('amifunctions.inc');

if ($_SESSION['sm61loggedin'] !== true) {
        die("Please login to use connect/disconnect functions.\n");
}

// Filter and validate user input
$remotenode = @trim(strip_tags($_POST['remotenode']));
$perm = @trim(strip_tags($_POST['perm']));
$button = @trim(strip_tags($_POST['button']));
$localnode = @trim(strip_tags($_POST['localnode']));

//if (! preg_match("/^\d+$/",$remotenode)) {
//    die("Please provide remote node number.\n");
//}

if (! preg_match("/^\d+$/",$localnode)) {
    die("Please provide local node number.\n");
}

// Read configuration file
if (!file_exists('allmon.ini')) {
    die("Couldn't load ini file.\n");
}
$config = parse_ini_file('allmon.ini', true);
#print "<pre>"; print_r($config); print "</pre>";

// Open a socket to Asterisk Manager
$fp = AMIconnect($config[$localnode]['host']);
if (FALSE === $fp) {
	die("Could not connect.\n\n");
}
if (FALSE === AMIlogin($fp, $config[$localnode]['user'], $config[$localnode]['passwd'])) {
	die("Could not login.");
}

// Which ilink command?
if ($button == 'connect') {
    if ($perm == 'on') {
        $ilink = 13;
        print "<b>Permanently Connecting $localnode to $remotenode</b>";
    } else {
        $ilink = 3;
        print "<b>Connecting $localnode to $remotenode</b>";
    }
} elseif ($button == 'monitor') {
    if ($perm == 'on') {
        $ilink = 12;
        print "<b>Permanently Monitoring $remotenode from $localnode</b>";
    } else {
        $ilink = 2;
        print "<b>Monitoring $remotenode from $localnode</b>";
    }
} elseif ($button == 'localmonitor') {
    if ($perm == 'on') {
        $ilink = 18;
        print "<b>Permanently Local Monitoring $remotenode from $localnode</b>";
    } else {
        $ilink = 8;
        print "<b>Local Monitoring $remotenode from $localnode</b>";
    }
} elseif ($button == 'disconnect') {
    if ($perm == 'on') {
        $ilink = 11;
        print "<b>Permanently Disconnect $remotenode from $localnode</b>";
    } else {
        $ilink = 11;
        print "<b>Still Permanently Disconnect $remotenode from $localnode</b>";
    }
}

#exit;

 $AMI1 = AMIcommand ($fp, "rpt cmd $localnode ilink $ilink $remotenode");


?>
