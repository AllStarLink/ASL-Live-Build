<?php
	// New php session management code -- KB4FXC 01/25/2018
	include("session.inc");

	session_unset();
	$_SESSION['sm61loggedin'] = false;

	//`echo logout called >> /tmp/log.out`;


	print "Logged out.";
?>
