<?php
include("session.inc");

// Author: Paul Aidukas KN2R (Copyright) July 15, 2013
// For ham radio use only, NOT for comercial use!
// Be sure to allow popups from your Allmon web server to your browser!!

if ($_SESSION['sm61loggedin'] !== true) {
    die ("<br><h3>ERROR: You Must login to use the 'Bubble Chart' function!</h3>");
}

$node = @trim(strip_tags($_POST['node']));
$localnode = @trim(strip_tags($_POST['localnode']));

if ($node == '') {
    $url = "<script>window.open('http://stats.allstarlink.org/getstatus.cgi\?$localnode')</script>";
    echo $url;
} else {
    print "<b>Opening Bubble Chart for node $node</b>";
    $url = "<script>window.open('http://stats.allstarlink.org/getstatus.cgi\?$node')</script>";
    echo $url;
}

?>
