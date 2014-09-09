<?php
namespace Lib\Common;

class Bahn
{
	static public function isBahnhof($name, $stationlist)
	{
		foreach($stationlist as $station)
		{
			if($station["value"] == $name)
			{
				return true;
			}
		}
		return false;
	}

	static public function filter($trains, $min, $max, $dauer = NULL)
	{
		$treffer = array();
		foreach($trains as $train)
		{
			foreach($train["station"] as $station)
			{
				if($station["timestamp"]>=$min && $station["timestamp"]<=$max)
				{
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
}


