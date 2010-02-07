<?php
	/*
	Supernova's Supersig Signature Generator
	By Supernova (martin@mfoot.com)
	Originally for www.festersplace.com
	*/

	// Database connectivity settings
	$statserver = 'localhost';
	$statsuser = 'root';
	$statspass = 'toor';
	$statsdb = 'hlstatsx';

		// The cache expiry time for signatures, in seconds
	$cacheExpiryTime = 60*60;

	// The size of the text in the printout
	$size = 12;

	// The colour of the text used in the sigs
	
	// Foreground white, rgb(255,255,255)
	$foregroundr = 255;
	$foregroundg = 255;
	$foregroundb = 255;
	
	// Background black, rgb(0,0,0)
	$backgroundr = 0;
	$backgroundg = 0;
	$backgroundb = 0;
	
	// The server name that is shown in the bottom right of the sigs
	$servername="localhost";
	
	// A friendly name for the server. Used in the generator title as $name Custom Signatures.
	$name = "My Server";

	// The path to the supersigs installation directory
	// Such that sigs directory is $servername/$sigsDir
	$sigsDir = "supersigs/supersigs";
	
	// This is the directory to your hlstatsx:ce stats, so that a link to your player stats can be generated.
	$statsDir = "hlstatsx";
	
	// The game that the server will be running on.
	// Possibilities are:
	//	css		Counter-Strike: Source
	//	hl2mp	Half-Life 2 Multiplayer
	//	tf		Team Fortress 2
	//	hl2ctf	Half-Life 2 Capture the flag
	//	dods		Day of Defeat: Source
	//	insmod	Insurgency: Modern Infantry Combat
	//	ff		Fortress Forever
	//	hidden	The Hidden: Source
	//	zps		Zombie Panic! Source
	//	aoc		Age of Chivalry
	
	// Note, currently only tf, ff, and dods are supported.
	$game = "tf";
	
	// Generate .php links or .jpg links. If you are using jpg links be sure to
	// rename "htaccess" to ".htaccess".
	$linkType = "php";
	
	if(!($db = mysql_connect($statserver, $statsuser, $statspass))) {
		die("Can't connect to the server!");
	}
	else {
		if(!(mysql_select_db($statsdb))) {
			die("Can't connect to the database!");
		}
	}
?>
