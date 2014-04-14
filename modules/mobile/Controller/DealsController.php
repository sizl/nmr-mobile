<?php

namespace Nmr\Mobile\Controller;
use Nmr\Deals;

class DealsController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', '/:deal_id/:seo_safe_title', function($deal_id, $seo_safe_title) {
			$Deals = new Deals();
			$deals = $Deals->fetch();
			$this->render('deals/view.html', $deals[$deal_id]);
		});
	}
}