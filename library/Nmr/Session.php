<?php
namespace Nmr;
use Nmr\Facebook;
use Nmr\ApiClient;

class Session {

	private $api;

	public function __construct(ApiClient $api)
	{
		$this->api = $api;
	}

	public function defaultCustomer()
	{
		return [
			'authenticated' => false,
			'fb_uid' => 0
		];
	}

	public function getCustomer()
	{
		$session_id = $this->getSessionId();
		$result = $this->api->get('/customersession', ['session_id' => $session_id]);

		if(isset($result['data']['customer'])){
			return $result['data']['customer'];
		}

		return false;
	}

	public function isAuthenticated()
	{
		return $this->hasCookie();
	}

	public function getFacebookUid()
	{
		if(isset($_COOKIE['fbsr_' . Facebook::APP_ID])){
			$facebook = \Nmr\Facebook::instance();
			return $facebook->getUser();
		}

		return 0;
	}

	public function validateLogin($post, &$error)
	{
		if(empty($post['email_address'])){
			$error = 'Email address cannot be blank';
			return false;
		}

		if(empty($post['password'])){
			$error = 'Password cannot be blank';
			return false;
		}

		return true;
	}

	public function prepareAuthPost(&$post)
	{
		//set ip of client
		$post['ip_address'] = $_SERVER['REMOTE_ADDR'];

		//set cookie if present
		if($this->hasCookie()){
			$post['session_id'] = $_COOKIE['NMRSESSID'];
		}
	}

	public function hasCookie()
	{
		return isset($_COOKIE['NMRSESSID']);
	}

	public function getSessionId()
	{
		return $_COOKIE['NMRSESSID'];
	}

	public function setCookie($session_id)
	{
		setcookie("NMRSESSID", $session_id, time()+3600 ,'/');
	}

	public function destroy()
	{
		if(isset($_COOKIE['PHPSESSID'])){
			session_id($_COOKIE['PHPSESSID']);

			if (session_status() !== PHP_SESSION_ACTIVE) {
				session_start();
			}

			session_destroy();

			setcookie ("PHPSESSID", "", time() - 3600, '/');
			setcookie ("NMRSESSID", "", time() - 3600, '/');
		}
	}
}