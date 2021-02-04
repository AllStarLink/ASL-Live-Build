<?php
include("session.inc");
include("global.inc");
include("common.inc");

if ($_SESSION['sm61loggedin'] !== true) {
    die ("<br><h3>ERROR: You Must login to use the 'Control Panel' function!</h3>");
}

$node = @trim(strip_tags($_GET['node']));
$localnode = @trim(strip_tags($_POST['localnode']));

if ($localnode !== '') {
    $node = $localnode;
}

if (!is_numeric($node)) {
    die ("Please provide a properly formated URI. (ie controlpanel.php?node=1234)");
}

$title = "AllStar $node Control Panel";
    
if ($_SESSION['sm61loggedin'] === true) {
    // Read allmon INI file
    if (!file_exists('allmon.ini')) {
        die("Couldn't load file allmon.ini.\n");
    }
    $allmonConfig = parse_ini_file('allmon.ini', true);
    
    // Read cintrolpanel INI file
    if (!file_exists('controlpanel.ini')) {
        die("Couldn't load file controlpanel.ini.\n");
    }
    $cpConfig = parse_ini_file('controlpanel.ini', true);
    
    //combine [general] stanza with this node's stanza
    $cpCommands = $cpConfig['general'];
    if (isset($cpConfig[$node])) {
        foreach ($cpConfig[$node] as $type => $arr) {
            if ($type == 'labels') {
                foreach($arr as $label) {
                    $cpCommands['labels'][] = $label;
                }
            } elseif ($type == 'cmds') {
                foreach($arr as $cmd) {
                    $cpCommands['cmds'][] = $cmd;
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
<meta name="description" content="AllStar Control Panel">
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
<div id="header4Tag"><i><?php echo $title ?></i></div>
<div id="header2Tag"><i><?php echo $TITLE3; ?></i></div>
<div id="headerImg"><a href="http://allstarlink.org" target="_blank"><img src="allstarlink.jpg" alt="Allstar Logo"></a></div>
</div>
<br>
<div id="cpMain">
Control command (select one): <select name="cpSelection" class="submit" id="cpSelect">
<?php 
for($i=0; $i < count($cpCommands['labels']); $i++) {
    print "<option value=\"" . $cpCommands['cmds'][$i] . "\">" . $cpCommands['labels'][$i] . "</option>\n";
}
?>
</select>
<input type="hidden" id="localnode" value="<?php echo $node ?>">
<input type="button" class="submit" value="Execute" id="cpExecute">
<br/><br>
<div id="cpResult">
    <!-- Results shown here -->
</div>
</div>
<br>
<?php include "footer.inc"; ?>
