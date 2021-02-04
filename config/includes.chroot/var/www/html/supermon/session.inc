<?php
	// New php session management code -- KB4FXC 01/25/2018
	session_start([ 'name' => "supermon61" ]);
	if (!array_key_exists("sm61loggedin", $_SESSION))
		     $_SESSION["sm61loggedin"] = false;

?>
