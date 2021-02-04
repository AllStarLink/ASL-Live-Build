<?php

// New features, IRLP capability, Paul Aidukas KN2R (Copyright) July 15, 2013-2017
// For ham radio use only, NOT for comercial use!

$var1 = explode('/', $_SERVER['REQUEST_URI']);
$var2 = array_pop($var1);
$current_url = urldecode($var2);

// Read allmon INI file
$iniFile = 'allmon.ini';
if (!file_exists($iniFile)) {
    die("Couldn't load ini file: $iniFile");
}
$config = parse_ini_file($iniFile, true);
#print "<pre>"; print_r($config); print "</pre>";

if (count($config) == 0) {
    die("Check ini file format");
}

// Make a list of menu items
$systems = array();
foreach($config as $name => $data) {
    
	// Show menu items if allowed. NOTE: Logic change from
	// the "Stock" version of allmon2, which uses the 'nomenu'
	// parameter---this is for compatibility with installed
	// systems. KB4FXC 01/30/2018
    if(!isset($data['menu']) || $data['menu'] !== "1") {
        continue;
    }
 
    // Breaks don't show as a menu item
    if(strtolower($name) == 'break') {
        continue;
    }
    
    // Group menus by system
    $sysName = 'MainNavBar';
    if (isset($data['system'])) {
        $sysName=$data['system'];
    }
    
    // URL: Use 'url', 'rtcmnode', 'nodes', or name
    if (isset($data['url'])) {
        $systems[$sysName][$name]['url'] = $data['url'];
    } elseif(isset($data['rtcmnode'])) {
        $systems[$sysName][$name]['url'] = "voter.php?node={$data['rtcmnode']}";
    } elseif (isset($data['nodes'])) {
        $systems[$sysName][$name]['url'] = "link.php?nodes={$data['nodes']}";
    } else {
        $systems[$sysName][$name]['url'] = "link.php?nodes=$name";
    }
}
//print '<pre>'; print_r($systems); print '</pre>';

// Output menu 
$outBuf = "<div id=\"menu\">\n";
$outBuf .= "<ul>";
foreach ($systems as $sysName => $items) {
    if ($sysName == "MainNavBar") {
        // $itemName = key($items);
        // $url =  $items[$itemName]['url'];
        // $outBuf .= "<li><a href=\"$url\">$itemName</a></li>\n";
        $outBuf .= "<li>";
        foreach($items as $itemName => $itemValue) {
            if ($current_url == $itemValue['url']) {
                    $outBuf .= " <a class=\"active\" href=\"" . $itemValue['url'] . "\">$itemName</a>";
            } else {
                if (is_numeric($itemName)) {
                    $outBuf .= " <a href=\"" . $itemValue['url'] . "\">$itemName</a>";
                } else {
                    $outBuf .= " <a href=\"" . $itemValue['url'] . "\" target=\"_blank\">$itemName</a>";
                }
            }
        }
        $outBuf .= "</li>";
    } else {
        $outBuf .= "<li class=\"dropdown\">\n";
        $outBuf .= "<a href=\"#\" class=\"dropbtn\">$sysName</a>";
        $outBuf .= "<div class=\"dropdown-content\">";
        foreach($items as $itemName => $itemValue) {
            if ($current_url == $itemValue['url']) {
                $outBuf .= " <a class=\"active\" href=\"" . $itemValue['url'] . "\">$itemName</a>";
            } else {
                $outBuf .= " <a href=\"" . $itemValue['url'] . "\">$itemName</a>";
            }
        }
        $outBuf .= "</div>\n</li>\n";
    }
}

$outBuf .= "</ul>\n</div>\n";
$outBuf .= "<div class=\"clearer\"></div>";
print $outBuf;

//print '<pre>'; print_r($outBuf); print '</pre>';
?>
