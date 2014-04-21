<?php

namespace Nmr\Mobile\Controller;
use Nmr\Deals;

class IndexController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', function() {
			$this->render('deals/index.html');
		});
	}
}