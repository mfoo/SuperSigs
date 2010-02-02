<?php
	/*
	Supernova's Supersig Signature Generator Page
	By Supernova (martin@mfoot.com)
	Originally for www.festersplace.com
	*/

	include('config.php');
	
	/* Function printLots takes a position number from the list of positions in the image, and makes a dropdown box for it. */
	function printLots($name){
		echo "\t<td>\n";
		echo "\t\t<p>What do you want in position " . $name . "?</p>\n";
		echo "\t\t<select name=\"" . $name . "\" onchange=\"toggleCustom('" . $name . "');\">\n";
		// If they have selected custom text and haven't entered any, stop it from being redrawn as "custom".$name here.
		if(($_POST[$name]=="custom".$name) && ($_POST['custom'.$name]==""))
			echo "\t\t\t<option value=\"\">Nothing</option>\n";
		else{
			echo "\t\t\t<option value=\"" . $_POST[$name] . "\">" . $_POST[$name] . "</option>\n";
			echo "\t\t\t<option value=\"\">Nothing</option>\n";
		}
		
		echo "\t\t\t<option value=\"custom".$name."\">Custom Text</option>\n";
		echo <<<END
			<option value="name">Name</option>
			<option value="kills">Kills</option>
			<option value="deaths">Deaths</option>
			<option value="kpd">KPD</option>
			<option value="points">Points</option>
			<option value="scorechange">Points + Skill Change</option>
			<option value="headshots">Headshots</option>
			<option value="rank">Rank</option>
			<option value="favweapon">Favourite Weapon</option>
			<option value="favteam">Favourite Team</option>
			<option value="favvictim">Favourite Victim</option>
			<option value="suicides">Suicides</option>
			<option value="sandvich">Sandviches Eaten</option>
			<option value="recentaward">Most Recent Award</option>
			<option value="averageping">Average Ping</option>
			<option value="country">Country</option>
			<option value="killstreak">Longest Kill Streak</option>
			<option value="serverrank">Server Rank Name</option>
			<option value="time">Connection Time</option>
			<option value="sentries">Number Of Sentries Built</option>
			<option value="assists">Number Of Assists</option>
			<option value="dominations">Number Of Dominations</option>
			<option value="medicassist">Medic Assists</option>
			<option value="ubers">Number Of Ubers Deployed</option>
			<option value="wrenchkills">Number Of Wrench Kills</option>
		</select>
END;
		echo "\n\t\t<div id=\"custom" . $name . "div\" style=\"display: ";
		echo $_POST[$name]!="custom".$name ? "none" : "block";			
		echo ";\">";
		echo "<input type=\"text\" name=\"custom".$name."\" value=\"" . $_POST['custom'.$name] . "\"/>";
		echo "</div>";
		echo "\t</td>\n";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
		<meta name="description" content="Supernova's Supersigs Signature Generator"/>
		<meta name="keywords" content="signature generator hlstats"/> 
		<meta name="author" content="Martin Foot"/> 
		<title>Supernova's Super Sigs for HLstats Community Edition</title>
		<link rel="stylesheet" type="text/css" href="styles.css" media="screen"/>
		<script src="jquery.js" type="text/javascript"></script>
		<script type="text/javascript">
			function toggleCustom(id) {
				if(document.getElementsByName(id)[0].value == "custom" + id){
					document.getElementById("custom" + id + "div").style.display = 'block';
				}
				else {
					document.getElementById("custom" + id + "div").style.display = 'none';
				}
			}
						
			function getImageUrl(){
				// Grab the custom text field instead of "custom<X>" if that is selected.
				// TODO: Clean this up
				return "index.php?generate=true&id=" + 
					$("input[name=id]").val() + "&one=" +
					($("select[name=one]").val() == "customone" ?  $("input[name=customone]").val() : $("select[name=one]").val()) + "&two=" +
					($("select[name=two]").val() == "customtwo" ? $("input[name=customtwo]").val() : $("select[name=two]").val()) + "&three=" + 
					($("select[name=three]").val() == "customthree" ? $("input[name=customthree]").val() : $("select[name=three]").val()) + "&four=" + 
					($("select[name=four]").val() == "customfour" ? $("input[name=customfour]").val() : $("select[name=four]").val()) + "&five=" +
					($("select[name=five]").val() == "customfive" ? $("input[name=customfive]").val() : $("select[name=five]").val()) + "&six=" +
					($("select[name=six]").val() == "customsix" ? $("input[name=customsix]").val() : $("select[name=six]").val()) + "&seven=" +
					($("select[name=seven]").val() == "customseven" ? $("input[name=customseven]").val() : $("select[name=seven]").val()) + "&eight=" +
					($("select[name=eight]").val() == "customeight" ? $("input[name=customeight]").val() : $("select[name=eight]").val()) + "&nine=" +
					($("select[name=nine]").val() == "customnine" ? $("input[name=customnine]").val() : $("select[name=nine]").val()) + "&ten=" +
					($("select[name=ten]").val() == "customten" ? $("input[name=customten]").val() : $("select[name=ten]").val()) + "&eleven=" +
					($("select[name=eleven]").val() == "customeleven" ? $("input[name=customeleven]").val() : $("select[name=eleven]").val()) + "&twelve=" +
					($("select[name=twelve]").val() == "customtwelve" ? $("input[name=customtwelve]").val() : $("select[name=twelve]").val()) + "&font=" +
					$("select[name=font]").val() + "&background=" +
					$("select[name=background]").val();
			}
			
			function reloadImage(){
				var url = getImageUrl();
				var image = $("#sig");

				image.fadeOut(function(){
					// Change the image source then the user clicks something
					$(this).attr("src", url);
				});
			
				image.load(function(){
					// When the image has loaded, fade back in again
					$(this).fadeIn();
					
					// Tell the person to submit the form (Can't get the player name from
					// the db without sending it to the php
					$("textarea[name=output]").val("Please press Submit to generate a new signature code.");
				});
			}
	
			$(document).ready(function(){
				// Tell all of the select boxes to reload the image on change
				$("select").change(reloadImage);
			
				// Same for the custom input boxes
				$("input[type=text]").blur(reloadImage);
			});
	
			
		</script>
	</head>
	<body style="margin: auto; width: 870px;">

	<div class="container">

		<div class="header">
			<a href="generate.php"><span><?php echo $name ?> Custom Signatures</span></a>
		</div>

		<div class="stripes"><span></span></div>
		
		<div class="nav">
			<a href="generate.php">Generate</a>
			<a href="help.php">Help</a>
			<a href="version.php">Version</a>
			<div class="clearer"><span></span></div>
		</div>

		<div class="stripes"><span></span></div>

		<div class="main">
		
			<div class="left" style="width: 100%;">

				<div class="content">

					<h1>Welcome to the Generator</h1>
					<div class="descr">Feb 02, 2010 by Supernova</div>

					<p>Welcome to the custom signature generator.</p>
				
					<p>Select the options below. An example image with placings can be seen here:</p>
					<p><img src="img/positions.png" alt="positions"/></p>
		
					<?php
							if(isset($_POST['id'])){
							// If they've supplied an ID, we will need the database connection
							include('config.php');

							mysql_query("SET NAMES 'utf8'");
							
							// Get their player ID
							$data = mysql_fetch_array(mysql_query("SELECT playerId FROM hlstats_PlayerNames WHERE name = '" .mysql_real_escape_string($_POST['id']) . "' ORDER BY numuses DESC"));

							// Delete their old sig
							if(file_exists("sigs/".$data['playerId'].".png"))
								unlink("sigs/".$data['playerId'].".png");
							
							// Generate the path to their new sig
							$url="";
							if($_POST['one']=="customone") $url .=  "&one=".urlencode($_POST['customone']);
								elseif($_POST['one']!="") 	$url .=  "&one=".urlencode($_POST['one']);
							if($_POST['two']=="customtwo") $url .= "&two=".urlencode($_POST['customtwo']);
								elseif($_POST['two']!="") $url .=  "&two=".urlencode($_POST['two']);
							if($_POST['three']=="customthree") $url .= "&three=".urlencode($_POST['customthree']);
								elseif($_POST['three']!="") $url .=   "&three=".urlencode($_POST['three']);
							if($_POST['four']=="customfour") $url .=   "&four=".urlencode($_POST['customfour']);
								elseif($_POST['four']!="") $url .=   "&four=".urlencode($_POST['four']);
							if($_POST['five']=="customfive") $url .=   "&five=".urlencode($_POST['customfive']);
								elseif($_POST['five']!="") $url .=   "&five=".urlencode($_POST['five']);
							if($_POST['six']=="customsix") $url .=   "&six=".urlencode($_POST['customsix']);
								elseif($_POST['six']!="") $url .=   "&six=".urlencode($_POST['six']);
							if($_POST['seven']=="customseven") $url .=   "&seven=".urlencode($_POST['customseven']);
								elseif($_POST['seven']!="") $url .=   "&seven=".urlencode($_POST['seven']);
							if($_POST['eight']=="customeight") $url .=   "&eight=".urlencode($_POST['customeight']);
								elseif($_POST['eight']!="") $url .=   "&eight=".urlencode($_POST['eight']);
							if($_POST['nine']=="customnine") $url .=   "&nine=".urlencode($_POST['customnine']);
								elseif($_POST['nine']!="") $url .=   "&nine=".urlencode($_POST['nine']);
							if($_POST['ten']=="customten") $url .=   "&ten=".urlencode($_POST['customten']);
								elseif($_POST['ten']!="") $url .=   "&ten=".urlencode($_POST['ten']);
							if($_POST['eleven']=="customeleven") $url .=   "&eleven=".urlencode($_POST['customeleven']);
								elseif($_POST['eleven']!="") $url .=   "&eleven=".urlencode($_POST['eleven']);
							if($_POST['twelve']=="customtwelve") $url .=   "&twelve=".urlencode($_POST['customtwelve']);
								elseif($_POST['twelve']!="") $url .=   "&twelve=".urlencode($_POST['twelve']);
								
							// If they dont set a font, index.php will use the default one (visitor1.ttf)
							if($_POST['font']!="") $url .= "&font=".$_POST['font'];

							if($_POST['background'] != "")
								$url .= "&background=" . $_POST['background'];
							else
								$url .= "&background=Random";
				
							// Output the text
							echo "<p>Your sig can be seen here:</p>\n\n<p style=\"height: 75px;\"><img src=\"index.php?generate=true&id=" . urlencode($_POST['id']) . $url ."\" alt=\"signature\" id=\"sig\"/>\n</p>";
							echo "<p>Your signature code is:</p>\n";
							echo "<form name=\"copytext\">";
							echo "<textarea name=\"output\" rows=\"3\" cols=\"50\" wrap=\"on\">";
							echo "[url=http://" . $servername . "/" . $statsDir . "/hlstats.php?mode=playerinfo&player=" . $data['playerId'] . "][img]http://" . $servername . "/" . $sigsDir . "/index.php?id=". urlencode($_POST['id']) . $url . "[/img][/url]";
							echo "</textarea>";
							echo "<br />\n<input type=\"button\" value=\"Select all\" onchange=\"this.form.output.focus();this.form.output.select();copied=document.selection.createRange();copied.execCommand('copy');\">";
							echo "</form>";
							echo "<p>\nJust copy it into your forum signature page :) Unhappy with this? Just try again below:</p>";
						}
						
						// Start the output of the form
						echo "<form method=\"post\" action=\"\">\n";
						echo "<table>\n";
						
						// Populate the table
						echo "<tr>";
							printLots("one");
							printLots("five");
							printLots("nine");
						echo "</tr>";
						
						echo "<tr>";
							printLots("two");
							printLots("six");
							printLots("ten");
						echo "</tr>";

						echo "<tr>";
							printLots("three");
							printLots("seven");
							printLots("eleven");
						echo "</tr>";

						echo "<tr>";
							printLots("four");
							printLots("eight");
							printLots("twelve");
						echo "</tr>\n";

						echo "</table>\n";
						echo "<p>What font would you like?</p>";
						echo "<p><select name=\"font\">";
						
						// If they chose a font, then put that font as the first option in the list.
						$fontUsed = $_POST['font'];
						
						if($fontUsed!="") echo "<option value=\"" . $fontUsed . "\">" . $fontUsed . "</option>\n";
						
						// Add all the other fonts to the dropdown
						$dir = opendir("fonts/");
						
						while($file=readdir($dir)){
							if(substr($file,strlen($file)-4)==".ttf")
								if($file != $fontUsed . ".ttf")
									echo "<option value=\"" . str_replace(".ttf","",$file) . "\">".$file."</option>";
						}
						
						echo "</select></p>";
						
						echo "<p>What background would you like? Note: \"random\" means a weighted average, the class that you pick most often will appear the most.</p>";
						echo "<p><select name=\"background\">";
						
						// If they chose a background, set that as the default
						$backgroundChosen = $_POST['background'];
						if($backgroundChosen!="") echo "<option value=\"" . $backgroundChosen . "\">" . $backgroundChosen . "</option>\n";
						if($backgroundChosen !="Random") echo "<option name=\"random\">Random</option>";
						
						// Add all the other backgrounds
						$dir=opendir("images/" . $game . "/");
						while($file=readdir($dir)){
							if($file!="." && $file!=".." && $file != $backgroundChosen){
								if(strpos($file,".")==false)
									echo "<option value=\"" . $file. "\">".$file."</option>";
							}
						}
						
						echo "</select></p>";
						
						echo "<p>What is your player name?</p>\n<p><input type=\"text\" name=\"id\" value=\"" . $_POST['id'] . "\"/></p>\n
							<p><input type=\"submit\" value=\"Submit\" name=\"submit\"/></p>\n";
						echo "</form>\n";
						
				?>
								
			</div>

		</div>

		<div class="clearer"><span></span></div>

	</div>

	<div class="footer">
	
		<div class="bottom">
			
			<span class="left">&copy; 2009 <a href="http://www.supersigs.mfoot.com/">mfoot.com</a>. Valid <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a> &amp; <a href="http://validator.w3.org/check?uri=referer">XHTML</a>.</span>
			<span class="right">Template design by <a href="http://templates.arcsin.se">Arcsin</a></span>

			<div class="clearer"><span></span></div>

		</div>

	</div>

</div>

</body>

</html>
