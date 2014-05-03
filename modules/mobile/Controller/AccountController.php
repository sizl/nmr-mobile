<?php

namespace Nmr\Mobile\Controller;
use Nmr;

class AccountController extends \Nmr\Application\Controller {

	public function create()
	{
		$this->route('post', function() {

			$post = $_POST;
			$post['customer']['ip_address'] = $_SERVER['REMOTE_ADDR'];

			if($this->session->hasNmrCookie()){
				$post['session_id'] = $_COOKIE['NMRSESSID'];
			}

			$result = $this->api->post('/customers', $post);
			$this->processAuthResponse($result);
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

			$this->session->prepareAuthPost($post);
			$result = $this->api->post('/login', $post);

			$this->processAuthResponse($result);
		});
	}

	public function fbconnect()
	{
		$this->route('post', function() {
			$post = $_POST;
			$this->session->prepareAuthPost($post);
			$result = $this->api->post('/loginoauth', $post);

			if($result['error'] == 0 && isset($result['data']['session_id'])){
				$this->session->setNmrCookie($result['data']['session_id']);
				$this->renderJson(['status' => 0, 'customer' => $result['data']['customer']]);
			}else{
				$this->renderJson(['status' => 1, 'error' => $result['message']]);
			}
		});
	}

	private function processAuthResponse($result)
	{
		//Api call returned negative result
		if($result['error'] == 1 && isset($result['message'])){
			$this->renderJson(['status' => 1, 'error' => $result['message'], 'result' => $result]);
			return;
		}

		//Login was successful
		if(!empty($result['data']['session_id'])){
			$this->session->setNmrCookie($result['data']['session_id']);
			$this->renderJson([
				'status' => 0,
				'customer' => $result['data']['customer'],
				'session_id' => $result['data']['session_id']
			]);

			return;
		}

		//Catch-all error
		$this->renderJson(['status' => 1, 'error' => 'An unknown error occurred.', 'result' => $result]);
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