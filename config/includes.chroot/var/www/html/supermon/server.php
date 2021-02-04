<?php

// New features, IRLP capability, Paul Aidukas KN2R (Copyright) July 15, 2013-2017
// For ham radio use only, NOT for comercial use!

#error_reporting(E_ALL);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no');
date_default_timezone_set('America/New_York');
include('amifunctions.inc');
include('nodeinfo.inc');
include("global.inc");
include("common.inc");

// Sanity check: correct request?
if (empty($_GET['nodes'])) {
    $data['status'] = 'Unknown request!';
    echo 'data: ' .  json_encode($data) . "\n\n";
    ob_flush();
    flush();
    exit;
}

// Read parameters passed to us
$passedNodes = explode(',', @trim(strip_tags($_GET['nodes'])));

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
}

$elnk_cache = array();                  // KB4FXC 02/17/2018
$irlp_cache = array();                  // KB4FXC 02/17/2018

// Read allmon INI file
if (!file_exists('allmon.ini')) {
    die("Couldn't load ini file.\n");
}
$config = parse_ini_file('allmon.ini', true);
#print "<pre>"; print_r($config); print "</pre>";
#print "<pre>"; print_r($config[$node]); print "</pre>";

// Sanity check: Must only have nodes in our ini file
$nodes = array();
foreach($passedNodes as $i => $node) {
    if (isset($config[$node])) {
        $nodes[] = $node;
    } else {
        $data = array('node' => $node, 'status' => "Node $node is not in allmon ini file");
        echo "event: nodes\n";
        echo 'data: ' . json_encode($data) . "\n\n";
        ob_flush();
        flush();
    }
}

// Open a socket to each Asterisk Manager.
$servers = array(); $fp = array();
foreach($nodes as $node) {
    $host = $config[$node]['host'];
    
    // Connect and login to each manager only once.
    if (!array_key_exists($host, $servers)) {
        // try to connect
        // Show a nice message that we're attempting to connect
        $data = array('host' => $host, 'node' => $node, 'status'=>' &nbsp; Connecting to Asterisk Manager...');
        echo "event: connection\n";
        echo 'data: ' . json_encode($data) . "\n\n";
        ob_flush();
        flush();

        $fp[$host] = AMIconnect($config[$node]['host']);
        if ($fp[$host] === FALSE) {
            $data = array('host' => $host, 'node' => $node, 'status' => ' &nbsp; Could not connect to Asterisk Manager.');
            echo "event: connection\n";
            echo 'data: ' . json_encode($data) . "\n\n";
            ob_flush();
            flush();
        } else {
            // try to login
            if (FALSE !== AMIlogin($fp[$host], $config[$node]['user'], $config[$node]['passwd'])) {
                $servers[$host] = 'y';
            } else {
                $data = array('host' => $host, 'node' => $node, 'status' => ' &nbsp; Could not login to Asterisk Manager.');
                echo "event: connection\n";
                echo 'data: ' . json_encode($data) . "\n\n";
                ob_flush();
                flush();
            }
        }
    }
}
#print_r($servers);

// Main loop - build $data array and write it as a json object
$current=array();
$saved=array();
$nodeTime=array();
$ticToc='';
while(TRUE) {
    foreach ($nodes as $node) {
        
        // Is host of this node logged in?
        if (isset($servers[$config[$node]['host']])) {
            #print "Servers: " . $servers[$config[$node]['host']];
        } else {
            continue;
            #die ("a host is not logged in");
        }
        
        $connectedNodes = getNode($fp[$config[$node]['host']], $node);
        $sortedConnectedNodes = sortNodes($connectedNodes);

        // Build array of time values
        $nodeTime[$node]['node']=$node;
        $nodeTime[$node]['info'] = getAstInfo($fp[$config[$node]['host']], $node);
        
        // Build array 
        $current[$node]['node']=$node;
        $current[$node]['info'] = getAstInfo($fp[$config[$node]['host']], $node);
        
        // Save remote nodes
        $current[$node]['remote_nodes'] = array(); $i=0;
        foreach ($sortedConnectedNodes as $remoteNode => $arr) {
            // Store remote nodes time values
            $nodeTime[$node]['remote_nodes'][$i]['elapsed'] = $arr['elapsed'];
            $nodeTime[$node]['remote_nodes'][$i]['last_keyed'] = $arr['last_keyed'];
            
            // Store remote nodes other than time values (&nbsp;). 
            // Array key of remote_nodes is not node number to prevent javascript (for in) sorting
            $current[$node]['remote_nodes'][$i]['node']=$arr['node'];
            $current[$node]['remote_nodes'][$i]['info']=$arr['info'];
            $current[$node]['remote_nodes'][$i]['link']=$arr['link'];
            $current[$node]['remote_nodes'][$i]['ip']=$arr['ip'];
            $current[$node]['remote_nodes'][$i]['direction']=$arr['direction'];
            $current[$node]['remote_nodes'][$i]['keyed']=$arr['keyed'];
            $current[$node]['remote_nodes'][$i]['mode']=$arr['mode'];
            $current[$node]['remote_nodes'][$i]['elapsed'] = '&nbsp;';


// WA3DSP
// Change to return "Never" so test can be made in link.php

      if ( $arr['last_keyed'] == "Never" ) {
           $current[$node]['remote_nodes'][$i]['last_keyed'] = 'Never';
      } else {   
           $current[$node]['remote_nodes'][$i]['last_keyed'] = '&nbsp';
      }

// WA3DSP 3/2018    
// Change to add local RXKEYED COS display
$current[$node]['remote_nodes'][$i]['cos_keyed']=$arr['cos_keyed'];
$current[$node]['remote_nodes'][$i]['info']=$arr['info'];
$current[$node]['remote_nodes'][$i]['node']=$arr['node'];
// END

// ADDED KN2R 9/2018
// Change to add local TXKEYED PTT display
$current[$node]['remote_nodes'][$i]['tx_keyed']=$arr['tx_keyed'];
// END KN2R

        $i++;
        }
        
    }

    // Send current nodes only when data changes
    if ($current !== $saved) {
        $saved = $current;
        echo "event: nodes\n";
        echo 'data: ' . json_encode($current) . "\n\n";
    }
    
    // Send times every cycle
    echo "event: nodetimes\n";
    echo 'data: ' . json_encode($nodeTime) . "\n\n";
    
    
    #print "===== \$current =====\n";
    #print_r($current);            
    #print_r($nodeTime);            
    #print "===== end =====\n\n";
    ob_flush();
    flush();
    usleep(400000); # Wait 0.4 seconds
}

fwrite($fp, "ACTION: Logoff\r\n\r\n");
exit;

// Get status for this $node
function getNode($fp, $node) {
    $actionRand = mt_rand();    # Asterisk Manger Interface an actionID so we can find our own response
    
    $actionID = 'xstat' . $actionRand;
    if ((fwrite($fp,"ACTION: RptStatus\r\nCOMMAND: XStat\r\nNODE: $node\r\nActionID: $actionID\r\n\r\n")) !== FALSE ) {
        // Get RptStatus
        $rptStatus = AMIget_response($fp, $actionID);
        #print "<pre>\n===== Xstat =====\n";
        #var_dump($rptStatus);            
        #print "===== end =====\n</pre>\n";
    } else {
        $data['status'] = 'XStat() failed!';
        echo 'data: ' .  json_encode($data) . "\n\n";
        ob_flush();
        flush();
    }
    
    // format of Conn lines: Node# isKeyed lastKeySecAgo lastUnkeySecAgo
    $actionID = 'sawstat' . $actionRand;
    if ((fwrite($fp,"ACTION: RptStatus\r\nCOMMAND: SawStat\r\nNODE: $node\r\nActionID: $actionID\r\n\r\n")) !== FALSE ) {
        // Get RptStatus
        $sawStatus = AMIget_response($fp, $actionID);
        #print "<pre>\n===== \$sawStat start =====\n";
        #var_dump($sawStatus);            
        #print "===== end =====\n</pre>\n";
    } else {
        $data['status'] = 'sawStat failed!';
        echo 'data: ' .  json_encode($data) . "\n\n";
        ob_flush();
        flush();
    }
    
    // Parse this $node. Retuns an array of currently connected nodes
    $current = parseNode($fp, $rptStatus, $sawStatus);
    #print "<pre>===== \$current start =====\n";
    #print_r($current);
    #print "===== end =====\n</pre>";

    return($current);
}

########## ##########
function sortNodes($nodes) {
    
    $arr=array();
    $never_heard=array();
    $sortedNodes=array();
    
    // build an array of heard and unheard
    foreach($nodes as $nodeNum => $row) {
        if ($row['last_keyed'] == '-1') {
            $never_heard[$nodeNum]='Never heard';
        } else {
            $arr[$nodeNum]=$row['last_keyed'];
        }
    }
    
    // Sort nodes that have been heard
    if (count($arr) > 0) {
        asort($arr, SORT_NUMERIC);
    }
    
    // Add in nodes that have not been heard
    if (count($never_heard) > 0) {
        ksort($never_heard, SORT_NUMERIC);
        foreach($never_heard as $nodeNum => $row) {
            $arr[$nodeNum] = $row;
        }
    }
    
    // Build sorted node array
    foreach($arr as $nodeNum => $row) {
        // Build last_keyed string. Converts seconds to hours, minutes, seconds.
        if ($nodes[$nodeNum]['last_keyed'] > -1) {
            $t = $nodes[$nodeNum]['last_keyed'];
            $h = floor($t / 3600);
            $m = floor(($t / 60) % 60);
            $s = $t % 60;
            $nodes[$nodeNum]['last_keyed'] = sprintf("%03d:%02d:%02d", $h, $m, $s);
        } else {
            $nodes[$nodeNum]['last_keyed'] = "Never";
        }

        $sortedNodes[$nodeNum]=$nodes[$nodeNum];
    }
    
    return ($sortedNodes);
}

########## ##########
function parseNode($fp, $rptStatus, $sawStatus) {

    $curNodes = array();
    $links = array();
    $conns = array();

    // Parse 'rptStat Conn:' lines.
    $lines = explode("\n", $rptStatus);
    foreach ($lines as $line) {
        if (preg_match('/Conn: (.*)/', $line, $matches)) {
            $arr = preg_split("/\s+/", trim($matches[1]));
            if(is_numeric($arr[0]) && $arr[0] > 3000000) {
                // no ip when echolink
                $conns[] = array($arr[0], "", $arr[1], $arr[2], $arr[3], $arr[4]);
            } else {
                $conns[] = $arr;
            }
        }

// WA3DSP 3/2018
	if (preg_match('/Var: RPT_RXKEYED=./', $line, $matches)) {
           $rxKeyed=substr($matches[0], strpos($matches[0], "=") + 1);
        }
// END WA3DSP

// ADDED KN2R 9/2018
	if (preg_match('/Var: RPT_TXKEYED=./', $line, $matches)) {
           $txKeyed=substr($matches[0], strpos($matches[0], "=") + 1);
        }
// END KN2R

    }

    #print "<pre>Conns: \n"; print_r($conns); print "</pre>";

    // Parse 'sawStat Conn:' lines.
    $keyups = array();
    $lines = explode("\n", $sawStatus);
    foreach ($lines as $line) {
        if (preg_match('/Conn: (.*)/', $line, $matches)) {
            $arr = preg_split("/\s+/", trim($matches[1]));
            $keyups[$arr[0]] = array('node' => $arr[0], 'isKeyed' => $arr[1], 'keyed' => $arr[2], 'unkeyed' => $arr[3]);
        }
    }
    #print "<pre>====== \$keyups start ======\n"; print_r($keyups); print '====== end ======</pre>'; 

    // Parse 'LinkedNodes:' line.
    if (preg_match("/LinkedNodes: (.*)/", $rptStatus, $matches)) {
        $longRangeLinks = preg_split("/, /", trim($matches[1]));
    }
    foreach ($longRangeLinks as $line) {
        $n = substr($line,1);
        $modes[$n]['mode'] = substr($line,0,1);
    }

    // Pull above arrays together into $curNodes
    if (count($conns) > 0 ) {
        // Local connects

        foreach($conns as $node) {
            $n = $node[0];
            $curNodes[$n]['node'] = $node[0];
            $curNodes[$n]['info'] = getAstInfo($fp, $node[0]);
            $curNodes[$n]['ip'] = $node[1];
            $curNodes[$n]['direction'] = $node[3];
            $curNodes[$n]['elapsed'] = $node[4];
            $curNodes[$n]['link'] = @$node[5];

            ## Fix for table display bug of IRLP nodes - Paul Aidukas, KN2R ;)
            #
            if (isset($node[5])) {
                $curNodes[$n]['link'] = @$node[5];
            } else {
                $curNodes[$n]['direction'] = $node[2];
                $curNodes[$n]['elapsed'] = $node[3];
                if (isset($modes[$n]['mode'])) {
                    if ($modes[$n]['mode'] == 'C') {
                        $curNodes[$n]['link'] = "CONNECTING";
                    } else {
                        $curNodes[$n]['link'] = "ESTABLISHED";
                    }
                }
            }

            $curNodes[$n]['keyed'] = 'n/a';
            $curNodes[$n]['last_keyed'] = 'n/a';

            // Get mode
            if (isset($modes[$n])) {
                $curNodes[$n]['mode'] = $modes[$n]['mode'];
            } else {
                $curNodes[$n]['mode'] = 'Local Monitor';
            }
            $n++;
        }

        // Pullin keyed
        foreach($keyups as $node => $arr) {
            if ($arr['isKeyed'] == 1) {
                $curNodes[$node]['keyed'] = 'yes';
            } else {
                $curNodes[$node]['keyed'] = 'no';
            }
            $curNodes[$node]['last_keyed'] = $arr['keyed'];
        }

// WA3DSP 3/2018
	$curNodes[1]['node'] = 1;

     } else {
	$curNodes[1]['info'] = "NO CONNECTION";
}    

if ($rxKeyed === "1") {
   $curNodes[1]['cos_keyed'] = 1;
} else {
   $curNodes[1]['cos_keyed'] = 0;
}
// END WA3DSP

// ADDED KN2R 9/2018 - PTT indicator
if ($txKeyed === "1") {
   $curNodes[1]['tx_keyed'] = 1;
} else {
   $curNodes[1]['tx_keyed'] = 0;
}
// END KN2R

    return $curNodes;
}
?>
