<?php

namespace Nmr\Mobile\Controller;
use Nmr;

class RegisterController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', function() {
			$this->render('/register/index.html');
		});

		$this->route('post', function() {

			$post = $_POST;
			$post['customer']['ip_address'] = $_SERVER['REMOTE_ADDR'];

			if($this->session->hasCookie()){
				$post['session_id'] = $_COOKIE['NMRSESSID'];
			}

			//create new user account via api call
			$result = $this->api->post('/customers', $post);

			//Api call returned negative result
			if($result['error'] == 1 && isset($result['message'])){
				$this->renderJson(['status' => 1, 'error' => $result['message'], 'result' => $result]);
				return;
			}

			//registration was successful. got session id back
			if(!empty($result['data']['session_id'])){
				$this->session->setCookie($result['data']['session_id']);
				$this->renderJson([
					'status' => 0,
					'customer' => $result['data']['customer'],
					'session_id' => $result['data']['session_id']
				]);

				return;
			}

			//Catch-all error
			$this->renderJson(['status' => 1, 'error' => 'An unknown error occurred while trying to create your account', 'result' => $result]);
		});
	}
}