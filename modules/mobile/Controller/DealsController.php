<?php

namespace Nmr\Mobile\Controller;
use Nmr\Deals;

class DealsController extends \Nmr\Application\Controller {

	//How many deals to show per query
	protected $feed_limit = 24;

	/*
	 * Home Page
	 */
	public function index()
	{
		$Deals = new Deals($this->api);
		$deals = $Deals->fetch(0, $this->feed_limit);

		$cell_template = $this->getHandlebarsTemplate('deals/deal-cell.hbs');
		$deals_html = $this->renderHandlebars($cell_template, ['deals' => $deals]);

		$this->render([
			'js_options' => [
				'fetchUrl' => '/deals/fetch',
				'offset' => count($deals),
				'limit' => $this->feed_limit
			],
			'page_options' => [
				'cell_template' => $cell_template,
				'deals_html' => $deals_html
			]
		]);
	}

	/*
	 * Product Detail Page
	 */
	public function view($id, $seo_title)
	{
		$Deals = new Deals($this->api);
		$data = $Deals->find($id);
		$deal = $data['deal'];

		$attributes = [];

		if ($deal['attributes']) {
			$attrs = array_keys($deal['attributes']);
			foreach($attrs as $key) {
				$attributes[$key] = 0;
				if (count($deal['attributes'][$key]) == 1) {
					$attributes[$key] = $deal['attributes'][$key][0];
					continue;
				}
			}
		}

		//used to prepopulate add item form
		$form = [
			'quantity' => 1,
			'attribute' => $attributes
		];

		$this->render([
			'form' => $form,
			'deal' => $deal,
			'js_options' => [
				'deal' => $deal,
				'deal_item_id' => $deal['deal_item_id'],
				'product_items' => $data['product_items'],
				'image_map' => $data['image_map']
			],
		]);
	}

	/*
	 * Display Deals by category
	 */

	public function category($category)
	{
		$Deals = new Deals($this->api);
		$deals = $Deals->fetch(0, $this->feed_limit, $category);

		$cell_template = $this->getHandlebarsTemplate('deals/deal-cell.hbs');
		$deals_html = $this->renderHandlebars($cell_template, ['deals' => $deals]);

		$this->render('deals/index.html', [
			'js_options' => ['limit' => $this->feed_limit, 'category' => $category],
			'page_options' => [
				'cell_template' => $cell_template,
				'deals_html' => $deals_html
			]
		]);
	}

	public function search()
	{
		$search = '';

		if (!isset($_GET['q']) || empty($_GET['q'])) {
			$deals = [];
		} else {
			$search = $_GET['q'];
			$Deals = new Deals($this->api);
			$deals = $Deals->fetch(0, $this->feed_limit, false, $search);
		}

		$cell_template = $this->getHandlebarsTemplate('deals/deal-cell.hbs');
		$deals_html = $this->renderHandlebars($cell_template, ['deals' => $deals]);

		$this->render('deals/index.html', [
			'js_options' => ['limit' => $this->feed_limit, 'search' => $search],
			'page_options' => [
				'cell_template' => $cell_template,
				'deals_html' => $deals_html
			],
			'search_term' => $search
		]);
	}

	public function fetch()
	{
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		$category = !empty($_GET['category']) ? $_GET['category'] : false;
		$limit = !empty($_GET['limit']) ? $_GET['limit'] : $this->feed_limit;
		$offset = $page * $limit;

		$Deals = new Deals($this->api);
		$deals = $Deals->fetch($offset, $limit, $category);

		$this->renderJson([
			'status' => 0,
			'deals' => $deals,
			'count' => count($deals)
		]);
	}
}