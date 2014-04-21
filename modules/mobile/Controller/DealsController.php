<?php

namespace Nmr\Mobile\Controller;
use Nmr\Deals;

class DealsController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', '/:id/:seo_title', function($deal_id, $seo_title) {

			$Deals = new Deals();
			$deals = $Deals->fetch();

			if($this->isAjax()) {
				$this->render('deals/view.html', ['deal' => $deals[$deal_id]]);
			}
		});

	}

	public function fetch()
	{
		$this->route('get', '/:limit/:offset', function($limit, $offset) {
			$Deals = new Deals();
			$deals = $Deals->fetch($limit, $offset);
			$this->render_json(['status' => 0, 'deals' => $deals]);
		});
	}
	public function find()
	{
		$this->route('get', '/:deal_id', function($deal_id) {
			$Deals = new Deals();
			$deal = $Deals->find($deal_id);
			$this->render_json(['status' => 0, 'deal' => $deal]);
		});
	}
}