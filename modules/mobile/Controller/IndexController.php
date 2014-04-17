<?php

namespace Nmr\Mobile\Controller;
use Nmr\Deals;

class IndexController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', function() {

			$Deals = new Deals();
			$deals = $Deals->fetch();

			$this->render('deals/index.html', ['deals' => $deals]);
		});
	}
}