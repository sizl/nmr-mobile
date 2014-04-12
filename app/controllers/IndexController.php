<?php

namespace NmrController;

class IndexController extends \Nmr\BaseController {

	public function index()
	{
		$this->route('get', function($referrer = '') {
			$this->render();
		});
	}
}