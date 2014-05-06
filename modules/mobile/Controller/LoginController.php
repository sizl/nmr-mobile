<?php

namespace Nmr\Mobile\Controller;
use Nmr;

class LoginController extends \Nmr\Application\Controller {

	public function index()
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

			//Api call returned negative result
			if($result['error'] == 1 && isset($result['message'])){
				$this->renderJson(['status' => 1, 'error' => $result['message'], 'result' => $result]);
				return;
			}

			//Login was successful
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
			$this->renderJson(['status' => 1, 'error' => 'An unknown error occurred.', 'result' => $result]);
		});
	}

	public function fbconnect()
	{
		$this->route('post', function() {
			$post = $_POST;
			$this->session->prepareAuthPost($post);
			$result = $this->api->post('/loginoauth', $post);

			if($result['error'] == 0 && isset($result['data']['session_id'])){
				$this->session->setCookie($result['data']['session_id']);
				$this->renderJson(['status' => 0, 'customer' => $result['data']['customer']]);
			}else{
				$this->renderJson(['status' => 1, 'error' => $result['message']]);
			}
		});
	}
}