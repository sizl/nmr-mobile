<?php

namespace Nmr\Mobile\Controller;
use Nmr;

class AccountController extends \Nmr\Application\Controller {

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
					'type' => $type,
					'type_titlelized' => ucfirst($type),
					'fields' => Nmr\Address::$fields
				];

				if($this->isAjax()){
					$this->render('account/address.html', $data);
				}else{
					$this->render($data);
				}
			});
		}
	}

	public function addresses()
	{
		$this->route('get', function() {

			if($this->data['user']['authenticated']){

				$result = $this->api->get('/customeraddresses', ['customer_id' => $this->data['user']['id']]);

				print_r($result);
				die();
			}
		});
	}
}