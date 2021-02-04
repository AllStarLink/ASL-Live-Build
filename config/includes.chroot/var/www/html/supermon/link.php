<?php 

// New features, IRLP capability, Paul Aidukas KN2R (Copyright) July 15, 2013-2017
// For ham radio use only, NOT for comercial use!

include("session.inc");
include("global.inc");
include("common.inc");
include "header.inc";
//error_reporting(E_ALL);
$parms = @trim(strip_tags($_GET['nodes']));
$passedNodes = explode(',', @trim(strip_tags($_GET['nodes'])));
#print_r($nodes);

if (count($passedNodes) == 0) {
    die ("Please provide a properly formated URI. (ie link.php?nodes=1234 | link.php?nodes=1234,2345)");
}

// Added WA3DSP

if (isset($_COOKIE['display-data'])) {
    foreach ($_COOKIE['display-data'] as $name => $value) {
        $name = htmlspecialchars($name);
        switch ($name) {
            case "number-displayed";
               $Displayed_Nodes = htmlspecialchars($value);
               break;
            case "show-number";
               $Display_Count = htmlspecialchars($value);
               break;
            case "show-all";
               $Show_All = htmlspecialchars($value);
               break;
        }
       // echo "$name : $value <br />\n";
    }
}

// If not defined in cookie display all nodes
if (! isset($Displayed_Nodes)) {
    $Displayed_Nodes="999";
} elseif
    ($Displayed_Nodes === "0") {
         $Displayed_Nodes="999"; 
}

// If not define in cookie display all
if (! isset($Display_Count)) {
  $Display_Count=0;
}

// If not defined in cookie show all else omit "Never" Keyed 
if ( ! isset($Show_All)) {
  $Show_All="1";
}

// END WA3DSP

// Get Allstar database file
$db = $ASTDB_TXT;			// Defined in global.inc
$astdb = array();
if (file_exists($db)) {
    $fh = fopen($db, "r");
    if (flock($fh, LOCK_SH)){
        while(($line = fgets($fh)) !== FALSE) {
            $arr = preg_split("/\|/", trim($line));
            $astdb[$arr[0]] = $arr;
        }
    }
    flock($fh, LOCK_UN);
    fclose($fh);
    #print "<pre>"; print_r($astdb); print "</pre>";
}

// Read supermon INI file
if (!file_exists('allmon.ini')) {
    die("Couldn't load allmon ini file.\n");
}
$config = parse_ini_file('allmon.ini', true);
#print "<pre>"; print_r($config); print "</pre>";
#print "<pre>"; print_r($config[$node]); print "</pre>";

// Remove nodes not in our allmon.ini file.
$nodes=array();
foreach ($passedNodes as $i => $node) {
	if (isset($config[$node])) {
		$nodes[] = $node;
	} else {
		print "Warning: Node $node not found in our allmon ini file.";
	}
}

?>
<script type="text/javascript">
// when DOM is ready

$(document).ready(function() {
  if(typeof(EventSource)!=="undefined") {
		
    // Start SSE 
    var source=new EventSource("server.php?nodes=<?php echo $parms; ?>");
        
    // Fires when node data come in. Updates the whole table
    source.addEventListener('nodes', function(event) {
    //console.log('nodes: ' + event.data);	        
    // server.php returns a json formated string

    var tabledata = JSON.parse(event.data);
    for (var localNode in tabledata) {
        var tablehtml = '';

        // Added WA3DSP
        var total_nodes = 0;
        var shown_nodes = 0;
        var ndisp = <?php echo (int) $Displayed_Nodes ?>;
	ndisp++;
        var sdisp = <?php echo $Display_Count ?>;
        var sall = <?php echo $Show_All ?>;
        // END

	// KB4FXC -- refactored telemetry state logic 10/21/2018

	var cos_keyed = 0;
	var tx_keyed = 0;

        for (row in tabledata[localNode].remote_nodes) {

               if (tabledata[localNode].remote_nodes[row].cos_keyed == 1)
			cos_keyed = 1;

		if (tabledata[localNode].remote_nodes[row].tx_keyed == 1)
			tx_keyed = 1;

	}
        
        if (cos_keyed == 0) { 
                if (tx_keyed == 0)
                        tablehtml += '<tr class="gColor"><td colspan="1" align="center">' + localNode + '</td><td colspan="1" align="center">Idle</td><td colspan="5"></td></tr>';
                else
                        tablehtml += '<tr class="tColor"><td colspan="1" align="center">' + localNode + '</td><td colspan="1" align="center">PTT-Keyed</td><td colspan="5"></td></tr>';
        }
        else {
                if (tx_keyed == 0)
                        tablehtml += '<tr class="lColor"><td colspan="1" align="center">' + localNode + '</td><td colspan="1" align="center">COS-Detected</td><td colspan="5"></td></tr>';
                else
                        tablehtml += '<tr class="bColor"><td colspan="1" align="center">' + localNode + '</td><td colspan="2" align="center">COS-Detected and PTT-Keyed (Full-Duplex)</td><td colspan="4"></td></tr>';
        }

	for (row in tabledata[localNode].remote_nodes) {

            if (tabledata[localNode].remote_nodes[row].info === "NO CONNECTION") {

               tablehtml += '<tr><td colspan="7"> &nbsp; &nbsp; No Connections.</td></tr>';

            } else {

	       nodeNum=tabledata[localNode].remote_nodes[row].node;	
	       if (nodeNum != 1) {

               // ADDED WA3DSP 
               // Increment total and display only requested
               total_nodes++;
               if (row<ndisp) {
                  if (sall == "1" || tabledata[localNode].remote_nodes[row].last_keyed != "Never" || total_nodes < 2) {
                     shown_nodes++;
               // END WA3DSP
        		
                     // Set blue, red, yellow, whatever, or no background color
	             if (tabledata[localNode].remote_nodes[row].keyed == 'yes') {
		        tablehtml += '<tr class="rColor">';
	             } else if (tabledata[localNode].remote_nodes[row].mode == 'C') {
		        tablehtml += '<tr class="cColor">';
	             } else {
		        tablehtml += '<tr>';
	             }

	             var id = 't' + localNode + 'c0' + 'r' + row;
	             //console.log(id);
	             tablehtml += '<td id="' + id + '" align="center" class="nodeNum">' + tabledata[localNode].remote_nodes[row].node + '</td>';
            		
	             // Show info or IP if no info
	             if (tabledata[localNode].remote_nodes[row].info != "") {
		     	tablehtml += '<td>' + tabledata[localNode].remote_nodes[row].info + '</td>';
	             } else {
		    	tablehtml += '<td>' + tabledata[localNode].remote_nodes[row].ip + '</td>';
	             }
            	     tablehtml += '<td align="center" id="lkey' + row + '">' + tabledata[localNode].remote_nodes[row].last_keyed + '</td>';
 		     tablehtml += '<td align="center">' + tabledata[localNode].remote_nodes[row].link + '</td>';
	             tablehtml += '<td align="center">' + tabledata[localNode].remote_nodes[row].direction + '</td>';
	             tablehtml += '<td align="right" id="elap' + row +'">' + tabledata[localNode].remote_nodes[row].elapsed + '</td>';
            		
	             // Show mode in plain english
	             if (tabledata[localNode].remote_nodes[row].mode == 'R') {
		      	tablehtml += '<td align="center">Receive Only</td>';
	             } else if (tabledata[localNode].remote_nodes[row].mode == 'T') {
		     	tablehtml += '<td align="center">Transceive</td>';
		     } else if (tabledata[localNode].remote_nodes[row].mode == 'C') {
		    	tablehtml += '<td align="center">Connecting</td>';
	             } else {
		     	tablehtml += '<td align="center">' + tabledata[localNode].remote_nodes[row].mode + '</td>';
	             }
       		     tablehtml += '</tr>';
	          }
        	  //console.log('tablehtml: ' + tablehtml);

                 }  
               }
            }
         }    

      // ADDED WA3DSP
      // Display Count 
         if (sdisp === 1 && total_nodes >= shown_nodes && total_nodes > 1) {
            if (shown_nodes == total_nodes) {
               tablehtml += '<td colspan="2"> &nbsp; &nbsp;' + total_nodes + ' nodes connected.</td></tr>';
            } else {
               tablehtml += '<td colspan="2"> &nbsp; &nbsp;' + shown_nodes + ' shown of ' + total_nodes + ' nodes connected.</td></tr>';
            }
         }
      // END WA3DSP

      $('#table_' + localNode + ' tbody:first').html(tablehtml);
    }
});

        
        // Fires when new time data comes in. Updates only time columns
        source.addEventListener('nodetimes', function(event) {
			//console.log('nodetimes: ' + event.data);	        
			var tabledata = JSON.parse(event.data);
			for (localNode in tabledata) {
				tableID = 'table_' + localNode;
				for (row in tabledata[localNode].remote_nodes) {
					//console.log(tableID, row, tabledata[localNode].remote_nodes[row].elapsed, tabledata[localNode].remote_nodes[row].last_keyed);

					rowID='lkey' + row;
					$( '#' + tableID + ' #' + rowID).text( tabledata[localNode].remote_nodes[row].last_keyed );
					rowID='elap' + row;
		 			$( '#' + tableID + ' #' + rowID).text( tabledata[localNode].remote_nodes[row].elapsed );

				}
			}


	                if (spinny == "*") {
                                spinny = "|";
                        } else if (spinny == "|") {
                                spinny = "/";
                        } else if (spinny == "/") {
                                spinny = "-";
                        } else if (spinny == "-") {
                                spinny = "\\";
                        } else if (spinny == "\\") {
                                spinny = "|";
                        } else {
                                spinny = "*";
                        }
                        $('#spinny').html(spinny);
        });
        
        // Fires when connection message comes in.
        source.addEventListener('connection', function(event) {
			//console.log(statusdata.status);
			var statusdata = JSON.parse(event.data);
			tableID = 'table_' + statusdata.node;
			$('#' + tableID + ' tbody:first').html('<tr><td colspan="7">' + statusdata.status + '</td></tr>');
		});
		       
    } else {
        $("#list_link").html("Sorry, your browser does not support server-sent events...");
    }
});
</script>

<?php
if ($_SESSION['sm61loggedin'] === true) {
?>

<!-- Connect form -->
<div style="border-radius: 10px;" id="connect_form">
<?php 
if (count($nodes) > 0) {
    if (count($nodes) > 1) {
        print "<select id=\"localnode\" class=\"submit\">";
        foreach ($nodes as $node) {

        if (isset($astdb[$node]))
                $info = $astdb[$node][1] . ' ' . $astdb[$node][2] . ' ' . $astdb[$node][3];
        else
                $info = "Node not in database";

            print "<option value=\"$node\">$node => $info</option>";
        }
        print "</select>";
    } else {
        print " <input class=\"submit\" type=\"hidden\" id=\"localnode\" value=\"{$nodes[0]}\">";
    }
?>
 <input style="margin-top:10px;" type="text" id="node">
Permanent <input class="submit" type="checkbox"><br/>
<input style="margin-top:2px;" type="button" class="submit" value="Connect" id="connect">
<input type="button" class="submit" value="Disconnect" id="disconnect">
<input type="button" class="submit" value="Monitor" id="monitor">
<input type="button" class="submit" value="Local Monitor" id="localmonitor">
<input type="button" class="submit" value="DTMF" id="dtmf">
<input type="button" class="submit" value="Lookup" id="astlookup">
<input type="button" class="submit" value="Rpt Stats" id="rptstats">
<input type="button" class="submit" value="Bubble Chart" id="map">
<input type="button" class="submit" value="Control" id="controlpanel">
<input style="margin-bottom:5px;" type="button" class="submit" value="Favorites" id="favoritespanel">

<SCRIPT>
    function OpenActiveNodes () {
        window.open('http://stats.allstarlink.org');
    }
    function OpenAllNodes () {
        window.open('https://allstarlink.org/nodelist.php');
    }
    function OpenHelp () {
        window.open('https://hamvoip.org/#howtos');
    }
    function OpenConfigEditor () {
        window.open('edit/configeditor.php');
    }
    function OpenWiki () {
        window.open('http://wiki.allstarlink.org');
    }
    function OpenArchiveURL () {
        window.open('<?php if (! empty($ARCHIVE_URL)) print "$ARCHIVE_URL"; ?>');
    }
</SCRIPT>
<br>

<input style="margin-top:5px;" type="button" class="submit" value="Configuration Editor" OnClick="OpenConfigEditor()">
<input type="button" class="submit" value="Iax/Rpt/DP RELOAD" id="astreload">
<input type="button" class="submit" value="AST START" id="astaron">
<input type="button" class="submit" value="AST STOP" id="astaroff">
<input type="button" class="submit" value="RESTART" id="fastrestart">
<input style="margin-bottom:1px;" type="button" class="submit" value="Server REBOOT" id="reboot">
<?php if ($system_type === "hamvoip") { ?>
<input style="margin-bottom:1px;" type="button" class="submit" value="SW Update?" id="openupdate">
<?php } ?>
<br>
<input style="margin-top:1px;" type="button" class="submit" value="AllStar How To's" OnClick="OpenHelp()">
<input type="button" class="submit" value="AllStar Wiki" OnClick="OpenWiki()">
<input type="button" class="submit" value="CPU Status" id="cpustats">
<input type="button" class="submit" value="AllStar Status" id="stats">
<?php if ($EXTN) { ?>
<input type="button" class="submit" value="Registry" id="extnodes">
<?php } ?>
<input type="button" class="submit" value="Node Info" id="astnodes">
<input type="button" class="submit" value="Active Nodes" OnClick="OpenActiveNodes()">
<input style="margin-bottom:1px;" type="button" class="submit" value="All Nodes" OnClick="OpenAllNodes()">
<?php
// ADDED KN2R 6-2018
   if (! empty($DATABASE_TXT)) {
      ?> <input style="margin-bottom:1px;" type="button" class="submit" value="Database" id="database">
      <?php
   }
?>
<br>
<?php
   if ((! empty($ARCHIVE_URL)) || (`cat /etc/asterisk/rpt.conf |egrep -c ^"archivedir"` > 0)) {
      ?> <input style="margin-top:1px;" type="button" class="submit" value="Archive" OnClick="OpenArchiveURL()">
      <?php
   }
// END KN2R
?>
<!-- Added GPIO, WA3DSP 3-25-17 -->
<input style="margin-top:1px;" type="button" class="submit" value="GPIO" id="openpigpio">
<input type="button" class="submit" value="Linux Log" id="linuxlog">
<input type="button" class="submit" value="AST Log" id="astlog">
<input type="button" class="submit" value="Connection Log" id="clog">
<?php if ($IRLPLOG) { ?>
<input type="button" class="submit" value="IRLP Log" id="irlplog">
<?php } ?>
<input type="button" class="submit" value="Web Access Log" id="webacclog">
<input type="button" class="submit" value="Web Error Log" id="weberrlog">
<input style="margin-bottom:10px;" type="button" class="submit" value="Restrict" id="openbanallow">
<br>
<?php
$uptime = exec("$UPTIME");
$hostname = exec("$HOSTNAME |$AWK -F '.' '{print $1}'");
$myday = exec("$DATE '+%A, %B %e, %Y %Z'");
$astport = exec("$CAT /etc/asterisk/iax.conf |$EGREP 'bindport' |$SED 's/bindport=//g'");
$mgrport = exec("$CAT /etc/asterisk/manager.conf |$EGREP '^port =' |$SED 's/port = //g'");
if (empty($WANONLY)) {
   //$myip = exec("$WGET -t 1 -T 3 -q -O- http://checkip.dyndns.org:8245 |$CUT -d':' -f2 |$CUT -d' ' -f2 |$CUT -d'<' -f1");
   $myip = exec("$WGET -t 1 -T 3 -q -O- http://myip.hamvoip.org |$CUT -d':' -f2 |$CUT -d' ' -f2 |$CUT -d'<' -f1");
   $WL=""; $mylanip = exec("$IFCONFIG |$GREP inet | $HEAD -1 |$AWK '{print $2}'");
   if ($mylanip == "127.0.0.1") {
      $mylanip = exec("$IFCONFIG |$GREP inet |$TAIL -1 |$AWK '{print $2}'"); $WL="W";
   }
} else {
   $mylanip = exec("$IFCONFIG |$GREP inet |$HEAD -1 |$AWK '{print $2}'");
   $myip = $mylanip;
}
$myssh = exec("$CAT /etc/ssh/sshd_config |$EGREP '^Port' |$TAIL -1 |$CUT -d' ' -f2");
if ($myip == $mylanip) {
   print "[ $hostname ] [ WAN IP: <a href=\"custom/iplog.txt\" target=\"_blank\">${myip}</a> ] [ AstP: ${astport} ] [ MgrP: ${mgrport} ] [ SShP: ${myssh} ]";
} else {
   print "[ $hostname ] [ WAN IP: <a href=\"custom/iplog.txt\" target=\"_blank\">${myip}</a> ] [ " . $WL . "LAN IP: ${mylanip} ] [ AstP: ${astport} ] [ MgrP: ${mgrport} ] [ SShP: ${myssh} ]";
}
} #endif (count($nodes) > 0)
print "<br>";
print "[ $myday $uptime ]";

?>
</div>

<?php
}
?>

<!-- Nodes table -->
<?php

// ADDED WA3DSP = Display configuration tool
print "<p style=\"margin-bottom:5px;margin-top:10px;\"><input type=\"button\" class=\"submit\" Value=\"Display Configuration\" onclick=\"window.open('display-config.php','DisplayConfiguration','status=no,location=no,toolbar=no,width=600,height=550,left=100,top=100')\">";
// END WA3DSP

// ADDED KN2R - CPU temp indicator
$CPUTemp = exec("/usr/local/sbin/supermon/get_temp");
print " &nbsp; [ $CPUTemp]";

// ADDED KN2R - 11/7/2018 - Core dump file indicator
if (isset($SHOW_COREDUMPS) && ($SHOW_COREDUMPS == 'yes')) {
    $Cores = `ls /var/lib/systemd/coredump |wc -w`;
    if ($Cores == 1) {
       print " [ Core dump: <span style=\"background-color: yellow; color: black;\">&nbsp;$Cores</span>&nbsp;]";
    } elseif ($Cores > 1) {
       print " [ Core dumps: <span style=\"background-color: red; color: yellow; font-weight: bold;\">&nbsp;$Cores</span>&nbsp;]";
    }
}
if ((`cat /etc/asterisk/rpt.conf |egrep -c ^"outstreamcmd"` > 0) && (`cat /etc/asterisk/rpt.conf |egrep -c ^"archivedir"` > 0) && ($STREAMING_NODE == $ARCHIVING_NODE)) {
   print " [ Streaming & Archiving node(s): $STREAMING_NODE ]";
} else {
   if (`cat /etc/asterisk/rpt.conf |egrep -c ^"outstreamcmd"` > 0) {
      if (isset($STREAMING_NODE)) {
         print " [ Streaming node(s): $STREAMING_NODE ]";
      }
   }
   if (`cat /etc/asterisk/rpt.conf |egrep -c ^"archivedir"` > 0) {
      if (isset($ARCHIVING_NODE)) {
         print " [ Archiving node(s): $ARCHIVING_NODE ]";
      }
   }
}
// END KN2R

print "</p>";

// END KN2R

// Added WA3DSP Weather conditions
if (isset($LOCALZIP)) {
    $WX = exec("/usr/local/sbin/weather.sh $LOCALZIP v");
    print "<p style=\"margin-top:0px;\">[ Weather conditions for $LOCATION $LOCALZIP: ";
    print "<span style=\"margin-top:0px; background-color: GAINSBORO;\">&nbsp;$WX&nbsp;</span> ]";
}

// ADDED WA3DSP Autosky warning messages
// if they exist
if (file_exists("/tmp/AUTOSKY/warnings.txt")) {
    if (filesize("/tmp/AUTOSKY/warnings.txt")) {
        $warnings = file_get_contents ("/tmp/AUTOSKY/warnings.txt");
        print "<span style=\"color: red;\"><br><b>$warnings</b></span>";
    }
}
// END WA3DSP

print "</p>";

#print '<pre>'; print_r($nodes); print '</pre>';
foreach($nodes as $node) {
    #print '<pre>'; print_r($config[$node]); print '</pre>';

    if (isset($astdb[$node]))
        $info = $astdb[$node][1] . ' ' . $astdb[$node][2] . ' ' . $astdb[$node][3];
      else
        $info = "Node not in database";

    if (($info == "Node not in database") || (isset($config[$node]['hideNodeURL']) && $config[$node]['hideNodeURL'] == 1)) {
        $nodeURL = $node;
        $title = "&nbsp; Private Node $node => $info &nbsp; ";

    // ADDED KN2R
        if (isset($config[$node]['lsnodes'])) {
            $lsNodesChart = $config[$node]['lsnodes'];
            $title .= "<a href=\"$lsNodesChart\" target=\"_blank\" id=\"lsnodeschart\">lsNodes</a> &nbsp;";
        } else if ((preg_match("/localhost/", $config[$node]['host'])) || ((preg_match("/127.0.0.1/", $config[$node]['host'])))) {
            $lsNodesChart = "/cgi-bin/lsnodes_web?node=$node";
            $title .= "<a href=\"$lsNodesChart\" target=\"_blank\" id=\"lsnodeschart\">lsNodes</a> &nbsp;";
        }
    } else {
        $nodeURL = "http://stats.allstarlink.org/nodeinfo.cgi?node=$node";
        $bubbleChart = "http://stats.allstarlink.org/getstatus.cgi?$node";
    	$title = "&nbsp; Node <a href=\"$nodeURL\" target=\"_blank\">$node</a> => $info &nbsp;";
    	$title .= "&nbsp; <a href=\"$bubbleChart\" target=\"_blank\" id=\"bubblechart\">Bubble Chart</a> &nbsp;" ;
        if (isset($config[$node]['lsnodes'])) {
            $lsNodesChart = $config[$node]['lsnodes'];
            $title .= "<a href=\"$lsNodesChart\" target=\"_blank\" id=\"lsnodeschart\">lsNodes</a> &nbsp;";
        } else if ((preg_match("/localhost/", $config[$node]['host'])) || ((preg_match("/127.0.0.1/", $config[$node]['host'])))) {
            $lsNodesChart = "/cgi-bin/lsnodes_web?node=$node";
            $title .= "<a href=\"$lsNodesChart\" target=\"_blank\" id=\"lsnodeschart\">lsNodes</a> &nbsp;";
        }
    // END KN2R

        if (isset($config[$node]['listenlive'])) {
            $ListenLiveLink = $config[$node]['listenlive'];
            $title .= "<a href=\"$ListenLiveLink\" target=\"_blank\" id=\"lsnodeschart\">Listen Live</a> &nbsp;";
        }
    }
?>
	<table class=gridtable id="table_<?php echo $node ?>">
	<colgroup>
           <col span="1">
           <col span="1">
           <col span="1">
           <col span="1">
           <col span="1">
           <col span="1">
           <col span="1">
        </colgroup>
	<thead>

        <tr><th colspan="7"><i><?php echo $title; ?></i></th></tr>

	<tr><th>Node</th><th>Node Information</th><th>Received</th><th>Link</th><th>Direction</th><th>Connected</th><th>Mode</th></tr>
	</thead>
	<tbody>
	<tr><td colspan="7"> &nbsp; Waiting...</td></tr>
	</tbody>
	</table><br />
<?php
}
?>
</div>
<div id="spinny">
</div>
<?php include "footer.inc"; ?>

