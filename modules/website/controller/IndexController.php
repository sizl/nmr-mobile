<?php

namespace Nmr\Website\Controller;

class IndexController extends \Nmr\BaseController {

	public function index()
	{
		$this->route('get', function($referrer = '') {
			$this->render();
		});
	}
}