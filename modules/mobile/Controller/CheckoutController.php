<?php

namespace Nmr\Mobile\Controller;

class CheckoutController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', function() {

			$this->render(['options' => [
				'is_logged_out' => true,
				'has_addresses' => false
			]]);

		});

		$this->route('post', function() {
			$this->render_json(['status' => 0]);
		});
	}
}