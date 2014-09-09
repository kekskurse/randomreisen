<?php
namespace Lib\Bahn;

class Stations
{
	public function __construct()
	{
		$this->curl = new \anlutro\cURL\cURL;
	}
	public function searchStations($name)
	{
		$url = "http://reiseauskunft.bahn.de/bin/ajax-getstop.exe/dn?REQ0JourneyStopsS0A=1&REQ0JourneyStopsF=excludeMetaStations&REQ0JourneyStopsS0G=".$name."?&js=true&";
		$data = $this->curl->get($url);
		$body = $data->body;
		$regex = '@"value":"([^"]*)","id":"([^"]*)","extId":"([0-9]*)","type":"([^"]*)","typeStr":"([^"]*)","xcoord":"([0-9]*)","ycoord":"([0-9]*)","state":"([^"]*)","prodClass":"([0-9]*)","weight":"([0-9]*)"@';
		preg_match_all($regex, $body, $matches);
		#var_dump($matches);exit();
		$res = array();
		for($i=0;$i<count($matches[0]);$i++)
		{
			$t["value"]=$matches[1][$i];
			$t["id"]=$matches[2][$i];
			$t["extId"]=$matches[3][$i];
			$t["type"]=$matches[4][$i];
			$t["typeStr"]=$matches[5][$i];
			$t["xcoord"]=$matches[6][$i];
			$t["ycoord"]=$matches[7][$i];
			$t["state"]=$matches[8][$i];
			$t["prodClass"]=$matches[9][$i];
			$t["weight"]=$matches[10][$i];
			$res[] = $t;
		}
		return $res;
		var_dump($body);
		var_dump(json_decode($body));
		$detais = json_decode($body, true);
		return $detais;
	}
	public function nextTrains($staion, $timeStamp, $typ = array("ICE", "IC", "D", "NV"))
	{
		$url = "http://reiseauskunft.bahn.de/bin/bhftafel.exe/dn?ld=96248&amp;country=DEU&amp;rt=1&amp;newrequest=yes&amp;";
		$data = array();
		$data["input"] = $staion;
		$data["date"] = date("d.m.Y", $timeStamp);
		$data["time"] = date("H:m", $timeStamp);
		$data["start"]="Suchen";
		#echo date("d.m.Y H:i:s", $timeStamp);
		$data["boardType"] = "dep";
		if(in_array("ICE", $typ))
		{
			$data["GUIREQProduct_0"]="on";
		}
		if(in_array("IC", $typ))
		{
			$data["GUIREQProduct_1"]="on";
		}
		if(in_array("D", $typ))
		{
			$data["GUIREQProduct_2"]="on";
		}
		if(in_array("NV", $typ))
		{
			$data["GUIREQProduct_3"]="on";
		}
		if(in_array("S", $typ))
		{
			$data["GUIREQProduct_4"]="on";
		}
		if(in_array("BUS", $typ))
		{
			$data["GUIREQProduct_5"]="on";
		}
		if(in_array("SCHIFF", $typ))
		{
			$data["GUIREQProduct_6"]="on";
		}
		if(in_array("U", $typ))
		{
			$data["GUIREQProduct_7"]="on";
		}
		if(in_array("TRAM", $typ))
		{
			$data["GUIREQProduct_8"]="on";
		}
		$data  = $this->curl->post($url, $data);

		$regex = '@<tr\sid="journeyRow_[0-9]*"\sclass="[^"]*">\s<td\sclass="time">([0-9]{2}:[0-9]{2})</td>\s<td\sclass="train"><a href="[^"]*"><img\ssrc="[^"]*"\sclass="middle"\salt=""\s/></a></td><td class="train">\s<a\shref="[^"]*">\s([^<]*)\s</a>\s</td>\s<td class="route">\s<span class="bold">\s<a onclick="[^"]*"\shref="[^"]*">\s([^<]*)\s</a>\s</span>\s<br\s/>([^<]*)\s</td>\s<td\sclass="platform">\s<strong>([^<]*)</strong>@m';
		preg_match_all($regex, $data->body, $matches);
		$res = array();
		for($i=0;$i<count($matches[0]);$i++)
		{
			$t = array();
			$t["abfahrt"] = $matches[1][$i];
			$t["typ"] = substr($matches[2][$i], 0, strpos($matches[2][$i], " "));
			$t["nummer"] = trim(substr($matches[2][$i], strrpos($matches[2][$i], " ")));
			$t["lastStation"] = $matches[3][$i];
			$t["platform"] = $matches[5][$i];
			#var_dump($matches[5][$i]);

			$tmp = explode("\n", $matches[4][$i]);
			$station = array();
			for($g=1;$g<count($tmp);$g =$g+3)
			{
				$station["name"] = trim($tmp[$g]);
				$station["time"] = $tmp[$g+1];
				$d = explode(":", $station["time"]);
				$station["timestamp"] = mktime ($d[0], $d[1]);
				$t["station"][] = $station;
			}
			$res[] = $t;
		}
		return $res;
	}
}