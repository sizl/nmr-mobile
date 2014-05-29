<?php

namespace Nmr;

class Events {

	public $api;

	public function __construct(ApiClient $api)
	{
		$this->api = $api;
	}

	public function fetch($offset=0, $limit=25)
	{
		$params = [
			'offset'=> $offset,
			'limit' => $limit
		];

		$result = $this->api->get('/events', $params);

		if ($result['error'] == 0 && isset($result['data'])) {
			return $result['data'];
		}

		return [];
	}

	public function fetchDeals($daily_deal_id, $offset=0, $limit=12)
	{
		$params = [
			'offset'=> $offset,
			'limit' => $limit
		];

		$result = $this->api->get('/events/' . $daily_deal_id, $params);

		if ($result['error'] == 0 && isset($result['data'])) {
			return $result['data'];
		}

		return [];
	}
}