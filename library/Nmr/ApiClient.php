<?php

namespace Nmr;

class ApiClient {

	const API_BASE = 'http://kevapi.qa.nomorerack.com';
	const API_KEY = 'MekDavJupsyitegyayHaskirdyicboot';

	public function __construct() {}

	public function get($path, array $params = [])
	{
		$options = $this->getOptions([CURLOPT_POST => false]);
		$path = $this->appendQueryParams($path, $params);
		return $this->curl($path, $options);
	}

	public function post($path, array $params = [])
	{
		$options = $this->getOptions([CURLOPT_POST => true]);
		$options = $this->appendPostParams($options, $params);
		return $this->curl($path, $options, $params);
	}

	private function curl($path, $options)
	{
		$uri = $this->buildUri($path);
		$handle = curl_init($uri);
		curl_setopt_array($handle, $options);
		$output = curl_exec($handle);
		curl_close($handle);

		if(!empty($output)){
			$result = json_decode($output, true);
			if(is_array($result)){
				return $result;
			}
		}

		//catch-all debug response
		return $this->debugResponse($uri, $options, $output);
	}

	private function getOptions($options=null)
	{
		$defaults = [
			CURLOPT_VERBOSE => true,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 5,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false
		];

		//using foreach instead of array_merge because
		//merging arrays with numeric indexes reset keys
		if(is_array($options) && !empty($options)){
			foreach($options as $key => $value) {
				$defaults[$key] = $value;
			}
		}

		return $defaults;
	}

	private function buildUri($path)
	{
		$glue = $this->glue($path);
		$path .= $glue . 'api_key=' . self::API_KEY;
		return self::API_BASE . $path;
	}

	private function appendQueryParams($path, $params)
	{
		if(!empty($params)){
			$glue = $this->glue($path);
			$path .= $glue . http_build_query($params);
		}

		return $path;
	}

	private function appendPostParams($options, $params)
	{
		if(!empty($params)){
			$options[CURLOPT_POSTFIELDS] = http_build_query($params);
		}

		return $options;
	}

	private function glue($path)
	{
		return (strpos($path, '?') === false) ? '?' : '&';
	}

	private function debugResponse($uri, $options, $output)
	{
		$request = ['uri' => $uri];

		if($options[CURLOPT_POST] == true){
			$request['method'] = 'POST';
			$request['params'] = $options[CURLOPT_POSTFIELDS];
		}else{
			$request['method'] = 'GET';
		}

		return ['error' => 1, 'message' => 'API request failed', 'request' => $request, 'output' => $output];
	}
}
