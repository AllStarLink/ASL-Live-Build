<?php

include("../session.inc");
include("../common.inc");

$DEBUG=FALSE;

/*

   Sample Parameters from simpleusb

    [selected] => Array
        (
            [name] => usb
            [devstr] => 1-1.5:1.0
            [devnum] => 1
            [rxmixerset] => 500
            [rxondelay] => 0
            [rxaudiodelay] => 0
            [txmixaset] => 850
            [txmixbset] => 500
            [txdsplevel] => 500
            [pttstatus] => 0
            [rxsdstring] => Ignored
            [rxcdstring] => CM108/CM119 Active HIGH
            [rxtestkeyed] => 0
            [coscomposite] => 0
            [deemphasis] => 0
            [preemphasis] => 0
            [echomode] => 0
            [plfilter] => 1
            [dcsfilter] => 0
            [rxboostset] => 1
            [invertptt] => 0
            [rxcdtype] => 1
            [rxsdtype] => 0
	    [tx_audio_level_method] => 0
        )


    [devices] => Array
        (
            [usb] => usb
            [usb2] => usb2
        )

    [hasusb] => Array
        (
            [usb] => 1
            [usb2] => 1
        )

*/


if ($_SESSION['sm61loggedin'] !== true) {
    die ("<br><h3>ERROR: You Must login to supermon to use the 'simpleusb-control' function!</h3>");
}
?>

<html>
<!--	simpleusb-control.php
	
	View and control simpleusb from supermon
	Copyright - D. Crompton, WA3DSP 6/2019

	This program reads and updates the HAMVOIP simpleusb settings via the
	Asterisk client and is intended to be run as an option within Supermon

	First column commented print statments are for testing - use $DEBUG=TRUE for testing
-->

<head>
<link type="text/css" rel="stylesheet" href="../supermon.css">
</head>

<body style="background-color: powderblue;">

<p style="text-align:center;font-size: 1.5em;"><b>View/Control simpleusb </b></p>

<?php

// FUNCTIONS

	// This function displays a radio button form with variable options and initially displays current values

        function Disp_Form_Radio ($current,$title,$form_name,$opts,$opt1,$opt1_cmd,$opt2="",$opt2_cmd="",$opt3="",$opt3_cmd="",$opt4="",$opt4_cmd="") {
            $M=1;

		switch ($current) {
	    	case 0:
     			$check1="checked";
			$check2="";
			$check3="";
			$check4="";
			break;
		case 1:
			$check1="";
                        $check2="checked";
                        $check3="";
			$check4="";
			break;
		case 2:
			$check1="";
                        $check2="";
                        $check3="checked";
			$check4="";
			break;
		case 3:
                        $check1="";
                        $check2="";
                        $check3="";
			$check4="checked";
                        break;

	    }	

	    switch ($opts[0]) {
		// Special case 1 - multiple options one item per line
		case 1:
			switch ($opts[1]) {
				case 1:
					break;
				case 2:
 
					echo "<td><b>$title</b><input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt1_cmd\" $check1> $opt1";
                        		echo "&nbsp;&nbsp;&nbsp;";
                        		echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt2_cmd\" $check2> $opt2 </td>";
                        		break;
				// One item - 3 options
				case 3:
					echo "<td><b>$title</b><input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt1_cmd\" $check1> $opt1";
		                        echo "&nbsp;&nbsp;&nbsp;";
                		        echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt2_cmd\" $check2> $opt2";
					echo "&nbsp;&nbsp;&nbsp;";
                                        echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt3_cmd\" $check3> $opt3 </td>";
                        		break;
				// One item - 4 options
				case 4:
					echo "<td><b>$title</b><input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt1_cmd\" $check1> $opt1";     
		                        echo "&nbsp;&nbsp;&nbsp;";
                		        echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt2_cmd\" $check2> $opt2";
                        		echo "&nbsp;&nbsp;&nbsp;";
                        		echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt3_cmd\" $check3> $opt3";
                        		echo "&nbsp;&nbsp;&nbsp;";
                                        echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt4_cmd\" $check4> $opt4 </td>";
					break;

				}
			break;
		// 2 options - 2 items per line
		case 2:
			echo "<td><b>$title</b></td><td><input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt1_cmd\" $check1> $opt1";
			echo "&nbsp;&nbsp;&nbsp;";
			echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt2_cmd\" $check2> $opt2 </td>";
			break;
		// 3 options - 2 item per line
		case 3:
			echo "<td><b>$title</b></td><td><input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt1_cmd\" $check1> $opt1";
                        echo "&nbsp;&nbsp;&nbsp;";
			echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt2_cmd\" $check2> $opt2";
			echo "&nbsp;&nbsp;&nbsp;";
                        echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt3_cmd\" $check3> $opt3 </td>";
                        break;
	     }
        }
	
	// Display and select available Devices
        function Disp_Device_Form_Radio ($current,$title,$form_name,$opts,$opt1,$opt1_cmd,$opt2="",$opt2_cmd="",$opt3="",$opt3_cmd="",$opt4="",$opt4_cmd="") {
            $M=1;

// print " Current - $current - $opt1 - $opt2";

		if ( $current == $opt1 ) {
			$check1="checked";
			$check2="";
			$check3="";
			$check4="";
		} elseif ( $current == $opt2 ) {
			$check1="";
			$check2="checked";
			$check3="";
			$check4="";
		} elseif ( $current == $opt3 ) {
			$check1="";
			$check2="";
			$check3="checked";
			$check4="";
		} else {
			$check1="";
			$check2="";
			$check3="";
			$check4="checked";
		}


	    switch ($opts) {
			case 1:
				break;
			case 2:
 
				echo "<td><b>$title</b><input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt1_cmd\" $check1> $opt1";
                       		echo "&nbsp;&nbsp;&nbsp;";
                       		echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt2_cmd\" $check2> $opt2 </td>";
                       		break;

			case 3:		
				echo "<td><b>$title</b><input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt1_cmd\" $check1> $opt1";
	                        echo "&nbsp;&nbsp;&nbsp;";
               		        echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt2_cmd\" $check2> $opt2";
				echo "&nbsp;&nbsp;&nbsp;";
                                echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt3_cmd\" $check3> $opt3 </td>";
                       		break;

			// One item - 4 options
			case 4:
				echo "<td><b>$title</b><input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt1_cmd\" $check1> $opt1";     
	                        echo "&nbsp;&nbsp;&nbsp;";
               		        echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt2_cmd\" $check2> $opt2";
                       		echo "&nbsp;&nbsp;&nbsp;";
                       		echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$opt3_cmd\" $check3> $opt3";
                       		echo "&nbsp;&nbsp;&nbsp;";
                                echo "<input type=\"radio\" class=\"submit\" name=\"$form_name\" value=\"$optr4_cmd\" $check4> $opt4 </td>";
				break;

		}
	}

	// This function displays numeric form values with range checks and initially displays current values

	function Disp_Form_Numeric ($current,$title,$form_name,$len,$minval,$maxval,$disabled) {
		
		if ( $disabled == "" ) {
			echo "<td><b>$title</b>";
			$Submit="submit";
		} else {
			// if you do not want to display disabled object just return here
			echo "<td>&nbsp;&nbsp;<span style=\"color: #505050;\"> $title</span>";
			$Submit="";
		}
			
		echo " <input type=\"number\" class=\"$Submit\" name=\"$form_name\" maxlength=\"$len\" min=\"$minval\" max=\"$maxval\" value=\"$current\" $disabled style=\"width: 60px;\" /></td>";
        }

	// Check if value change has been made and if so update

	function check_new_val($item,$loc,$off,$on,$prefix="") {

		if ( $item == "" ) {
			return;
		}
		if ( $item != $loc ) {
			if ((isset($prefix)) && ($prefix != "")) {
				Send_Command($prefix.$item);
			} else {

				if ( $item == 0 ) {
					if ( $off != "" ) {
						Send_Command($off);
					}
				} else {
					Send_command($on);
				}
			}
		}
	}

	// Send menu-support command to Allstar client
	function Send_Command($Command) {
		global $SUDO, $ASTERISK, $DEBUG;

	if ( $DEBUG ) {
		print $Command;
	}

	if ( $Command == "j" ) {
		$_SESSION['WRITE']="1";
	}
	$ret=`$SUDO $ASTERISK -rx "susb tune menu-support $Command"`;
		return $ret;
	}

	// Send any command to Allstar client
        function Send_Basic_Command($Command) {
                global $SUDO, $ASTERISK, $DEBUG;
                $ret=`$SUDO $ASTERISK -rx "$Command"`;

	if ( $DEBUG ) {
		print $Command;

	}
                return $ret;
        }

	// Allows adding content or spaces to title
	function Align_Title($title) {
			$title .= " -";
		return $title;
	}

	// Return item value from 'selected' array
	function state($item) {
        	global $j;
        	return ($j['selected'][$item]);
	}

	// Return devices from 'devices' array
	function device($stanza) {
	        global $j;
        	return ($j['devices'][$stanza]);
	}

	// Return if device is active
	function hasusb($device) {
		global $j;
		if ( ($j['hasusb'][$device]) == 1) {
			return "Attached and Configurable";
		} else {
			return "Not Attached and not configurable";
		}
	}

	// Security for POST
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
	return $data;
	}

	function status_text($status) {
		if ( $status == "0" ) {
			return "<span style=\"background-color: #FFFF00\">'CLEAR'</span>";
		} else {
			return "<span style=\"background-color: red\">'KEYED'</span>";
		}
	}

// BEGIN MAIN

        // Get current values from the Asterisk Client
        $current=Send_Command("4");
        $j = json_decode($current, true); // Parse returned JSON data

	if ( json_last_error() != JSON_ERROR_NONE ) {
		Print " Read Error - Try again";
		exit;
	}

        $device_cur=device('state'('name'));
        $deemp_loc=state('deemphasis');
        $preemp_loc=state('preemphasis');
        $plfilter_loc=state('plfilter');
        $dcsfilter_loc=state('dcsfilter');
        $coskey_loc=state('rxtestkeyed');
        $rxboost_loc=state('rxboostset');
        $pttinv_loc=state('invertptt');
        $cos_loc=state('rxcdtype');
        $ctcss_loc=state('rxsdtype');
        $echo_loc=state('echomode');
        $rxlev_loc=state('rxmixerset');
        $rxondly_loc=state('rxondelay');
        $rxauddly_loc=state('rxaudiodelay');
        $txleva_loc=state('txmixaset');
        $txlevb_loc=state('txmixbset');
        $txdsplev_loc=state('txdsplevel');
	$pttstatus=state('pttstatus');
	$coscomposite=state('coscomposite');
	$txmode_loc=state('tx_audio_level_method');
	$write_loc="";

	// POST values
	
//	if ( (isset($_POST["device"])) && ($_POST["device"] != "" )) {

		$device=test_input($_POST["device"]);
		$deemp=test_input($_POST["deemp"]);
		$preemp=test_input($_POST["preemp"]);
		$plfilter=test_input($_POST["plfilter"]);
		$dcsfilter=test_input($_POST["dcsfilter"]);
		$cos=test_input($_POST["cos"]);
		$ctcss=test_input($_POST["ctcss"]);
		$rxondly=test_input($_POST["rxondly"]);
		$rxauddly=test_input($_POST["rxauddly"]);
		$rxlev=test_input($_POST["rxlev"]);
		$txleva=test_input($_POST["txleva"]);
		$txlevb=test_input($_POST["txlevb"]);
		$txdsplev=test_input($_POST["txdsplev"]);	
		$rxboost=test_input($_POST["rxboost"]);
		$echo=test_input($_POST["echo"]);
		$pttinv=test_input($_POST["pttinv"]);
		$coskey=test_input($_POST["coskey"]);
		$write=test_input($_POST["write"]);	
		$txmode=test_input($_POST["txmode"]);
		

	// If device changes do not change anything else on this update

		if ( $device == "") {
			Send_Basic_Command("susb active $device_cur");
			$_SESSION['DEVICE']=$device_cur;
			
		} elseif (( ! isset($_SESSION['DEVICE']) ) || ( $_SESSION['DEVICE'] != $device )) { 
			Send_Basic_Command("susb active $device");
			$_SESSION['DEVICE']=$device;

		} else {

			check_new_val($deemp,$deemp_loc,"d","D"); 
			check_new_val($preemp,$preemp_loc,"p","P");
			check_new_val($plfilter,$plfilter_loc,"r","R");
			check_new_val($dcsfilter,$dcsfilter_loc,"s","S");
		       	check_new_val($rxboost,$rxboost_loc,"x","X");
		       	check_new_val($echo,$echo_loc,"e","E");
			check_new_val($pttinv,$pttinv_loc,"i","I");
			check_new_val($coskey,$coskey_loc,"k","K");
			check_new_val($cos,$cos_loc,"","","m");
			check_new_val($ctcss,$ctcss_loc,"","","M");
			check_new_val($rxondly,$rxondly_loc,"","","t");
			check_new_val($rxauddly,$rxauddly_loc,"","","T");
			check_new_val($rxlev,$rxlev_loc,"","","c");
			check_new_val($txleva,$txleva_loc,"","","f");
			check_new_val($txlevb,$txlevb_loc,"","","g");
			check_new_val($txdsplev,$txdsplev_loc,"","","h");
			check_new_val($txmode,$txmode_loc,"","","n");
			check_new_val($write,$write_loc,"","j");
		}
//	}

?>

<!-- The following prints the web screen -->
<center>
<!-- Use self method and strip html for security -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p>
<input type="submit" class="submit" value="Update">
 &nbsp;
<input type="button" class="submit" Value="Close Window" onclick="self.close()">
</p>
<table style="border-collapse: collapse; border-spacing: 0px 0px; table-layout:fixed; width:700px;"
?>
<tr>
<?php

        // Get current values again after update
	$current=Send_Command("4");
        $j = json_decode($current, true); // Parse returned JSON dat

	if ( json_last_error() != JSON_ERROR_NONE ) {
		Print " Read Error - Try again";
		exit;
	}

	$device_cur=device('state'('name'));
        $deemp_loc=state('deemphasis');
        $preemp_loc=state('preemphasis');
        $plfilter_loc=state('plfilter');
        $dcsfilter_loc=state('dcsfilter');
	$coskey_loc=state('rxtestkeyed');
	$rxboost_loc=state('rxboostset');
	$pttinv_loc=state('invertptt');
	$cos_loc=state('rxcdtype');
	$ctcss_loc=state('rxsdtype');
	$echo_loc=state('echomode');
	$rxlev_loc=state('rxmixerset');
	$rxondly_loc=state('rxondelay');
	$rxauddly_loc=state('rxaudiodelay');
	$txleva_loc=state('txmixaset');
	$txlevb_loc=state('txmixbset');
	$txdsplev_loc=state('txdsplevel');
        $pttstatus=state('pttstatus');
        $coscomposite=state('coscomposite');
	$txmode_loc=state('tx_audio_level_method');

	$keys = array_keys($j['devices']);

//print_r($j);

// Display current device and device options
// Options - title text, button name, # of options, Option 1 text, Option 1 command, Option 2 text, Option2 command, .....

$Device_Status=hasusb($device_cur);
echo "<tr align=\"center\"><td><p style=\"font-size:1.1em;\"><b>Current Device is - $device_cur</b></p></td></tr><tr align=\"center\">";
echo "<tr align=\"center\"><td><p style=\"font-size:1.1em;\"><b>This device is $Device_Status</b></p></td></tr><tr align=\"center\">";

switch(count($j['devices'])) {
	case 1:
		?>
                <input type="text" name="device" value="<?php echo $device_cur; ?>" style="display:none">
                <?php
		break;
	case 2:
		Disp_Device_Form_Radio($device_cur,"<p style=\"font-size:1.1em;\">Select Device - &nbsp;&nbsp;  ","device","2",$keys[0],$keys[0],$keys[1],$keys[1]);
		break;
	case 3:
		Disp_Device_Form_Radio($device_cur,"<p style=\"font-size:1.1em;\">Select Device - &nbsp;&nbsp;   ","device","3",$keys[0],"",$keys[1],"",$keys[2],"");
		break;
	case 4:
		Disp_Device_Form_Radio($device_cur,"<p style=\"font-size:1.1em;\">Select Device - &nbsp;&nbsp;   ","device","4",$keys[0],"",$keys[1],"",$keys[2],"",$keys[3],"");
		break;
}
echo "</tr>";

// Display parameters and options
echo"</table><table cellspacing=\"0\" cellpadding=\"0\" style=\"margin-bottom:0; margin-top:1em; border-spacing: 1em .5em; table-layout:auto; width:700px;\"><tr>";

// Call html form radio button
// Options - title text, button name, # of options, Option 1 text, Option 1 command, Option 2 text, Option2 command, .....
// If # of options=1 then print only one item per line. The second value is the number of options.

Disp_Form_Radio($deemp_loc,Align_Title("DE-EMPHASIS"),"deemp","2","OFF","0","ON","1");
Disp_Form_Radio($preemp_loc,Align_Title("PRE-EMPHASIS"),"preemp","2","OFF","0","ON","1");
echo "</tr><tr>";
Disp_Form_Radio($plfilter_loc,Align_Title("PL FILTER"),"plfilter","2","OFF","0","ON","1");
Disp_Form_Radio($dcsfilter_loc,Align_Title("DCS FILTER"),"dcsfilter","2","OFF","0","ON","1");
echo "</tr><tr>";
Disp_Form_Radio($echo_loc,Align_Title("ECHO BACK"),"echo","2","OFF","0","ON","1");
Disp_Form_Radio($coskey_loc,Align_Title("KEY COS"),"coskey","2","OFF","0","ON","1");
echo "</tr><tr>";
Disp_Form_Radio($rxboost_loc,Align_Title("RX BOOST"),"rxboost","2","OFF","0","ON","1");
Disp_Form_Radio($pttinv_loc,Align_Title("INVERT PTT"),"pttinv","2","OFF","0","ON","1");
echo "</tr></table>";
echo "<table style=\"margin-bottom:0; border-spacing: 10px 10px; table-layout:fixed; width:800px;\">";
echo "<tr align=\"center\">";
Disp_Form_Radio($cos_loc,"COS - &nbsp;&nbsp;&nbsp;","cos","13","None","0","USB","1","USB-invert","2");
echo "</tr><tr align=\"center\">";
Disp_Form_Radio($ctcss_loc,"CTCSS -","ctcss","13","None","0","USB","1","USB-invert","2");
echo "</tr><tr align=\"center\">";
//Disp_Form_Radio($mode,"TX Audio Mode - ","mode","14","Log","0","Linear","1","Log/DSP","2","Linear/DSP","3");
Disp_Form_Radio($txmode_loc,"TX Audio Mode - ","txmode","12","Log","0","Linear","1");
?>
</tr>
</table>
<table style="margin-bottom:0; border-spacing: 10px 10px; table-layout:fixed; width:700px;">
<tr>
<?php
// Display numeric values - Current, Title, Value, Length, Min value, Max Value, disabled 
Disp_Form_Numeric($rxlev_loc,"RX Level&nbsp;&nbsp;&nbsp; -","rxlev","3","0","999","");
Disp_Form_Numeric($rxondly_loc,"rxondelay&nbsp;&nbsp; -","rxondly","4","-300","300","");
Disp_Form_Numeric($rxauddly_loc,"rxaudiodelay&nbsp; -","rxauddly","3","0","26","");
echo "</tr><tr>";

// Example to gray selections - not currently used
// Disable DSP for log and linear modes and TXB for DSP modes
//if ( $mode != "2" && $mode != "3" ) {
//        $dsp="disabled";
	$txb="";
//} else {
        $dsp="";
//	$txb="disabled";
//}

Disp_Form_Numeric($txleva_loc,"TX Level A -","txleva","3","0","999","");
Disp_Form_Numeric($txlevb_loc,"TX Level B -","txlevb","3","0","999",$txb);
Disp_Form_Numeric($txdsplev_loc,"TX DSP Level -","txdsplev","3","800","999",$dsp);

echo "</tr></table>";
$cos_stat=status_text($coscomposite);
$ptt_stat=status_text($pttstatus);
echo "<table cellspacing=\"20\"><tr>";
echo "<td><b>COS/CTCSS Composite Status - $cos_stat</b></td><td><b>PTT Status - $ptt_stat</b></td>";
echo "</tr></table>";
echo "<table><tr><td>";

Disp_Form_Radio($write_loc,"Permanently Write Settings -","write","2","NO","0","YES","1");
echo "</td></tr></table>";
if ( (isset($_SESSION["WRITE"])) &&   ( $_SESSION['WRITE'] == "1" )) {
	echo "<table>"; 
	echo "<tr><td align=\"center\">";
	echo "<p style=\"font-size:1.1em;\"><b>";
	echo "Device '$device_cur' Settings Permanently Saved";
	echo "</b></td></tr></table>";
        $_SESSION['WRITE']="0";
}

// Following for test purposes only
if ($DEBUG ) {
	$data=Send_Command("2");
	if ( $data == "" ) {
		print "<p>---NONE---</p>";
	} else {
		print "<pre>$data</pre>";
	} 
}
?>
</center>
</form>
</body>
</html>

