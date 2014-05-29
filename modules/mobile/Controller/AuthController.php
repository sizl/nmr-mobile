<?php

namespace Nmr\Mobile\Controller;
use Nmr;

class AuthController extends \Nmr\Application\Controller {

	public function loginView()
	{
		$this->render('account/login-form.html');
	}

	public function loginSubmit()
	{
		$post = $this->getPost();

		if($this->session->validateLogin($post, $error) == false){
			$this->renderJson(['status' => 1, 'error' => $error]);
			return;
		}

		$this->session->prepareAuthPost($post);
		$result = $this->api->post('/login', $post);

		//Api call returned negative result
		if($result['error'] == 1 && isset($result['message'])){

			$this->renderJson([
				'status' => 1,
				'error' => $result['message'],
				'result' => $result
			]);

			return;
		}

		//Login was successful
		if(!empty($result['data']['session_id'])){

			$this->session->createUserSession($result['data']);

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

	public function registerView()
	{
		$this->render('/account/register.html');
	}

	public function registerSubmit()
	{
		$post = $this->getPost();

		$post['customer']['ip_address'] = isset($_SERVER['REMOTE_ADDR'])
			? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';

		if($this->session->hasSessionCookie()){
			$post['session_id'] = $this->session->getSessionId();
		}

		//create new user account via api call
		$result = $this->api->post('/customers', $post);

		//Api call returned negative result
		if ($result['error'] == 1 && isset($result['message'])) {

			$this->renderJson([
				'status' => 1,
				'error' => $result['message'],
				'result' => $result
			]);

			return;
		}

		//registration was successful. got session id back
		if (!empty($result['data']['session_id'])) {

			$this->session->createUserSession($result['data']);

			$this->renderJson([
				'status' => 0,
				'customer' => $result['data']['customer'],
				'session_id' => $result['data']['session_id']
			]);

			return;
		}

		//Catch-all error
		$this->renderJson([
			'status' => 1,
			'error' => 'An unknown error occurred while trying to create your account',
			'result' => $result
		]);

	}

	public function fbConnect()
	{
		$post = $this->getPost();
		$this->session->prepareAuthPost($post);
		$result = $this->api->post('/loginoauth', $post);

		if($result['error'] == 0 && isset($result['data']['session_id'])){

			$this->session->createUserSession($result['data']);

			$this->renderJson(['status' => 0, 'customer' => $result['data']['customer']]);
		}else{
			$this->renderJson(['status' => 1, 'error' => $result['message']]);
		}
	}

	public function logout()
	{
		if ($this->session->hasSessionCookie()) {

			$sessionId = $this->session->getSessionId();

			$result = $this->api->post('/logout', ['session_id' => $sessionId]);

			if($result['error'] == 0){
				$this->session->destroy();
			}
		}

		$this->slim->redirect('/');

	}
}