<?php

namespace Nmr\Mobile\Controller;
use Nmr;

class CheckoutController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', function() {
			$this->render([
				'has_address' => false
			]);
		});

		$this->route('post', function() {
			$this->render_json(['status' => 0]);
		});
	}

	public function address()
	{
		if($this->isPost()){

			$this->route('post', '/:type', function($type) {
				//TODO: insert address by type
				$this->render_json($_POST);
			});

		}else{

			$this->route('get', '/:type', function($type) {

				$data = [
					'title' => 'Billing Address',
					'fields' => Nmr\Address::$fields,
					'submit_label' => 'Continue to Checkout',
					'type' => $type
				];

				if($this->isAjax()){
					$this->render('account/address-form.html', $data);
				}else{
					$this->render('account/address.html', $data);
				}
			});
		}
	}
}