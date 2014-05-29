<?php
namespace Nmr;
use Nmr\Facebook;
use Nmr\ApiClient;

class Session {

	private $api;
	protected $facebook;

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
		if (!$this->hasSessionCookie()) {
			return false;
		}

		$session_id = $this->getSessionId();
		$result = $this->api->get('/customersession', ['session_id' => $session_id]);

		if(isset($result['data']['customer'])){
			return $result['data']['customer'];
		}

		return false;
	}

	public function getFacebookUid()
	{
		$facebook = $this->getFacebook();

		if ($facebook->hasFbAppCookie()) {
			return $facebook->getUser();
		}

		return 0;
	}

	public function getCookie()
	{
		return $_COOKIE;
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
		$post['ip_address'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';

		//set cookie if present
		if($this->hasSessionCookie()){
			$post['session_id'] = $this->getSessionId();
		}
	}

	public function destroy()
	{
		if(isset($_COOKIE['PHPSESSID'])){
			session_id($_COOKIE['PHPSESSID']);

			if (session_status() === PHP_SESSION_ACTIVE) {
				session_destroy();
			}

			setcookie ("PHPSESSID", "", time() - 3600, '/');
		}

		if(isset($_COOKIE['NMRSESSID'])){
			setcookie ("NMRSESSID", "", time() - 3600, '/');
		}
	}

	public function createUserSession($data)
	{
		$this->setSessionCookie($data['session_id']);

		if (isset($data['customer']['id'])) {
			$this->setCustomerIdCookie($data['customer']['id']);
		}
	}

	public function createCartSession($cartId)
	{
		$cookieLife = 604800; // one week
		setcookie("scid", $cartId, time()+$cookieLife ,'/');
	}

	public function hasCartIdCookie()
	{
		return isset($_COOKIE['scid']);
	}

	public function getCartIdCookie()
	{
		return $_COOKIE['scid'];
	}

	public function hasSessionCookie()
	{
		return isset($_COOKIE['NMRSESSID']);
	}

	public function getSessionId()
	{
		return $_COOKIE['NMRSESSID'];
	}

	public function setSessionCookie($session_id)
	{
		$cookieLife = 604800; // one week
		setcookie("NMRSESSID", $session_id, time()+$cookieLife ,'/');
	}

	public function setCustomerIdCookie($customerId)
	{
		//set current customer id
		$cookieLife = 604800; // one week
		setcookie("ccid", $customerId, time()+$cookieLife ,'/');
	}

	public function getCustomerIdCookie()
	{
		return $_COOKIE['ccid'];
	}

	public function hasCustomerIdCookie()
	{
		return isset($_COOKIE['ccid']);
	}

	public function setFacebook($facebook)
	{
		$this->facebook = $facebook;
	}

	public function getFacebook()
	{
		$config = require APP_ROOT . '/config/config.php';
		$this->facebook = new \Nmr\FacebookClient($config['facebook']);
		return $this->facebook;
	}
}