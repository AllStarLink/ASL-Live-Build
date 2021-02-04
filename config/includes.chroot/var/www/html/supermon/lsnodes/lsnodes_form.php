<?php

if ( ! file_exists ( "/var/www/html/lsnodes/lsnodes_no_supermon_pw" )) {

 include("../supermon/session.inc");

 if ($_SESSION['sm61loggedin'] !== true) {

    die ("<br><h3>ERROR: You Must login to Supermon to use the lsnodes commands!</h3>");

 }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Allstar Node and Command Entry</title>
<link rel="stylesheet" type="text/css" media="screen" href="css-form.css" />

<!-- lsnodes_form.php to be used in together with lsnodes_web
     Copyright D. Crompton, WA3DSP - 2014, 2015, 2016
-->

</head>

<body>

<script language="JavaScript">

function generateCommand()
    {
      document.getElementById('command').value =
      document.getElementById('command_prefix1').value +
      document.getElementById('command_prefix2').value +
      document.getElementById('tonode').value;
    }

</script>

<?php $fromnode = ($_GET["node"]); ?>

<center>
<p class="header">Node/Command Input</p>
<form method=post action="/cgi-bin/lsnodes_web">

	<p></p>

	<ol>
        
        <li>
        	<label for="node">Local Node</label>
        	<input type="text" name="node" id="node" value="<?php echo $fromnode;?>"/>
        </li>

        <li>
               <label for="tonode">Remote Node</label>
               <input type="text" name="tonode" id="tonode" autocomplete="off" value="" onkeyup="generateCommand()"/>
        </li>

 
<li>

<select name="command_prefix2" id=command_prefix2 autocomplete="off" onchange="generateCommand()">

<!-- Add additional drop down options below -->

<option value="">Enter Command Below or use Dropdown</option>
<option value="*3">Connect</option>
<option value="*73">Connect Permanant</option>
<option value="*1">Disconnect</option>
<option value="*71">Disconnect Permanant</option>
<option value="*2">Monitor Link</option>
<option value="*78">Monitor Link Permanant</option>
<option value="*76">Disconnect ALL Nodes</option>
<option value="*70">System Status</option>
<option value="*51">Macro 1</option>
<option value="*52">Macro 2</option>
<option value="*53">Macro 3</option>
<option value="*54">Macro 4</option>
<option value="*55">Macro 5</option>
<option value="*80">Local Play ID</option>
<option value="*81">Local Play Time</option>
<option value="*82">Local Play 24H Time</option>
 
</select>
</li>

        <li>
                <label for="command_prefix1">Command</label>
                <input type="text" name="command_prefix1" id="command_prefix1" autocomplete="off" value="" onkeyup="generateCommand()" />
        </li>


	 <input type="hidden" name="command" id="command" />
			
		<li id="submit">
        	<button type="submit">Submit</button>
        </li>
				      	
    </ol>

</form>
</center>
<p class="text">
The node number is carried from the display page. A different node number can be entered but it must
reside on the same server you are interrogating. Enter just the node number for status.
</p>
<p class="text">Command is an assigned Allstar function which will be sent to the given node. 
Use the drop down to quickly enter common commands or select "Enter Command Below" and enter a command in
the command window. Some commands require that a "Remote" node be given such as connect and disconnect.
Commands are preceded by a '*' and are the same commands that would be entered
on the radio via DTMF. Example - *340000 - connect to node 40000
</p>
<p class="text">If just the node number is entered the status page for that node is displayed with
a refresh time of 1 minute. If both the node and a valid command are entered the command is executed
and the status page is displayed with an update 5 seconds later. Subsequent refreshes are at 1 minute.
The status page can be manually updated at any time by hitting the browser reload button.  
</p>
<p class="text">
Additional drop down commands can be added by editing this script<br><br>
</p>
</body>
</html>
