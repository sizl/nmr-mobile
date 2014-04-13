<?php

namespace Nmr\Mobile\Controller;

class AccountController extends \Nmr\BaseController {

	public function index()
	{
		$this->route('get', function() {
			$this->render();
		});
	}

	public function edit()
	{
		$this->route('get', function() {
			$this->render();
		});
	}
}