<?php
namespace Lib\Bahn;

class Nocurl
{
	public function get($url)
	{
		return file_get_contents($url);
	}
	public function post($url, $data)
	{
		$data = http_build_query($data);
		$context = [
		  'http' => [
		    'method' => 'POST',
		    'content' => $data
		  ]
		];
		$context = stream_context_create($context);
		$result = file_get_contents($url, false, $context);
		return $result;
	}
}
