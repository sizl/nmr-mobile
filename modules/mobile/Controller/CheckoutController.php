<?php

namespace Nmr\Mobile\Controller;
use Nmr;
use Nmr\Deals;

class CheckoutController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', function() {
			$this->render([
				'has_address' => false
			]);
		});

		$this->route('post', function() {
			$this->renderJson($_POST);
		});
	}

	public function address()
	{
		if($this->isPost()){

			$this->route('post', '/:type', function($type) {
				//TODO: insert address by type
				$this->renderJson($_POST);
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

	public function add_item()
	{
		$this->route('post', function(){

			$this->requireSession(function($session_id){

				$result = $this->api->post('/cart/add/' . $_POST['deal_id'], $_POST);

				if($result) {
					$this->renderJson(['status' => 0 , 'session_id' => $session_id, 'result' => $result]);
				}else{
					$this->renderJson([
						'status' => 1,
						//'error' => $error
					]);
				}
			});
		});
	}
}