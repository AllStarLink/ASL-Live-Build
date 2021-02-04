<?php
//
// Major refactoring -- KB4FXC 02/2018
//
error_reporting(0);

// Reads output lines from Asterisk Manager
function AMIget_response($fp, $actionID) {
    while (TRUE) {
		$str = fgets($fp);
		# Looking for our actionID
		if ("ActionID: $actionID" == trim($str)) {
			$response = $str;
			while (TRUE) {
				$str = fgets($fp);
		        #if (strlen(trim($str)) != 0 ) {
                if ($str != "\r\n") {
		            $response .= $str;
		        } else {
		            return($response);
		        }
			}
		}
    }
}

function AMIconnect($host) {
    // Set default port if not provided
    $arr = explode(":", $host);
    $ip = $arr[0];
    if (isset($arr[1])) {
        $port = $arr[1];
    } else {
        $port = 5038;
    }
    
    // Open a manager socket.
    $fp = @fsockopen($ip, $port, $errno, $errstr, 5);
    #print "parms: $ip $port $errno $errstr";
    return ($fp);
}

function AMIlogin($fp, $user, $password) {
    // Login
	$actionID = $user . $password;
    fwrite($fp,"ACTION: LOGIN\r\nUSERNAME: $user\r\nSECRET: $password\r\nEVENTS: 0\r\nActionID: $actionID\r\n\r\n");
    $login = AMIget_response($fp, $actionID);
	if (preg_match("/Authentication accepted/", $login) == 1) {
		return(TRUE);
	} else {
		return(FALSE);
	}
}


// Send a command to the AMI and return the response.
function AMIcommand($fp, $cmdString) {

	// AMI needs an ActionID so we can find our own response
	$actionRand = mt_rand();
	$actionID = 'cpAction_' . $actionRand;

	if ((@fwrite($fp,"ACTION: COMMAND\r\nCOMMAND: $cmdString\r\nActionID: $actionID\r\n\r\n")) > 0 ) {
    		$rptStatus = AMIget_response($fp, $actionID);
		$resultlist = explode ("\r\n", $rptStatus);
    		return ($resultlist[1]);
	} else {
    		return ("Get node $cmdString failed!");
	}

}

?>
