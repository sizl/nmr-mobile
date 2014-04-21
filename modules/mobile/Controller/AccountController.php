<?php

namespace Nmr\Mobile\Controller;
use Nmr;

class AccountController extends \Nmr\Application\Controller {

	public function create()
	{
		$this->route('post', function() {
			//TODO: create new account
			$this->render_json([
				'status' => 0,
			]);
		});
	}

	public function login()
	{
		$this->route('get', function() {
			$this->render();
		});

		$this->route('post', function() {
			//TODO: create user session
			$this->render_json([
				'status' => 0,
			]);
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
					'type' => $type,
					'type_titlelized' => ucfirst($type),
					'fields' => Nmr\Address::$fields
				];

				if($this->isAjax()){
					$this->render('account/address-form', $data);
				}else{
					$this->render($data);
				}
			});
		}
	}
}