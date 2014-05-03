<?php

namespace Nmr\Mobile\Controller;
use Nmr\Deals;

class DealsController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', '/:id/:seo_title', function($deal_id, $seo_title) {

			$Deals = new Deals();
			$deal = $Deals->find($deal_id);

			$data = [
				'quantity' => 5,
				'deal' => $deal
			];

			$this->render('deals/view.html', $data);

		});
	}

	public function fetch()
	{
		$this->route('get', '/:limit/:offset', function($limit, $offset) {
			$Deals = new Deals();
			$deals = $Deals->fetch($limit, $offset);
			$this->renderJson(['status' => 0, 'deals' => $deals]);
		});
	}

	public function find()
	{
		$this->route('get', '/:deal_id', function($deal_id) {
			$Deals = new Deals();
			$deal = $Deals->find($deal_id);
			$this->renderJson(['status' => 0, 'deal' => $deal]);
		});
	}
}