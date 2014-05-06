<?php

namespace Nmr\Mobile\Controller;
use Nmr\Deals;

class DealsController extends \Nmr\Application\Controller {

	protected $feed_limit = 8;

	public function index()
	{
		$this->route('get', function() {

			$page = !empty($_GET['page']) ? $_GET['page'] : 1;
			$limit = !empty($_GET['limit']) ? $_GET['limit'] : 8;
			$offset = $page * $limit;

			$Deals = new Deals();
			$deals = $Deals->fetch($limit, $offset);
			$this->renderJson(['status' => 0, 'deals' => $deals, 'count' => count($deals)]);
		});


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


	public function category()
	{
		//first load
		$this->route('get', '/:category', function($category) {

			if(isset($_GET['offset']) && isset($_GET['limit'])){
				$this->renderDealsJson($_GET['offset'], $_GET['limit'], $category);
			} else {
				$this->renderDeals(0, 4, $category);
			}
		});
	}

	protected function renderDeals($offset, $limit, $category = false)
	{
		$Deals = new Deals();
		$deals = $Deals->fetch($offset, $limit, $category);

		$cell_template = $this->getHandlebarsTemplate('deals/deal-cell.hbs');
		$deals_html = $this->renderHandlebars($cell_template, ['deals' => $deals]);

		$this->render('deals/index.html', [
			'js_options' => ['limit' => $limit, 'category' => $category],
			'page_options' => [
				'cell_template' => $cell_template,
				'deals_html' => $deals_html
			]
		]);
	}

	private function renderDealsJson($offset, $limit, $category)
	{
		$Deals = new Deals();
		$deals = $Deals->fetch($offset, $limit, $category);
		$this->renderJson(['status' => 0, 'deals' => $deals]);
	}
}