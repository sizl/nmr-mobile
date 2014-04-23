<?php

namespace Nmr;

class ApiClient {

	const API_BASE = 'http://kevapi.qa.nomorerack.com';
	const API_KEY = 'MekDavJupsyitegyayHaskirdyicboot';

	public function __construct() {}

	public function get($path, array $params = [])
	{
		$options = $this->getOptions();
		$options[CURLOPT_POST] = false;

		if(!empty($params)){
			$separator = $this->getSeparator($path);
			$path .= $separator . http_build_query($params);
		}

		return $this->curl($path, $options);
	}

	public function post($path, array $params = [])
	{
		$options = $this->getOptions();
		$options[CURLOPT_POST] = true;

		return $this->curl($path, $options, $params);
	}

	private function curl($path, $options, $params = [])
	{
		$uri = $this->buildUri($path);

		$handle = curl_init($uri);

		if($options[CURLOPT_POST]){
			$options[CURLOPT_POSTFIELDS] = http_build_query($params);
		}

		curl_setopt_array($handle, $options);

		$output = curl_exec($handle);
		curl_close($handle);

		return json_decode($output, true);
	}

	private function buildUri($path)
	{
		$separator = $this->getSeparator($path);
		$path .= $separator . 'api_key=' . self::API_KEY;
		return self::API_BASE . $path;
	}

	private function getSeparator($path)
	{
		return (strpos($path, '?') === false) ? '?' : '&';
	}

	private function getOptions()
	{
		return [
			CURLOPT_VERBOSE => true,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 5,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true
		];
	}
}
