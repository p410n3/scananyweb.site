<?php
	if(isset($_POST["address"]) ) 
	{ 
		//no timeout possible
		set_time_limit(0);
		ignore_user_abort(true);
		//style
		echo '<body link="#0000CC" style="font-family: arial;padding-top: 20px;color: #00CC00;background-color: #111;text-align: left;" onbeforeunload="return unload()">';
		//Leave page script
		echo '<script>function unload() {return "We do not save any data! Save them yourself if you need to.";}</script>';
		
		$ADDRESS = $_POST["address"];
		$SCANNER = $_POST["scanner"];
		$AGREE = $_POST["tos"];
		$BLOCKED = array('' , 'yourwebsite.xyz'); //well a blacklist obviously
		
		//ToS agreement check
		if($AGREE != '1')
		{
			die('You need to accept the Terms of Service (ToS)');
		}
	
		//only allow alphanumeric chars and dots
		if ( preg_match("/[^a-z0-9.-]/i", $ADDRESS) )
		{
			die('Invalid Input detected. Stopping script.');
		}
		
		//blacklist comparison
		if (in_array(strtolower($ADDRESS), $BLOCKED))
		{
			die('Blacklisted Adddress entered.');	
		}
		
		//localhost only blacklist
		if (fnmatch('127.*', $ADDRESS))
		{
			die('Blacklisted Adddress entered.');	
		}
		
		
		//Set command depending on chosen scan method
		if(strcmp($SCANNER,'dirb')==0)
		{
			$CMD = escapeshellcmd('dirb http://' .$ADDRESS. ' -S -r');
		}
		elseif(strcmp($SCANNER,'whatweb')==0)
		{
			$CMD = escapeshellcmd('whatweb ' .$ADDRESS. ' -v --color never');
		}
		elseif(strcmp($SCANNER,'nmap')==0)
		{
			$CMD = escapeshellcmd('nmap ' .$ADDRESS);
		}
		elseif(strcmp($SCANNER,'whois')==0)
		{
			$CMD = escapeshellcmd('whois ' .$ADDRESS);
		}
		elseif(strcmp($SCANNER,'dnsrecon')==0)
		{
			$CMD = escapeshellcmd('dnsenum ' .$ADDRESS. ' --nocolor');
		}
		
		//hints
		echo 'We do not save scans in any way. Be sure to copy-paste them somewhere!<BR>';
		echo 'Some scans might take a while to finish. Be patient!';
		
		//start command and outpout console
		while (@ ob_end_flush()); // end all output buffers if any

		$proc = popen($CMD, 'r');
		echo '<pre>';
		while (!feof($proc))
		{
			echo fread($proc, 4096);
			@ flush();
		}
		pclose($proc);
		echo '</pre>';
		echo '</body>';
	}
	else
	{
		die('Form data not set.');
	}	
?>
