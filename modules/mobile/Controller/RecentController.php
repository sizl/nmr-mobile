<?php


namespace Nmr\Mobile\Controller;

use Nmr\Deals;

class RecentController extends \Nmr\Application\Controller {

	protected $feed_limit = 20;

	public function index()
	{
		$recentItems = [];

		if ($this->session->hasCustomerIdCookie()) {

			$Deals = new Deals($this->api);
			$sessionId = $this->session->getSessionId();

			$recentItems = $Deals->getRecentlyViewedItems($sessionId);
		}

		$this->render([
			'recent' => [
				'items' => $recentItems,
				'count' => count($recentItems)
			]
		]);
	}

	public function add()
	{
		$id = $_POST['deal_item_id'];
		$Deals = new Deals($this->api);
		$deal = $Deals->find($id);

		//add recently viewed to database if user is logged in
		if ($this->session->hasSessionCookie()) {
			$sessionId = $this->session->getSessionId();
			$Deals->addToRecentlyViewed($deal, $sessionId);
		}

		$this->renderJson([
			'status' => 0
		]);
	}
}