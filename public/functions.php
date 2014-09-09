<?php
function getCheckbox($name, $default = False, $retValue = false, $submitname = "")
{
	$ret = $default;
	if(isset($_GET[$name])&&$_GET[$name]=="on")
	{
		$ret = True;
	}
	elseif((isset($_GET[$name])&&$_GET[$name]=="off")||isset($_GET[$submitname]))
	{
		$ret = False;
	}
	if($retValue==true)
	{
		if($ret==True)
		{
			return 'checked="checked"';
		}
		return '';
	}
	return $ret;
}
function getTextbox($name, $default = "", $retValue = false)
{
	$ret = $default;
	if(isset($_GET[$name]))
	{
		$ret = $_GET[$name];
	}
	if($retValue==true)
	{
		return 'value="'.$ret.'"';
	}
	return $ret;
}
function getSelect($name, $value, $default="", $retValue = true)
{
	$ret = getTextbox($name, $default);
	if($ret == $value)
	{
		if($retValue==true)
		{
			return 'selected="selected"';
		}
		return true;
	}
	else
	{
		if($retValue==true)
		{
			return '';
		}
		return false;
	}

}
function filter($trains, $min, $max, $dauer = NULL)
{
	$treffer = array();
	foreach($trains as $train)
	{
		#echo "<b>".$train["typ"]." ".$train["nummer"]."</b><br>";
		foreach($train["station"] as $station)
		{
			if($station["timestamp"]>=$min && $station["timestamp"]<=$max)
			{
				#echo "<li>".$train["typ"]." ".$train["nummer"]." nach ".$station["name"]." Ankunft um: ".$station["time"];
				if($dauer!=NULL)
				{
					$g = explode(":", $train["abfahrt"]);
					$start = mktime($g[0], $g[1]);
					$da = $station["timestamp"]-$start;
					if($da < $dauer * 60)
					{

					}
					else
					{
						$treffer[] = array($train, $station);
					}

				}
				else
				{
					$treffer[] = array($train, $station);
				}
				
			}
		}
	}
	return $treffer;
}
function requestID()
{
	$string = "";
	$keyA = array("name", "ice", "ic", "d", "nv", "stunde", "minuten");
	foreach($_GET as $key => $value)
	{
		if(in_array($key, $keyA))
		{
			$string .= $key.":".$value.";";
		}
	}
	return md5($string);
	#var_dump($_GET);
}