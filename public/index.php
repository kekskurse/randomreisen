<html>
<head>
	<title>Random Reisen</title>
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="css/sticky-footer.css" rel="stylesheet">
	<script src="jquery/jquery.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
</head>
<?php
ini_set("display_errors", 1);
date_default_timezone_set("Europe/Berlin");
include("../vendor/autoload.php");
#include("functions.php");
$libStation = new Lib\Bahn\Stations();
$eCache = new easylib\easycache\easycache();
if(\Lib\Common\Formdata::getTextbox("name", "")!="")
{
	if(!$stations = $eCache->get("station_".\Lib\Common\Formdata::getTextbox("name", "")))
	{
		$stations = $libStation->searchStations(\Lib\Common\Formdata::getTextbox("name", ""));
		$eCache->add("station_".\Lib\Common\Formdata::getTextbox("name", ""), $stations, 60*60*2);
	}
	if(\Lib\Common\Bahn::isBahnhof(\Lib\Common\Formdata::getTextbox("name", ""), $stations))
	{
		$reqeustid = \Lib\Common\Formdata::requestID();
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
			$trains = $libStation->nextTrains(\Lib\Common\Formdata::getTextbox("name", ""), $t, $typ);
			$min = time() + ($_GET["dauer"]-$_GET["pm"]) * 60;
			$max = time() + ($_GET["dauer"]+$_GET["pm"]) * 60;
			$treffer = \Lib\Common\Bahn::filter($trains, $min, $max, $_GET["reisedauer"]);
			$eCache->add("treffer_".$reqeustid, $treffer, 60*60);
		}
		#var_dump(count($treffer));
	}
}
?>
<div class="container-fluid">
<div class="row" style="margin-top:50px;">
	<div class="col-md-3"><h1>Random Reisen</h1></div>
	<div class="col-md-6">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
		  <li class="active"><a href="#home" role="tab" data-toggle="tab">Grundsuche</a></li>
		  <li><a href="#profile" role="tab" data-toggle="tab">Reisedetais</a></li>
		</ul>
		<form class="form" id="suchform" name="suchform">
			<!-- Tab panes -->
			<div class="tab-content">
				<div class="tab-pane active" id="home" style="height:150px;">
					<div class="form-group">
						<label for="">Startbahnhof</label><br>
						<div class="col-md-12">
							<input type="text" style="width:100%" class="form-control" name="name" id="name" <?php echo \Lib\Common\Formdata::getTextbox("name", "", true); ?> placeholder="Bahnhof">
						</div>
					</div>
					<div class="row" style="padding-top:40px;">
						<div class="col-md-6">
							<div class="form-group">
								<label for="">Zugtypen</label>
								<br>
								<div class="row">
									<div class="col-md-3">
										<input type="checkbox" name="ice" <?php echo \Lib\Common\Formdata::getCheckbox("ice", true, true, "submit"); ?>> <img src="http://www.img-bahn.de/v/1131/img/ice_24x24.gif">
									</div>
									<div class="col-md-3">
										<input type="checkbox" name="ic" <?php echo \Lib\Common\Formdata::getCheckbox("ic", true, true, "submit"); ?>> <img src="http://www.img-bahn.de/v/1131/img/ec_ic_24x24.gif">
									</div>
									<div class="col-md-3">
										<input type="checkbox" name="d" <?php echo \Lib\Common\Formdata::getCheckbox("d", false, true, "submit"); ?>> <img src="http://www.img-bahn.de/v/1131/img/ir_24x24.gif">
									</div>
									<div class="col-md-3">
										<input type="checkbox" name="nv" <?php echo \Lib\Common\Formdata::getCheckbox("nv", false, true, "submit"); ?>> <img src="http://www.img-bahn.de/v/1131/img/re_24x24.gif">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="">Reisebeginn</label><br>
								<input name="stunde" style="width:50px;float:left;" class="form-control" <?php echo \Lib\Common\Formdata::getTextbox("stunde", date("H"), true); ?>><span style="float:left;padding-top:5px;">:</span><input name="minuten" class="form-control" style="width:50px;float:left;" <?php echo \Lib\Common\Formdata::getTextbox("minuten", date("i"), true); ?>><br>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="profile" style="height:150px;">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="">Ankunft in</label><br>
								<input name="dauer" class="form-control" style="width:60;float:left;" <?php echo \Lib\Common\Formdata::getTextbox("dauer", "60", true); ?>>
								<select name="pm" class="form-control" style="width:110px;float:left;">
									<option value="5" <?php echo \Lib\Common\Formdata::getSelect("pm", "5", "5", true) ?>>+/- 5</option>
									<option value="10" <?php echo \Lib\Common\Formdata::getSelect("pm", "10", "5", true) ?>>+/- 10</option>
									<option value="15" <?php echo \Lib\Common\Formdata::getSelect("pm", "15", "5", true) ?>>+/- 15</option>
									<option value="20" <?php echo \Lib\Common\Formdata::getSelect("pm", "20", "5", true) ?>>+/- 20</option>
									<option value="60" <?php echo \Lib\Common\Formdata::getSelect("pm", "60", "5", true) ?>>+/- 60</option>
									<option value="120" <?php echo \Lib\Common\Formdata::getSelect("pm", "120", "5", true) ?>>+/- 120</option>
								</select> <span style="float:left;margin-top:8px;"> Minuten</span>
							</div>
						</div>
						<div class="col-md-6">
							<label for="">Mindest dauer der Bahnfahrt:</label><br>
							<input name="reisedauer" style="width:60;float:left;" class="form-control" <?php echo \Lib\Common\Formdata::getTextbox("reisedauer", "40", true); ?>><span style="float:left;padding-top:8px;"> Minuten</span>
						</div>
						
				</div>
			</div>
			<input class="btn btn-default" type="submit" name="submit" value="Suchen" style="float:right;">
		</form>
	</div>
	<div class="col-md-3"></div>
</div>
</div>
<hr>
<div class="row">
	<div class="col-md-3"></div>
	<div class="col-md-6">
<?php
if(isset($stations)&&count($stations)>1&&!isset($treffer))
{
	echo "W&auml;hle deinen Bahnhof und dr&uuml;cke erneut auf 'Suchen'<br><br>";
	#var_dump($stations);
	foreach($stations as $station)
	{
		?>
		<div style="width:210px;height:50px;border:1px solid black;float:left;padding:5px;"><a href="#" onclick='document.getElementById("name").value="<?php echo $station["value"]; ?>";document.suchform.submit();'><?php echo $station["value"]; ?></a></div>
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
		?>
		<div class="jumbotron">
		  <h1><?php echo $treffer[$zufall][0]["typ"]." ".$treffer[$zufall][0]["nummer"]; ?></h1>
		  <p>Von <?php echo \Lib\Common\Formdata::getTextbox("name", ""); ?>, Abfahrt um <?php echo $treffer[$zufall][0]["abfahrt"]; ?> auf Gleis <?php echo $treffer[$zufall][0]["platform"]; ?>
		  <p>Nach <b><?php echo $treffer[$zufall][1]["name"]; ?></b>, Ankunft um  <?php echo $treffer[$zufall][1]["time"]; ?></p>
		</div>
		<?php
		#echo "<br><center>Fahre mit dem ".$treffer[$zufall][0]["typ"]." ".$treffer[$zufall][0]["nummer"]." (Abfahrt: ".$treffer[$zufall][0]["abfahrt"].") nach ".$treffer[$zufall][1]["name"]." ankunft um ".$treffer[$zufall][1]["time"]."</center>";
	}
	if(count($treffer)>1)
	{
		echo "<center><a href='#' onclick='location.reload();'>Andere Verbindung</a></center>";
	}
}
?>
	</div>
	<div class="col-md-3"></div>
</div>
<div class="row" style="margin-top:10px;">
	<div class="footer">
      <div class="container">
        <p class="text-muted">Quellcode: <a target="_blank" href="https://github.com/sspssp/randomreisen">Github</a> - Datenquelle: <a target="_blank" href="http://reiseauskunft.bahn.de/bin/bhftafel.exe">Bahn</a></p>
      </div>
    </div>
	</div>


