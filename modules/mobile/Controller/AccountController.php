<?php

namespace Nmr\Mobile\Controller;
use Nmr;

class AccountController extends \Nmr\Application\Controller {

	public function create()
	{
		$this->route('post', function() {
			//TODO: create new account
			$this->renderJson([
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

			$post = $_POST;

			if($this->session->validateLogin($post, $error) == false){
				$this->renderJson(['status' => 1, 'error' => $error]);
			}

			$this->session->prepareLoginPost($post);
			$result = $this->api->post('/login', $post);

			$this->processResult($result);
		});
	}

	public function fbconnect()
	{
		$this->route('post', function() {

//			$facebook = \Nmr\Facebook::instance();
//			$facebook->api('/me/feed', 'POST', [
//				'link' => 'www.nomorerack.com',
//				'message' => 'just joined nomorerack'
//			]);

//			$access_token = $facebook->getAccessToken();
//			if($_POST['access_token'] != $access_token){
//				$this->renderJson(['status' => 1, 'message' => 'Invalid Access Token', 'ac' => $access_token, 'dc' => $_POST['access_token']]);
//			}

/* Temporary placeholder for FB Connect */
$post = [
	'email_address' => 'kevin.liu@nomorerack.com',
	'password' => 123456
];

$this->session->prepareLoginPost($post);
$result = $this->api->post('/customerlogin', $post);
$this->processResult($result);

		});
	}

	public function processResult($result)
	{
		//Api call returned negative result
		if($result['error'] == 1 && isset($result['message'])){
			$this->renderJson(['status' => 1, 'error' => $result['message'], 'result' => $result]);
		}

		//Login was successful
		elseif(!empty($result['data']['session_id'])){
			$this->session->setNmrCookie($result['data']['session_id']);
			$this->renderJson([
				'status' => 0,
				'customer' => $result['data']['customer'],
				'session_id' => $result['data']['session_id']
			]);
		}

		//Catch-all error
		//$this->renderJson(['status' => 1, 'error' => 'An unknown error occurred.', 'result' => $result]);
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