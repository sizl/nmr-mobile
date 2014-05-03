<?php

namespace Nmr\Mobile\Controller;
use Nmr\Deals;

class IndexController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', function() {

			$limit = 4;

			$Deals = new Deals();
			$deals = $Deals->fetch(0, $limit);

			$cell_template = $this->getHandlebarsTemplate('deals/deal-cell.hbs');
			$deals_html = $this->renderHandlebars($cell_template, ['deals' => $deals]);

			$this->render('deals/index.html', [
				'js_options' => ['limit' => $limit],
				'page_options' => [
					'cell_template' => $cell_template,
					'deals_html' => $deals_html
				]
			]);
		});
	}
}