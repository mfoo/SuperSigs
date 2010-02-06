<?php
	/*
	Supernova's Supersig Signature Generator
	By Supernova (martin@mfoot.com)
	Originally for www.festersplace.com
	*/

	/************ INCLUDES *************/

	include('config.php');
	include('functions.php');


	/************ VARIABLES ************/

	
	// Array for the co-ordinates of the text positions in the image
	$coordinates = array(
				array("one", 5, 15),
				array("two", 5, 30),
				array("three", 5, 45),
				array("four", 5, 60),
				array("five", 200, 15),
				array("six", 200, 30),
				array("seven", 200, 45),
				array("eight", 200, 60),
				array("nine", 390, 15),
				array("ten", 390, 30), 
				array("eleven",390,45),
				array("twelve",390,60),
				array("thirteen",390,70) // position thirteen is used for the server name
				);

	/************** BODY ***************/

	// Check if they entered an ID, if they didn't, quit
	if(!isset($_GET['id']))
		die("Please enter a player name or ID!");
	
	$playerID = mysql_real_escape_string($_GET['id']);

	mysql_query("SET NAMES 'utf8'");
	
	// If they entered a text string, assume it was the player name and grab their ID
	$serverId = mysql_real_escape_string($_GET['sId']);
	$game = mysql_fetch_array(mysql_query("SELECT game FROM hlstats_Servers WHERE serverId = " . $serverId));
	$game = $game[0];
	
	// If they have posted a text name from the generator, link it to an id
	if(!is_numeric($playerID)){
		// Get the ID of the player that uses that name the most then find out what the last name they used in game $game was.
		$data = mysql_fetch_array(mysql_query("SELECT playerId FROM hlstats_Players WHERE playerId = (SELECT playerId FROM hlstats_PlayerNames WHERE name LIKE '%" . $playerID . "%' ORDER BY numuses DESC LIMIT 1) AND game = '" . $game . "'"));
		if(is_null($data)) 
			die("Invalid player name!");

		$playerID = $data['playerId'];
	}

	// If they are generating the sig, set the cache expiry time to zero - forcing a redraw for the sig demo pic
	if(isset($_GET['generate']))
		$cacheExpiryTime = 0;

	// Check if a cache of their sig exists already
	$cacheExists = checkCache($playerID);

	// If a file exists but is too old, or it does not exist then create the new file
	if($cacheExists && getFileAge($playerID) > $cacheExpiryTime || !$cacheExists){
		$result = mysql_query("SELECT name FROM hlstats_PlayerNames WHERE playerId = " . $playerID . " ORDER BY numuses DESC");
		if($result==false)
			die("Player does not exist! Try a different name.");
	}

	// If the file is still cached, redirect to it
	else {
		header("Location: sigs/$playerID.png"); 
		die(); // not needed
	}
	
	// Begin making the signature
	
	// Get their name
	$data = mysql_fetch_array($result);
	$playerName = $data['name'];

	// Choose a random background picture, weighted by the number of times they pick each class
	$background=isset($_GET['background'])?$_GET['background']:"";
	
	if(empty($background) || $background=="Random")
		$backgroundPath = getRandomPicture($playerID, $game);
	else
		$backgroundPath = getSetBackgroundPicture($playerID, $background, $game);
		
	$background = imagecreatefromjpeg("images/" . $game . "/" . $backgroundPath);

	// Load the transparent overlay into an image resource
	$overlay = imagecreatefrompng("img/overlay.png");
	imagealphablending($background, true);	
	
	// Set the transparent colour as the colour at pixel 0,0 of the overlay
	imagecolortransparent($overlay, imagecolorat($overlay, 0, 0));
	//imagecolortransparent($overlay, ffffff);

	// Create some default colours for the text
	$canvas = imagecreatetruecolor(1, 1);
	$black = imagecolorallocate($canvas, $backgroundr, $backgroundg, $backgroundb);
	$white = imagecolorallocate($canvas, $foregroundr, $foregroundg, $foregroundb);

	// Apply the semi-transparent overlay to the background image
	imagecopy($background, $overlay, 0, 0, 0, 0, imagesx($overlay), imagesy($overlay));

	// Add the 1px wide rectangle around the edge
	imagerectangle($background, 0, 0, imagesx($overlay)-1, imagesy($overlay)-1, $black);

	$font=isset($_GET['font'])?$_GET['font']:'FreeMono';

	// Draw everything they want, use mysql_real_escape_string() to sanitise
	if(isset($_GET['one'])){  
		$background = drawText($playerID, $serverId, $game, "one", $background, $coordinates[0][1], $coordinates[0][2], mysql_real_escape_string($_GET['one']), $black, $white, $size, $font);
	}

	if(isset($_GET['two'])){
		$background = drawText($playerID, $serverId, $game, "two", $background, $coordinates[1][1], $coordinates[1][2],  mysql_real_escape_string($_GET['two']), $black, $white, $size, $font);
	}

	if(isset($_GET['three'])){
		$background = drawText($playerID, $serverId, $game, "three", $background, $coordinates[2][1], $coordinates[2][2],  mysql_real_escape_string($_GET['three']), $black, $white, $size, $font);
	}

	if(isset($_GET['four'])){
		$background = drawText($playerID, $serverId, $game, "four", $background, $coordinates[3][1], $coordinates[3][2],  mysql_real_escape_string($_GET['four']), $black, $white, $size, $font);
	}

	if(isset($_GET['five'])){
		$background = drawText($playerID, $serverId, $game, "five", $background, $coordinates[4][1], $coordinates[4][2],  mysql_real_escape_string($_GET['five']), $black, $white, $size, $font);
	}
	
	if(isset($_GET['six'])){
		$background = drawText($playerID, $serverId, $game, "six", $background, $coordinates[5][1], $coordinates[5][2],  mysql_real_escape_string($_GET['six']), $black, $white, $size, $font);
	}

	if(isset($_GET['seven'])){
		$background = drawText($playerID, $serverId, $game, "seven", $background, $coordinates[6][1], $coordinates[6][2],  mysql_real_escape_string($_GET['seven']), $black, $white, $size, $font);
	}

	if(isset($_GET['eight'])){
		$background = drawText($playerID, $serverId, $game, "eight", $background, $coordinates[7][1], $coordinates[7][2],  mysql_real_escape_string($_GET['eight']), $black, $white, $size, $font);
	}

	if(isset($_GET['nine'])){
		$background = drawText($playerID, $serverId, $game, "nine", $background, $coordinates[8][1], $coordinates[8][2],  mysql_real_escape_string($_GET['nine']), $black, $white, $size, $font);
	}
	
	if(isset($_GET['ten'])){
		$background = drawText($playerID, $serverId, $game, "ten", $background, $coordinates[9][1], $coordinates[9][2],  mysql_real_escape_string($_GET['ten']), $black, $white, $size, $font);
	}
	
	if(isset($_GET['eleven'])){
		$background = drawText($playerID, $serverId, $game, "eleven", $background, $coordinates[10][1], $coordinates[10][2],  mysql_real_escape_string($_GET['eleven']), $black, $white, $size, $font);
	}
	
	if(isset($_GET['twelve'])){
		$background = drawText($playerID, $serverId, $game, "twelve", $background, $coordinates[11][1], $coordinates[11][2],  mysql_real_escape_string($_GET['twelve']), $black, $white, $size, $font);
	}
	

	// Set the text size for the server name text
	$size = 8;
	$background = drawText($playerID, $serverId, $game, "thirteen", $background, $coordinates[12][1], $coordinates[12][2],  $servername, $black, $white, $size, $font);

	ob_start();
	
	// If they're generating the image, set the content headers and give the browser the new image. This stops people who test new sigs overwriting their current one.
	if(isset($_GET['generate'])){
		header("Content-type: image/png");
		imagepng($background);
	}
	
	// If they are updating their sig, write the sig image to a file cache then point the browser to it
	else{
		imagepng($background, "sigs/" . $playerID . ".png");
		chmod("sigs/" . $playerID . ".png", 0644);
		header('Location: sigs/' . $playerID . '.png');
	}	
	
	ob_end_flush();
	
	// Destroy the image resources to free up memory
	imagedestroy($overlay);
	imagedestroy($background);
	imagedestroy($canvas);
	mysql_close($db);
?>
