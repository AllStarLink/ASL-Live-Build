<?php
//
// Added IRLP sub-channel information -- KN2R (Copyright) 04/27/2018
// For ham radio use only, NOT for comercial use!
// Major refactoring -- KB4FXC 02/2018
//
error_reporting(0);


function irlp_cache_lookup($irlpnode)
{
	global $irlp_cache;
        global $IRLP_CALLS, $ZCAT, $AWK;

	$lookup = (int)substr("$irlpnode", 1);  // Strips off the leading "8" and leading zeros.

        // ADDED KN2R
        $lookup2 = $lookup;
        $lookup3 = (substr($lookup2, 3, 1));
        // END KN2R

	//`echo irlp_cache_lookup=$lookup >> /tmp/nodexxx`;

	if (isset($irlp_cache[$lookup])) {
		$column = $irlp_cache[$lookup];
		$time = time();
		//$calc = $column[0] - $time;
		//$str = "$time $calc";
		//`echo "irlpcache: $str" >> /tmp/nodexxx`;
		if ($time > $column[0])
			unset ($irlp_cache[$lookup]);

		$info = $column[1] . " [IRLP $lookup] " . $column[2];
		return ($info);

	} else {

                // ADDED KN2R
                if ($lookup >= 9000 && $lookup3 > 0) {
                        $lookup = (substr_replace($lookup, 0, 3));
                }
                // END KN2R

                $res = `$ZCAT $IRLP_CALLS | $AWK '-F|' 'BEGIN{IGNORECASE=1} $1 ~ /$lookup/ {printf ("%s\x18", $0);}'`;

		$table = explode ("\x18", $res);
       		array_pop ($table);

        	foreach ($table as $row) {

                	$column = explode ("|", $row);
                	$node = trim($column[0]);
                	$call = trim($column[1]);
			$qth = trim ($column[2] . ", " . $column[3] . " " . $column[4]);

			$str = $column[0];
			//`echo "irlpnew=$str" >> /tmp/nodexxx`;
			if ($node == $lookup) {
				$column = array();
				$column[0] = time() + 600;
				$column[1] = $call;
				$column[2] = $qth;
				$irlp_cache[$lookup] = $column;

                                // ADDED KN2R
                                if ($lookup >= 9000 && $lookup3 > 0) {
                                        $column[1] = "REF" . "$lookup2";
                                }
				$info = $column[1] . " [IRLP $lookup2] " . $column[2];
                                // END KN2R

				return ($info);
			} else {
				$column = array();
				$column[0] = time() + 30;
				$column[1] = "No info";
				$column[2] = "No info";
				$irlp_cache[$lookup] = $column;

				return ($info);
			}
		}
	}


}


function echolink_cache_lookup($fp, $echonode)
{
	global $elnk_cache;

	$lookup = (int)substr("$echonode", 1);  // Strips off the leading "3" and leading zeros.

	//`echo echolink_cache_lookup=$lookup >> /tmp/nodexxx`;

	if (isset($elnk_cache[$lookup])) {
		$column = $elnk_cache[$lookup];
		$time = time();
		//$calc = $column[0] - $time;
		//$str = "$time $calc";
		//`echo "elnkcache: $str" >> /tmp/nodexxx`;
		if ($time > $column[0])
			unset ($elnk_cache[$lookup]);
		$info = $column[1] . " [EchoLink $lookup] (" . $column[2] . ")";
		return ($info);

	} else {
		$AMI = AMIcommand ($fp, "echolink dbget nodename $lookup");	// Retrieve echolink data from Asterisk
		$rows = explode ("\n", $AMI);

		$column = explode ("|", $rows[0]);
		$str = $column[0];
		//`echo "elnknew=$str" >> /tmp/nodexxx`;
		if ($column[0] == $lookup) {
			$column[0] = time() + 300;
			$elnk_cache[$lookup] = $column;
			$info = $column[1] . " [EchoLink $lookup] (" . $column[2] . ")";
			return ($info);
		} else {
			$column = array();
			$column[0] = time() + 30;
			$column[1] = "No info";
			$column[2] = "No info";
			$elnk_cache[$lookup] = $column;
			$info = $column[1] . " [EchoLink $lookup] (" . $column[2] . ")";
			return ($info);
		}
	}

}

function getAstInfo($fp, $nodeNum) {
    global $astdb;
    //print '<pre>'; print_r($nodeNum); print '</pre>';
	//$time = time();
	//`echo time=$time, node=$nodeNum >> /tmp/nodexxx`;
    
    // Build info string
    if (isset($astdb[$nodeNum])) {
        $dbNode = $astdb[$nodeNum];
        $info = $dbNode[1] . ' ' . $dbNode[2] . ' ' . $dbNode[3];

    // EchoLink and IRLP stuff added by Paul Aidukas, KN2R:
    } elseif ($nodeNum > 3000000) {

        $info = echolink_cache_lookup($fp, $nodeNum);

    } elseif ($nodeNum > 80000) {		// KB4FXC 02/04/2018 -- Was: 40000

	$info = irlp_cache_lookup($nodeNum);

    } elseif (!empty($node['ip'])) {
        if (strlen(trim($node['ip'])) > 3) {
            $info = 'Web Txcvr or Phone Portal (' . $node['ip'] . ')';
        } else {
            $info = 'Unknown Mode';
        }
    } elseif (is_numeric($nodeNum)) {
        $info = 'Node not in database';
    } elseif (`echo $nodeNum |egrep -c "\-P"` > 0) {
        $info = 'Phone Portal';
    } else {
        $info = 'IaxRpt or Web Transceiver';
    }

	//`echo info=$info >> /tmp/nodexxx`;
    return $info;
}

?>
