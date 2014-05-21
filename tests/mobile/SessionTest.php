<?php

class SessionTest extends PHPUnit_Framework_TestCase {

	protected $api;
	protected $session;

	public function setUp()
	{
		$config = require APP_ROOT . '/config/config.php';
		$this->api = new Nmr\ApiClient($config['api']);

		$this->session = new Nmr\Session($this->api);
	}

	public function testDefaultCustomer()
	{
		$array = $this->session->defaultCustomer();

		$this->assertArrayHasKey('authenticated', $array);
		$this->assertArrayHasKey('fb_uid', $array);
	}

	public function testGetCustomerLogin()
	{
		$mockSession = new SessionSpy($this->api);

		//get a new session id
		$sessionId = $mockSession->getSessionId();

		$result = $this->api->post('/login', [
			'email_address'=> 'kevin.liu@nomorerack.com',
			'ip_address' => '124.24.52.1',
			'password' => '123456',
			'session_id' => $sessionId
		]);

		$mockSession->NMRSESSID = $sessionId;

		$this->assertEquals($result['data']['session_id'], $sessionId);

		$customer = $mockSession->getCustomer();

		$this->assertNotEmpty($customer);
		$this->assertArrayHasKey('id', $customer);

	}

	public function testPrepareAuthPost()
	{
		$post = ['foo' => 'bar'];
		$mockSession = new SessionSpy($this->api);

		$mockSession->NMRSESSID = 'blah';

		$mockSession->prepareAuthPost($post);

		$this->assertArrayHasKey('foo', $post);
		$this->assertArrayHasKey('ip_address', $post);
		$this->assertArrayHasKey('session_id', $post);
	}

	public function testGetFacebookUid()
	{
		$config = require APP_ROOT . '/config/config.php';
		$fbConfig = $config['facebook'];

		$mockSession = new SessionSpy($this->api);

		$mockFacebook = new FakeFacebook();
		$mockSession->setFacebook($mockFacebook);

		$mockSession->data['fbsr_'. $fbConfig['appId']] = 'abc';

		$result = $mockSession->getFacebookUid();

		$this->assertArrayHasKey('id', $result);

	}

	public function testMissingEmailLogin()
	{
		$mockSession = new SessionSpy($this->api);

		$post = ['cool'=> 'dawg'];
		$error = null;

		$result = $mockSession->validateLogin($post, $error);

		$this->assertFalse($result);

		$this->assertEquals('Email address cannot be blank', $error);
	}

	public function testMissingPasswordLogin()
	{
		$mockSession = new SessionSpy($this->api);

		$post = ['email_address'=> 'dawg'];
		$error = null;

		$result = $mockSession->validateLogin($post, $error);

		$this->assertFalse($result);

		$this->assertEquals('Password cannot be blank', $error);
	}

	public function testValidLogin()
	{
		$mockSession = new SessionSpy($this->api);

		$post = ['email_address'=> 'dawg', 'password' => 'waah'];
		$error = null;

		$result = $mockSession->validateLogin($post, $error);

		$this->assertTrue($result);
	}
}


class SessionSpy extends \Nmr\Session {

	public $NMRSESSID;
	public $data = [];

	public function getSessionId()
	{
		if (!$this->NMRSESSID) {

			$config = require APP_ROOT . '/config/config.php';
			$api = new Nmr\ApiClient($config['api']);
			$result = $api->post('/customersession');

			$this->NMRSESSID = $result['data']['session_id'];
		}

		return $this->NMRSESSID;
	}

	public function isAuthenticated()
	{
		return $this->hasCookie();
	}

	public function hasCookie()
	{
		return isset($this->NMRSESSID);
	}

	public function setSessionCookie($sessionId)
	{
		$this->NMRSESSID = $sessionId;
	}

	public function getCookie()
	{
		return $this->data;
	}

	public function getFacebook()
	{
		return new FakeFacebook();
	}
}

class FakeFacebook {

	public function hasFbAppCookie()
	{
		return true;
	}

	public function getUser()
	{
		return ['id' => 1234];
	}
}