<?php

namespace NmrMobile;

class CheckoutController extends \Nmr\BaseController {

	public function index()
	{
		$this->route('get', function() {
			$this->render();
		});
	}
}