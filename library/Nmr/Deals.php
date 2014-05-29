<?php
namespace Nmr;
use Nmr\ApiClient;

class Deals {

	public $api;

	public function __construct(ApiClient $api)
	{
		$this->api = $api;
	}

	public function fetch($offset=0, $limit=4, $category=false, $search='')
	{
		$range = $offset + $limit;

		$params = [
			'offset'=> $offset,
			'limit' => $range
		];

		if ($category) {
			$params['category'] = $category;
		}

		if ($search) {
			$params['search'] = $search;
		}

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

	public function addToRecentlyViewed(array $deal, $sessionId)
	{
		$this->api->post('/recentlyvieweditems', [
			'deal_item_id' => $deal['deal_item_id'],
			'session_id' => $sessionId
		]);
	}

	public function getRecentlyViewedItems($sessionId)
	{
		$result = $this->api->get('/recentlyvieweditems', [
			'session_id' => $sessionId
		]);

		if ($result['error'] == 0 && isset($result['data'])) {
			return $result['data'];
		} else {
			return $result;
		}

		return [];
	}
}