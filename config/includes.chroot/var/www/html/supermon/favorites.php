<?php
include("session.inc");
//
// Modified for SuperMon: Paul Aidukas KN2R
//
include("global.inc");
include("common.inc");

if ($_SESSION['sm61loggedin'] !== true) {
    die ("<br><h3>ERROR: You Must login to use the 'Favorites Panel' function!</h3>");
}
$node = @trim(strip_tags($_GET['node']));
if (!is_numeric($node)) {
    die ("Please provide a properly formated URI. (ie favorites.php?node=1234)");
}
$title = "AllStar $node Favorites Panel";
if ($_SESSION['sm61loggedin'] === true) {
    // Read controlpanel INI file
    if (!file_exists('favorites.ini')) {
        die("Couldn't load file favorites.ini.\n");
    }
    $cpConfig = parse_ini_file('favorites.ini', true);
    
    //combine [general] stanza with this node's stanza
    $cpCommands = $cpConfig['general'];
    if (isset($cpConfig[$node])) {
        foreach ($cpConfig[$node] as $type => $arr) {
            if ($type == 'label') {
                foreach($arr as $label) {
                    $cpCommands['label'][] = $label;
                }
            } elseif ($type == 'cmd') {
                foreach($arr as $cmd) {
                    $cpCommands['cmd'][] = $cmd;
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="generator" content="By hand with a text editor">
<meta name="description" content="SuperMon Favorites Panel">
<meta name="keywords" content="allstar monitor, app_rpt, asterisk">
<meta name="author" content="Tim Sawyer, WD6AWP">
<meta name="mods" content="New features, IRLP capability, Paul Aidukas, KN2R">
<link type="text/css" rel="stylesheet" href="supermon.css">
<link type="text/css" rel="stylesheet" href="jquery-ui.css">
<script src="jquery.min.js"></script>
<script src="jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
<?php if ($_SESSION['sm61loggedin'] !== true) { ?>

        alert ('Must login to use the Control Panel.');

<?php } else { ?>

        // css hides
        $("#cpMain").show();

<?php } ?>

    
    // When Ok is clicked
    $('#cpExecute').click(function() {
        var localNode = $('#localnode').val();
        var cpCommand = $('#cpSelect').val();
        
        // Do Ajax get
        $.get('controlserver.php?node=' + localNode + '&cmd=' + cpCommand, function( data ) {
            $('#cpResult').html( data );
        });
    });
});    
</script>
</head>
<body>
<div id="header" style="background-image: url(<?php echo $BACKGROUND; ?>); background-color:<?php echo $BACKGROUND_COLOR; ?>; height: <?php echo $BACKGROUND_HEIGHT; ?> ">
<div id="headerTitle"><i><?php echo "$CALL - $TITLE_LOGGED"; ?></i></div>
<div id="header3Tag"><i><?php echo $title ?></i></div>
<div id="header2Tag"><i><?php echo $TITLE3 ?></i></div>
<div id="headerImg"><a href="http://allstarlink.org" target="_blank"><img src="allstarlink.jpg" style="border: 0px;" alt="AllStar Logo"></a></div>
</div>
<div id="cpMain">
<br>
Favorite (select one): <select class="submit" name="cpSelection" id="cpSelect">
<?php 
for($i=0; $i < count($cpCommands['label']); $i++) {
    print "<option value=\"" . $cpCommands['cmd'][$i] . "\">" . $cpCommands['label'][$i] . "</option>\n";
}
?>
</select>
<input type="hidden" id="localnode" value="<?php echo $node ?>">
<input type="button" class="submit" value="Connect" id="cpExecute">
<br/><br>
<div id="cpResult">
    <!-- Results shown here -->
</div>
</div>
<br>
<?php include "footer.inc"; ?>

