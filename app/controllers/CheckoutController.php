<?php

namespace NmrController;

class CheckoutController extends \Nmr\BaseController {

	public function index()
	{
		$this->route('get', function() {
			$this->render();
		});
	}
}