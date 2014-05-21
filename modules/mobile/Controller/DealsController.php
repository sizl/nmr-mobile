<?php

namespace Nmr\Mobile\Controller;
use Nmr\Deals;

class DealsController extends \Nmr\Application\Controller {

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
			'js_options' => ['limit' => $this->feed_limit],
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
		$deal = $Deals->find($id);

		$data = [
			'quantity' => 5,
			'deal' => $deal
		];

		$this->render($data);
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