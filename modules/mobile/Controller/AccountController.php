<?php

namespace Nmr\Mobile\Controller;
use Nmr;

class AccountController extends \Nmr\Application\Controller {

	public function setup()
	{
		$this->route('get', function() {

			$reg_form = [
				'title' => 'Create a New Account',
				'action' => '/account/create',
				'submit' => 'Sign Up'
			];

			$login_form = [
				'title' => 'Log in',
				'action' => '/account/login',
				'submit' => 'Log In'
			];

			$this->render([
				'reg_form' => $reg_form,
				'login_form' => $login_form
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
				$this->render([
					'type' => $type,
					'type_titlelized' => ucfirst($type),
					'fields' => Nmr\Address::$fields
				]);
			});
		}
	}
}