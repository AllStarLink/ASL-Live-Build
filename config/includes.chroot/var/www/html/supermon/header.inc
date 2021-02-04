<?php
include("global.inc");
include("common.inc");

// Set title
$var1 = explode('/', $_SERVER['REQUEST_URI']);
$var2 = array_pop($var1);
$uri = urldecode($var2);

// ADDED KN2R - 10-2018
if (! empty($SMSERVERNAME)) {
    $pageTitle = $SMSERVERNAME . " | Supermon | ";
// END KN2R

} else {
    $pageTitle = strtoupper($_SERVER['SERVER_NAME']) . " | Supermon | "; 
}
if (preg_match("/voter\.php\?node=(\d+)$/", $uri, $matches)) {
    $pageTitle .= "RTCM " . $matches[1];
} elseif (preg_match("/link\.php\?nodes=(.+)$/", $uri, $matches)) {
    $pageTitle .= $matches[1];
} elseif (preg_match("/about/", $uri, $matches)) {
    $pageTitle .= "" . ucfirst($matches[0]);
}

$home = '/';
if (! empty($var1[1])) {
        $home="/" . $var1[1];
}

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $pageTitle; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
    if (! empty($REFRESH_DELAY)) {
        print "<meta http-equiv='REFRESH' content='$REFRESH_DELAY'; url='.$uri'>";
    } else {
        print "<meta http-equiv='REFRESH' content='21600; url='.$uri'>";
    }
?>
<meta name="generator" content="By hand with vi text editor">
<meta name="description" content="AllStar node manager">
<meta name="keywords" content="allstar monitor, app_rpt, asterisk">
<meta name="robots" content="noindex, nofollow">
<meta name="author" content="Tim Sawyer, WD6AWP">
<meta name="mods" content="New features, IRLP capability, Paul Aidukas, KN2R">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link type="text/css" rel="stylesheet" href="supermon.css">
<link type="text/css" rel="stylesheet" href="jquery-ui.css">
<script src="jquery.min.js"></script>
<script src="jquery-ui.min.js"></script>
<script type="text/javascript">
// when DOM is ready
//

$(document).ready(function() {

    //Set to hide Login Link
    hideLoginLink = false
    
    // Hide login link on page load?
    if (hideLoginLink) {
        $('#loginlink').hide();
    }

<?php if ($_SESSION['sm61loggedin'] === true) { ?>

        $('#loginlink').hide();

<?php } else { ?>

        $('#connect_form').hide();
        $('#logoutlink').hide();

<?php } ?>

    
    // Login dialog
    $("#login").dialog( {
        autoOpen: false,
        title: 'Manager Login',
        modal: true,
       open: function() {
           // On open, hide the original submit button
           $( this ).find( "[type=submit]" ).hide();
       },
        buttons: [ { 
           text: "Login",
           type: "submit",
           form: "login",
           click: function() {
               var user = $('form input:text').val();
               var passwd = $('input:password').val();
               $(this).dialog("close"); 
               $('#test_area').load("login.php", { 'user' : user, 'passwd' : passwd }, function(response) {
                   if (response.substr(0,5) != 'Sorry') {
			window.location.reload();


                   }
               });
               $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(1000).fadeOut(2000);
           }
        } ]
     });

     // make enter key submit login form
     $('#login').on('keyup', function(e){
       if (e.keyCode == 13) {
         $(':button:contains("Login")').click();
       }
     });
     
    // Login dialog opener
    $("#loginlink").click(function() {
        $("#login").dialog('open');
        return false;
    });
    
<?php if ($_SESSION['sm61loggedin'] === true) { ?>

    // Logout 
    $('#logoutlink').click(function(event) {
	$('#test_area').load("logout.php", "", function(response) {
		if (response.substr(0,5) != 'Sorry') {
			window.location.reload();
		}
	});
        event.preventDefault();
    });

    // Ajax function a link
    $('#connect, #monitor, #permanent, #localmonitor').click(function() {
        var button = this.id;    // which button was pushed
        var localNode = $('#localnode').val();
        var remoteNode = $('#node').val(); 
        var perm = $('input:checkbox:checked').val();
            if (remoteNode.length == 0) {
                  alert('Please enter the remote node number.');
                  return;
            }
            $.ajax( { url:'connect.php', data: { 'remotenode' : remoteNode, 'perm' : perm, 'button' : button, 'localnode' : localNode }, type:'post', success: function(result) {
                $('#test_area').html(result);
                $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(1000).fadeOut(2000);
            }
        });
    });
    $('#disconnect').click(function() {
        var button = this.id;    // which button was pushed
        var localNode = $('#localnode').val();
        var remoteNode = $('#node').val();
        var perm = $('input:checkbox:checked').val();
            if (remoteNode.length == 0) {
                  alert('Please enter the remote node number.');
                  return;
            }
            r = confirm("Disconnect " + remoteNode + " from " + localNode + "?");
            if (r !== true) {
                return;
            }
            $.ajax( { url:'disconnect.php', data: { 'remotenode' : remoteNode, 'perm' : perm, 'button' : button, 'localnode' : localNode }, type:'post', success: function(result) {
                $('#test_area').html(result);
                $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(1000).fadeOut(2000);
            }
        });
    });

    $('#controlpanel').click(function (event) {
        var url = "controlpanel.php?node=" + $('#localnode').val();
        var windowName = "ControlPanel" + $('#localnode').val();
        var windowSize = 'height=560, width=1000';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
      
    //
    // Add-on routines by Paul Aidukas KN2R for Supermon:
    //
    $('#favoritespanel').click(function (event) {
        var url = "favorites.php?node=" + $('#localnode').val();
        var windowName = "FavoritesPanel" + $('#localnode').val();
        var windowSize = 'height=500, width=800';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#astlog').click(function(event) {
        var url = "astlog.php";
        var windowName = "AsteriskLog" + $('#localnode').val();
        var windowSize = 'height=560, width=1100';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#clog').click(function() {
        var url = "connectlog.php";
        var windowName = "ConnectionLog" + $('#localnode').val();
        var windowSize = 'height=560, width=1120';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#stats').click(function() {
        var url = "stats.php?node=" + $('#localnode').val();
        var windowName = "AllStarStatistics" + $('#localnode').val();
        var windowSize = 'height=560, width=1140';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#astreload').click(function() {
        var button = this.id;
        var node = $('#node').val();

        r = confirm("Execute the Asterisk \"Iax2, Rpt, & Dialplan Reload\" commands?");
        if (r !== true) {
            return;
        }
        $.ajax( { url:'ast_reload.php', data: { 'node' : node, 'button' : button }, type:'post', success: function(result) {
                $('#test_area').html(result);
                $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(2000).fadeOut(2000);
            }
        });
    });
    $('#reboot').click(function() {
        var button = this.id;
        var node = $('#node').val();

        r = confirm("Perform a full Reboot of the AllStar server?");
        if (r !== true) {
            return;
        }
        $.ajax( { url:'reboot.php', data: { 'node' : node, 'button' : button }, type:'post', success: function(result) {
                $('#test_area').html(result);
                $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(8000).fadeOut(2000);
            }
        });
    });
    $('#cpustats').click(function() {
        var url = "cpustats.php";
        var windowName = "CPUstatistics" + $('#localnode').val();
        var windowSize = 'height=760, width=680';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#database').click(function() {
        var url = "database.php";
        var windowName = "Database" + $('#localnode').val();
        var windowSize = 'height=560, width=850';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#astaron, #astaroff').click(function() {
        var button = this.id;
        if (button == 'astaroff') {
            r = confirm("Perform Shutdown of AllStar system software?");
            if (r !== true) {
                return;
            }
        }
        if (button == 'astaron') {
            r = confirm("Perform Startup of AllStar system software?");
            if (r !== true) {
                return;
            }
        }
        $.ajax( { url:'astaronoff.php', data: { 'button' : button }, type:'post', success: function(result) {
                $('#test_area').html(result);
                $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(2000).fadeOut(2000);
            }
        });
    });
    $('#dtmf').click(function() {
        var button = this.id;
        var node = $('#node').val();
        var localnode = $('#localnode').val();

        if (node.length == 0) {
              alert('Please enter a DTMF command to execute on node ' + localnode + '.');
              return;
        }

        $.ajax( { url:'dtmf.php', data: { 'node' : node, 'button' : button, 'localnode' : localnode }, type:'post', success: function(result) {
                $('#test_area').html(result);
                $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(1000).fadeOut(2000);
            }
        });
    });
    $('#rptstats').click(function() {
        var node = $('#node').val();
        var localnode = $('#localnode').val();
        var url = "rptstats.php?node=" + node + "&localnode=" + localnode;
        var windowName = "RptStatistics" + $('#localnode').val();
        var windowSize = 'height=700, width=800';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#fastrestart').click(function() {
        var button = this.id;
        var localnode = $('#localnode').val();

        r = confirm("Perform a Fast-Restart of the AllStar system software?");
        if (r !== true) {
            return;
        }

        $.ajax( { url:'fastrestart.php', data: { 'button' : button, 'localnode' : localnode }, type:'post', success: function(result) {
                $('#test_area').html(result);
                $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(4000).fadeOut(3000);
            }
        });
    });
    $('#astlookup').click(function() {
        var button = this.id;
        var node = $('#node').val();
        var perm = $('input:checkbox:checked').val();
        var localnode = $('#localnode').val();

        if (node.length == 0) {
              alert('Please enter a Callsign or Node number to look up.');
              return;
        }
        var url = "astlookup.php?node=" + node + "&localnode=" + localnode + "&perm=" + perm;
        var windowName = "AstLookup" + $('#localnode').val();
        var windowSize = 'height=500, width=900';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#map').click(function() {
        var button = this.id;
        var node = $('#node').val();
        var localnode = $('#localnode').val();
        $.ajax( { url:'bubblechart.php', data: { 'node' : node, 'localnode' : localnode, 'button' : button }, type:'post', success: function(result) {
                $('#test_area').html(result);
                $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(1000).fadeOut(2000);
            }
        });
    });
    $('#astnodes').click(function() {
        var url = "astnodes.php";
        var windowName = "AstNodes" + $('#localnode').val();
        var windowSize = 'height=560, width=850';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#extnodes').click(function() {
        var url = "extnodes.php";
        var windowName = "ExtNodes" + $('#localnode').val();
        var windowSize = 'height=560, width=850';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#linuxlog').click(function() {
        var url = "linuxlog.php";
        var windowName = "LinuxLog" + $('#localnode').val();
        var windowSize = 'height=560, width=1000';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#irlplog').click(function() {
        var url = "irlplog.php";
        var windowName = "IRLPLog" + $('#localnode').val();
        var windowSize = 'height=560, width=900';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#webacclog').click(function() {
        var url = "webacclog.php";
        var windowName = "WebAccessLog"+ $('#localnode').val();
        var windowSize = 'height=560, width=1000';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#weberrlog').click(function() {
        var url = "weberrlog.php";
        var windowName = "WebErrorLog" + $('#localnode').val();
        var windowSize = 'height=560, width=1000';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });
    $('#openpigpio').click(function() {
        var url = "pi-gpio.php";
        var windowName = "Pi-GPIO" + $('#localnode').val();
        var windowSize = 'height=760, width=725';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });

 $('#openbanallow').click(function() {
        var url = "node-ban-allow-intro.php";
        var windowName = "Ban-Allow" + $('#localnode').val();
        var windowSize = 'height=700, width=750';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });

$('#openupdate').click(function() {
        var url = "hamvoip-update.php";
        var windowName = "SW-Update" + $('#localnode').val();
        var windowSize = 'height=600, width=750';
        window.open(url, windowName, windowSize);
        event.preventDefault();
    });


<?php } ?>

    // Click on a cell to populate the input form
    $('table').on('click', 'td', function( event ) {
          // Shows the table ID, the text of the cell, the class of the cell and the ID of the cell.
          //console.log('clicked:', $( this ).closest('table').attr('id'), $( this ).text(), $( this ).attr('class'), $( this ).attr('id'));
          
          // shows x and y coordinates of clicked cell
          //console.log('coordinates:', 'y=' + this.cellIndex, 'x=' + this.parentNode.rowIndex);
          
          if ( $( this ).attr('class') == 'nodeNum') {
                // Put node number into id="node"
                $('#connect_form #node').val($( this ).text());
                
                  // split table ID and put node into id="localnode"
                var idarr = $( this ).closest('table').attr('id').split('_');
                $('#connect_form #localnode').val(idarr[1]);
          }  
    });
    
    // Uncomment this block to allow shift+h to show login dialog.  
    $(document).keypress(function(event) {
        if (hideLoginLink) {
            var checkWebkitandIE=(event.which==72 ? 1 : 0);
            var checkMoz=(event.which==104 && event.shiftKey ? 1 : 0);

<?php if ($_SESSION['sm61loggedin'] !== true) { ?>

            if (checkWebkitandIE || checkMoz) {
                $("#login").dialog('open');
                return false;
            }

<?php } ?>

        }
      
    });

});
</script>
</head>
<body>
<div id="header" style="background-image: url(<?php echo $BACKGROUND; ?>); background-color: <?php echo $BACKGROUND_COLOR; ?>; height: <?php echo $BACKGROUND_HEIGHT; ?> ">

<?php
if ($_SESSION['sm61loggedin'] === true) {
   print "<div id=\"headerTitle\"><a href=\"index.php\"><i>$CALL - $TITLE_LOGGED</i></a></div>";
} else {
   print "<div id=\"headerTitle\"><a href=\"index.php\"><i>$CALL - $TITLE_NOT_LOGGED</i></a></div>";
}
?>
<div id="headerTag"><i><?php echo $LOCATION; ?><br><?php echo $TITLE2; ?></i></div>
<?php
if ($_SESSION['sm61loggedin'] === true) {
   print "<div id=\"header2Tag\"><i>$TITLE3</i></div>";
}
?>

<!-- Login opener -->
<div id="headerLogin">
<a href="#" id="logoutlink">Logout</a>
</div>
<div id="headerLogin">
<a href="#" id="loginlink">Login</a>
</div>
<div id="headerImg"><a href="https://allstarlink.org" target="_blank"><img src="allstarlink.jpg" style="border: 0px;" alt="Allstar Logo"></a></div>
</div>
<div class="clearer"></div>
<!-- Login form -->
<div id="login">
<div>
<form method="post" action="">
<table>
<tr><td>Username:</td><td><input style="width: 150px;" type="text" name="user" autocapitalize="none"></td></tr>
<tr><td>Password:</td><td><input style="width: 150px;" type="password" name="password"><input type="submit" name="dummy" ></td></tr>
</table>
</form>
</div>
</div>
<?php include "menu.inc" ?>
<!-- Command response area -->
<div id="test_area"></div>

