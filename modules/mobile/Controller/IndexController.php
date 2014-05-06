<?php

namespace Nmr\Mobile\Controller;

class IndexController extends \Nmr\Mobile\Controller\DealsController {

	public function index()
	{
		$this->route('get', function() {
			$offset = 0; $limit = $this->feed_limit;
			$this->renderDeals($offset, $limit);
		});
	}
}