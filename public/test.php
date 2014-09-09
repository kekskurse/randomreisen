<?php
ini_set("display_errors", 1);
//Settings
$config["station"]="Berlin HBF";
$config["reisedauer"]=60;
$config["reisedauerPM"]=10; //More or Less Time

//Stuff
$config["min"] = time() + ($config["reisedauer"]-$config["reisedauerPM"]) * 60;
$config["max"] = time() + ($config["reisedauer"]+$config["reisedauerPM"]) * 60;

date_default_timezone_set("Europe/Berlin");
include("../vendor/autoload.php");
$stations = new Lib\Bahn\Stations();
$res  = $stations->searchStations($config["station"]);
if(count($res)==1)
{
	$trains = $stations->nextTrains($config["station"], time());
	foreach($trains as $train)
	{
		#echo "<b>".$train["typ"]." ".$train["nummer"]."</b><br>";
		foreach($train["station"] as $station)
		{
			if($station["timestamp"]>=$config["min"] && $station["timestamp"]<=$config["max"])
			{
				echo "<li>".$train["typ"]." ".$train["nummer"]." nach ".$station["name"]." Ankunft um: ".$station["time"];
			}
		}
	}
	#var_dump($trains);
}
else
{
	var_dump($res);
}
#var_dump($res);