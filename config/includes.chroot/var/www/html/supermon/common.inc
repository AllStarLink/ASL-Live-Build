<?php
//
//	System specific parameters...
//	KB4FXC 02/2018
//
//	NOTE: DO NOT MODIFY!
//	THIS FILE WILL BE OVERWRITTEN BY PACKAGE UPDATES!
//
// Title in header when logged in
$TITLE_LOGGED = "Supermon 6.1+ AllStar Manager";
//
// Title in header when not logged in
$TITLE_NOT_LOGGED = "Supermon 6.1+ AllStar Monitor";
//
// Webserver access log
$WEB_ACCESS_LOG = "/var/log/httpd/access_log";
//
// Webserver error log
$WEB_ERROR_LOG = "/var/log/httpd/error_log";
//
// Asterisk message log
$ASTERISK_LOG = "/var/log/asterisk/messages";
//
// AllStar allmon database
$ASTDB_TXT = "/var/log/asterisk/astdb.txt";
//
// AllStar rpt_extnodes file --- not present in DNS mode.
$EXTNODES = "/tmp/rpt_extnodes";
//
// AllStar Echolink database dump
$ECHO_DB = "/tmp/echolink.db.gz";
//
// Private AllStar nodelist
$PRIVATENODES = "/etc/asterisk/local/privatenodes.txt";
//
//	IRLP related...
$IRLP_CALLS = "/tmp/irlpdata.txt.gz";
$IRLP_LOG = "/home/irlp/log/messages";
//
// Make sure this is hamvoip "Vx.xrc" code
// $version="`head -2 /etc/allstar_version`;
$version=`export TERM=vt100 && sudo asterisk -rx "core show version" |awk '{print $2}'`;
$version="Asterisk $version";
//echo "<p>$version</p>";
$pattern="/ [0-9].[0-9]rc[0-9]/";

if (preg_match($pattern, $version, $match)) {
	$system_type = "hamvoip";
} else {
	// Add other types if needed here
	// $system_type="";
	$system_type = "asl";
}
//
//	Paths to various programs called from php...
$ASTERISK =	"/usr/bin/asterisk";
$AWK =		"/bin/awk";
$CAT =		"/bin/cat";
$CUT =		"/bin/cut";
$DATE =		"/bin/date";
$EGREP =	"/bin/egrep";
$GREP =		"/bin/grep";
$HEAD =		"/bin/head";
$HOSTNAME =	"/bin/hostname";
$IFCONFIG =	"/bin/ifconfig";
$JOURNALCTL =	"/usr/bin/journalctl";
$MBUFFER =	"/usr/local/bin/mbuffer";
$SED =		"/bin/sed";
$SUDO =		"export TERM=vt100 && /usr/bin/sudo";
$TAIL =		"/bin/tail";
$UPTIME =	"/bin/uptime";
$WGET =		"/bin/wget";
$ZCAT =		"/bin/zcat";

if (file_exists($IRLP_LOG))
	$IRLPLOG = true;
else
	$IRLPLOG = false;

if (file_exists($IRLP_CALLS))
	$IRLP = true;
else
	$IRLP = false;

if (file_exists($EXTNODES))
	$EXTN = true;
else
	$EXTN = false;
?>
