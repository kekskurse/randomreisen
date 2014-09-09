<?php
ini_set("display_errors", 1);
date_default_timezone_set("Europe/Berlin");
include("../vendor/autoload.php");
include("functions.php");
$libStation = new Lib\Bahn\Stations();
$eCache = new easylib\easycache\easycache();
if(getTextbox("name", "")!="")
{
	if(!$stations = $eCache->get("station_".getTextbox("name", "")))
	{
		$stations = $libStation->searchStations(getTextbox("name", ""));
		$eCache->add("station_".getTextbox("name", ""), $stations, 60*60*2);
	}
	if(count($stations)==1)
	{
		$reqeustid = requestID();
		if(!$treffer = $eCache->get("treffer_".$reqeustid))
		{
			$typ = array();
			if(isset($_GET["ice"]))
			{
				$typ[] = "ICE";
			}
			if(isset($_GET["ic"]))
			{
				$typ[] = "IC";
			}
			if(isset($_GET["d"]))
			{
				$typ[] = "D";
			}
			if(isset($_GET["nv"]))
			{
				$typ[] = "NV";
			}
			$t = mktime($_GET["stunde"], $_GET["minuten"]);
			$trains = $libStation->nextTrains(getTextbox("name", ""), $t, $typ);
			$min = time() + ($_GET["dauer"]-$_GET["pm"]) * 60;
			$max = time() + ($_GET["dauer"]+$_GET["pm"]) * 60;
			$treffer = filter($trains, $min, $max, $_GET["reisedauer"]);
			$eCache->add("treffer_".$reqeustid, $treffer, 60*60);
		}
		#var_dump(count($treffer));
	}
}
?>

<center>
<h1>Bahnreisen Zufall</h1>
<form method="GET" name="suchform">
<b>Startbahnhof:</b><br>
<input name="name" id="name" <?php echo getTextbox("name", "", true); ?>><br>
<b>Zugtypen:</b><br>
<input type="checkbox" name="ice" <?php echo getCheckbox("ice", true, true, "submit"); ?>>ICE 
<input type="checkbox" name="ic" <?php echo getCheckbox("ic", true, true, "submit"); ?>>IC 
<input type="checkbox" name="d" <?php echo getCheckbox("d", false, true, "submit"); ?>>D 
<input type="checkbox" name="nv" <?php echo getCheckbox("nv", false, true, "submit"); ?>>NV<br>
<b>Reisebeginn:</b><br>
<input name="stunde" style="width:40px;" <?php echo getTextbox("stunde", date("H"), true); ?>>:<input name="minuten" style="width:40px;" <?php echo getTextbox("minuten", date("i"), true); ?>><br>
<b>Ankunft in:</b><br>
<input name="dauer" style="width:40" <?php echo getTextbox("dauer", "60", true); ?>>
<select name="pm">
	<option value="5" <?php echo getSelect("pm", "5", "5", true) ?>>+/- 5</option>
	<option value="10" <?php echo getSelect("pm", "10", "5", true) ?>>+/- 10</option>
	<option value="15" <?php echo getSelect("pm", "15", "5", true) ?>>+/- 15</option>
	<option value="20" <?php echo getSelect("pm", "20", "5", true) ?>>+/- 20</option>
	<option value="60" <?php echo getSelect("pm", "60", "5", true) ?>>+/- 60</option>
	<option value="120" <?php echo getSelect("pm", "120", "5", true) ?>>+/- 120</option>
</select> Minuten
<br>
<b>Mindes fahrzeit:</b><br>
<input name="reisedauer" style="width:40" <?php echo getTextbox("reisedauer", "20", true); ?>>Minuten<br>
<input name="submit" type="submit" value="Suchen">
</form>
</center>
<hr>
<?php
if(isset($stations)&&count($stations)>1)
{
	echo "W&auml;hle deinen Bahnhof und dr&uuml;cke erneut auf 'Suchen'<br>";
	foreach($stations as $station)
	{
		?>
		<a href="#" onclick='document.getElementById("name").value="<?php echo $station["value"]; ?>";document.suchform.submit();'><?php echo $station["value"]; ?></a><br>
		<?php
	}
}
?>
<?php
if(isset($treffer))
{
	if(count($treffer)==0)
	{
		echo "<b><center>Keine Verbindung gefunden</center></b>";
	}
	else
	{
		$zufall = rand(0, count($treffer)-1);
		echo "<br><center>Fahre mit dem ".$treffer[$zufall][0]["typ"]." ".$treffer[$zufall][0]["nummer"]." (Abfahrt: ".$treffer[$zufall][0]["abfahrt"].") nach ".$treffer[$zufall][1]["name"]." ankunft um ".$treffer[$zufall][1]["time"]."</center>";
	}
	if(count($treffer)>1)
	{
		echo "<center><a href='#' onclick='location.reload();'>Andere Verbindung</a></center>";
	}
}