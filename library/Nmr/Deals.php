<?php
namespace Nmr;
use Nmr\ApiClient;

class Deals {

	public $api;

	public function __construct(ApiClient $api)
	{
		$this->api = $api;
	}

	public function fetch($offset=0, $limit=4, $category=false)
	{
		$range = $offset + $limit;

		$params = [
			'start'=> $offset,
			'range' => $range,
			'category' => $category,
			'exclude' => 'deal_id,product_id,description,image_count,shipping,attributes'
		];

		$result = $this->api->get('/dailydeals', $params);

		if ($result['error'] == 0 && isset($result['data'])) {
			return $result['data'];
		}

		return [];
	}

	public function find($deal_id)
	{
		$result = $this->api->get('/dailydeals/' . $deal_id);

		if ($result['error'] == 0 && isset($result['data'])) {
			return $result['data'];
		}

		return [];
	}
}