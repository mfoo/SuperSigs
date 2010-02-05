<?php
	/*
	Supernova's Supersig Signature Generator
	By Supernova (martin@mfoot.com)
	Originally for www.festersplace.com
	*/

	/* Function checkCache takes a filename and returns whether it exists or not*/
	function checkCache($filename){
		return file_exists("sigs/".$filename.".png");
	}

	/* Function getModifiedTime takes a filename and returns the unix timestamp of when the file was last modified*/
	function getModifiedTime($filename){
		return filemtime($filename);
	}

	/* Function getFileAge takes a filename and returns it's age in seconds (the time since it was last modified)*/
	function getFileAge($filename){
		return time() - getModifiedTime("sigs/".$filename.".png");
	}

	/* Function getRandomPicture takes a player ID and returns the path to a signature background. The class chosen is generated 
	from a weighted random function. I.e. the class that the player chooses most will appear more often. The path of the image returned
	is the path to a file chosen at random from a folder containing the randomly chosen class' background images.*/
	function getRandomPicture($playerID, $game){
		// Get the total number of class changes the player has made
		$totalChanges = mysql_num_rows(mysql_query("SELECT * FROM `hlstats_Events_ChangeRole` WHERE playerId = " . $playerID));
		
		// Get a table containing the name of each class and the number of times they've changed to it from the DB
		$data = mysql_query("SELECT role, Count(role) as rolecount FROM `hlstats_Events_ChangeRole` WHERE playerId = " . $playerID . " AND eventTime > " . (time() - ( 60 * 60 * 24 * 30) ) . " GROUP BY role ORDER BY rolecount DESC");
		
		// Calculate the class
		$random = rand(1,100) / 100;
		while($classname = mysql_fetch_array($data)){
			if(($classname['rolecount'] / $totalChanges) < $random ){
				$role = $classname['role']; 
				break;
			}
		}

		// If the random number turned out to be too low, pick the most picked class
		if(is_null($role)){
			$data = mysql_fetch_array(mysql_query("SELECT role, Count(role) as rolecount FROM `hlstats_Events_ChangeRole` WHERE playerId = " . $playerID . " AND eventTime > " . (time() - ( 60 * 60 * 24 * 30) ) . " AND serverId = " . $serverId . " GROUP BY role ORDER BY rolecount DESC LIMIT 1"));
			$role = $data['role'];
		}

		// Set up index variable
		$i = 0;
		
		// Get the list of all files in the images directory for that class
		$files = opendir("images/" . $game . "/" . $role);
		while($file = readdir($files)){
			// readdir returns all files, in unix this includes current and parent directories, so we have to exclude "." and "..".
			if($file != '.' && $file != '..'){
				// Note: $x++ is post increment
				$results[$i++] = $file;
			}
		}
		
		// Housekeeping
		closedir($files); 
		
		// Choose a random filename from the list of files
		$picture = rand(1,count($results));
		return $role ."/" . $picture . ".jpg";
	}

	function getSetBackgroundPicture($playerID, $role, $game){
		$i=0;
		$files = opendir("images/" . $game . "/" . $role);
		while($file = readdir($files)){
			// readdir returns all files, in unix this includes current and parent directories, so we have to exclude "." and "..".
			if($file != '.' && $file != '..'){
				$i++;
			}
		}
		
		// Housekeeping
		closedir($files); 
		
		// Choose a random filename from the list of files
		$picture = rand(1,$i);
		return $role ."/" . $picture . ".jpg";
	}

	/* Function drawText takes a player ID, a type of data to draw, an image resource to draw onto, x and y coordinates for the bottom left
	of the text to be placed, the background and foreground colour, and the size of the font, gathers the correct data from the DB, then 
	draws the text. */
	function drawText($playerID, $serverId, $game, $type, $image, $x, $y, $text, $background, $foreground, $size, $font){
		if($font=="") $font="visitor1";
		$font = "fonts/".$font.".ttf";
		
		// Fallback to freemono if the font doesn't exist
		if(!file_exists($font)){
			$font = "fonts/FreeMono.ttf";
		}

		// Get the text string to print for each of the types
		switch($text){
			case "name":
				$data = mysql_fetch_array(mysql_query("SELECT name FROM hlstats_PlayerNames WHERE playerId = " . $playerID . " ORDER BY kills DESC"));
				$text = $data['name'];
				break;
			case "kpd":
				$kills = mysql_fetch_array(mysql_query("SELECT Count(*) as kills FROM hlstats_Events_Frags WHERE killerId = " . $playerID . " AND serverId = ". $serverId . " LIMIT 1"));
				$kills = $kills['kills'];
				$deaths = mysql_fetch_array(mysql_query("SELECT Count(*) as deaths FROM hlstats_Events_Frags WHERE victimId = " . $playerID . " AND serverId = ". $serverId . " LIMIT 1"));
				$deaths = $deaths['deaths'];
				$text = "KPD: " . number_format($kills/$deaths,2);
				break;
			case "kills":
				$data = mysql_fetch_array(mysql_query("SELECT Count(*) as kills FROM hlstats_Events_Frags WHERE killerId = " . $playerID . " AND serverId = ". $serverId . " LIMIT 1"));
				$text = "Kills: " . $data['kills'];
				break;
			case "deaths":
				$data = mysql_fetch_array(mysql_query("SELECT Count(*) as deaths FROM hlstats_Events_Frags WHERE victimId = " . $playerID . " AND serverId = ". $serverId . " LIMIT 1"));
				$text = "Deaths: " . $data['deaths'];
				break;
			case "points":
				$data = mysql_fetch_array(mysql_query("SELECT skill FROM hlstats_Players WHERE playerId = " . $playerID . " AND game = '" . $game . "'"));
				$text = "Skill: " . $data['skill'];
				break;
			case "headshots":
				$data = mysql_fetch_array(mysql_query("SELECT COUNT(headshot) AS headshots FROM hlstats_Events_Frags WHERE killerId = " . $playerID . " GROUP BY headshot ORDER BY headshots ASC LIMIT 1"));
				$text = "Headshots: " . $data['headshots'];
				break;
			case "rank":
				$data = mysql_query("SELECT playerId FROM hlstats_Players WHERE game = '". $game . "' ORDER BY skill DESC");
				$i = 0;
				$found = false;
				
				while(($temp = mysql_fetch_array($data)) && ($found == false)){
					$i++;

					if($temp[0] == $playerID){
						// If it's the correct player
						break;
					}
				}
				$text = "Rank: " . $i;
				break;
			case "favweapon":
				$data = mysql_fetch_array(mysql_query("SELECT weapon FROM hlstats_Events_Frags WHERE killerId = " . $playerID . " AND serverId = " . $serverId . " GROUP BY weapon ORDER BY Count(weapon) DESC LIMIT 1"));
				$weaponname = mysql_fetch_array(mysql_query("SELECT name FROM hlstats_Weapons WHERE code = '" . $data['weapon'] . "'")); 
				$text = "Fav Weapon: " . $weaponname['name'];
				break;
			case "favteam":
				$data = mysql_fetch_array(mysql_query("SELECT team FROM hlstats_Events_ChangeTeam WHERE playerId = " . $playerID . " AND serverId = " . $serverId . " AND team != 'Unassigned' GROUP BY team ORDER BY Count(team) DESC LIMIT 1"));
				$text = "Fav Team: " . $data['team'];
				break;
			case "favclass":
				$data = mysql_fetch_array(mysql_query("SELECT role FROM hlstats_Events_ChangeRole WHERE playerId = " . $playerID  . "  AND serverId = " . $serverId . " GROUP BY role ORDER BY Count(role) DESC LIMIT 1"));
				$text = "Fav Class: " . $data['role'];
				break;
			case "favvictim":
				$data = mysql_fetch_array(mysql_query("SELECT victimId FROM hlstats_Events_Frags WHERE killerId = " . $playerID . "  AND serverId = " . $serverId . " GROUP BY victimId ORDER BY Count(victimId) DESC"));
				$data = mysql_fetch_array(mysql_query("SELECT name FROM hlstats_PlayerNames WHERE playerId = '" . $data['victimId'] . "' ORDER BY numuses DESC LIMIT 1"));
				$text = "Fav Victim: " . $data['name'];
				break;
			case "suicides":
				$data = mysql_fetch_array(mysql_query("SELECT COUNT(playerId) as count FROM hlstats_Events_Suicides WHERE playerId = " . $playerID . " AND serverId = " . $serverId));
				$text = "Suicides: " . $data['count'];
				break;	
			case "sandvich":
				$data = mysql_fetch_array(mysql_query("SELECT Count(actionId) AS count FROM `hlstats_Events_PlayerActions` WHERE actionId = (SELECT id FROM `hlstats_Actions` WHERE code = 'sandvich' AND game = '" . $game . "') AND playerId = " . $playerID . ") AND serverId = " . $serverId));
				$text = "Sandviches eaten: " . $data['count'];
				break;
			// Shots don't get recorded by TF2 server, only for games like CSS :(
			/*case "shots":
				$data = mysql_fetch_array(mysql_query("SELECT shots FROM hlstats_Players WHERE playerId = " . $playerID));
				$text = "Shots fired: " . $data['shots'];
				break; */ 
			case "recentaward":
				$data = mysql_fetch_array(mysql_query("SELECT awardId FROM hlstats_Players_Awards WHERE playerId =" . $playerID . " AND game = '" . $game . "' ORDER BY awardTime DESC"));
				$data = mysql_fetch_array(mysql_query("SELECT verb FROM hlstats_Awards WHERE awardId = " . $data['awardId']));
				$text = "Most recent award: " . $data['verb'];
				break;		
			case "averageping":
				$data = mysql_query("SELECT ping FROM hlstats_Events_Latency WHERE playerId = " . $playerID . " AND serverId = " . $serverId);
				$text = 0;
				$count = 0;
				while($ping = mysql_fetch_array($data)){
					$text += $ping['ping'];
					$count++;
				}
				$text = "Average ping: " . floor($text / $count);
				break;
			case "country":
				$data = mysql_fetch_array(mysql_query("SELECT country FROM hlstats_Players WHERE playerId = " . $playerID));
				$text = $data['country'];
				break;
			case "killstreak":
				$data = mysql_fetch_array(mysql_query("SELECT kill_streak FROM hlstats_Players WHERE playerId = " . $playerID . " AND game = '" . $game . "'"));
				$text = "Kill Streak: " . $data['kill_streak'];
				break;
			case "serverrank":
				$data = mysql_fetch_array(mysql_query("SELECT kills FROM hlstats_Players WHERE playerID = " . $playerID . " AND game = '" . $game . "' ORDER BY kills DESC"));
				$data = $data['kills'];
				$data = mysql_fetch_array(mysql_query("SELECT rankName FROM hlstats_Ranks WHERE maxKills <= " . $data . " AND game = '" . $game . "' ORDER BY minKills DESC LIMIT 1"));
				$text = "Rank: " . $data['rankName'];
				break;
			case "time":
				$data = mysql_fetch_array(mysql_query("SELECT Sum(connection_time) AS sum from hlstats_Players_History WHERE playerId =  " . $playerId . " AND game = '" . $game . "' GROUP BY playerId"));
				$data = $data['sum'];
				$days = floor($data / (60*60*24));
				$hours = floor(($data - ($days * 60 * 60 * 24)) / (60*60));
				$text = "Connection time: " . $days . " days ". $hours . " hours";
				break;
			case "sentries":
				$data = mysql_fetch_array(mysql_query("SELECT Count(actionId) AS count FROM `hlstats_Events_PlayerActions` WHERE actionId = (SELECT id FROM hlstats_Actions WHERE code = 'builtobject_obj_sentrygun' AND game = '" . $game . "') AND playerId = " . $playerID));
				$text = "Sentries built: " . $data['count'];
				break;
			case "assists":
				$data = mysql_fetch_array(mysql_query("SELECT Count(actionId) AS count FROM `hlstats_Events_PlayerActions` WHERE actionId = (SELECT id FROM hlstats_Actions WHERE code = 'kill assist' AND game = '" . $game . "') AND playerId = " . $playerID));
				$text = "Assists: " . $data['count'];
				break;
			case "dominations":
				$data = mysql_fetch_array(mysql_query("SELECT Count(actionId) AS count FROM `hlstats_Events_PlayerActions` WHERE actionId = (SELECT id FROM hlstats_Actions WHERE code = 'domination' AND game = '" . $game . "') AND playerId = " . $playerID));
				$text = "Dominations: " . $data['count'];
				break;	
			case "medicassist":
				$data = mysql_fetch_array(mysql_query("SELECT Count(actionId) AS count FROM `hlstats_Events_PlayerActions` WHERE actionId = (SELECT id FROM hlstats_Actions WHERE code = 'kill_assist_medic' AND game = '" . $game . "') AND playerId = " . $playerID));
				$text = "Kill Assists - Medic: " . $data['count'];
				break;	
			case "ubers":
				$data = mysql_fetch_array(mysql_query("SELECT Count(actionId) AS count FROM `hlstats_Events_PlayerActions` WHERE actionId = (SELECT id FROM hlstats_Actions WHERE code = 'chargedeployed' AND game = '" . $game . "') AND playerId = " . $playerID));
				$text = "Ubers deployed: " . $data['count'];
				break;	
			case "wrenchkills":
				$data = mysql_fetch_array(mysql_query("SELECT COUNT(weapon) as count FROM hlstats_Events_Frags WHERE weapon='wrench' AND killerId = " . $playerID . " AND serverId = " . $serverId . " GROUP BY weapon ORDER BY weapon DESC"));
				$text = "Wrench kills: " . $data['count'];
				break;
			case "scorechange":
				$data = mysql_fetch_array(mysql_query("SELECT skill, last_skill_change FROM hlstats_Players WHERE playerId = " . $playerID . " AND game = '" . $game . "'"));
				$text = "Skill: " . $data['skill'] . " (";
				
				// add a "+" if they skill increased
				if($data['last_skill_change'] > 0)
					$text .= "+";
					
				$text .= $data['last_skill_change'] . ")";
				break;
		}
		
		$textn = preg_replace("/\\\/","",$text);
		
		$textDimensions = imagettfbbox($size, 0, $font, $text);
		// If they are middle areas, center align them
		if($type == "five" || $type == "six" || $type == "seven" || $type == "eight")
			$x = imagesx($image)/2 - ($textDimensions[2] - $textDimensions[0])/2;
		elseif($type == "nine" || $type == "ten" || $type == "eleven" || $type == "twelve" || $type == "thirteen")
			$x = imagesx($image) - 5 - ($textDimensions[2] - $textDimensions[0]);

		// Draw the image
		imagettftext($image, $size, 0, $x,$y-1, $background, $font, $text);
		imagettftext($image, $size, 0, $x,$y+1, $background, $font, $text);
		imagettftext($image, $size, 0, $x+1,$y, $background, $font, $text);
		imagettftext($image, $size, 0, $x-1,$y, $background, $font, $text);
		imagettftext($image, $size, 0, $x,$y, $foreground, $font, $text);

		return $image;
	}

?>
