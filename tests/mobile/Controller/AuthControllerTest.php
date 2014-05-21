<?php

class AuthControllerTest extends PHPUnit_Framework_TestCase {

	protected $slim;
	protected $controller;

	public function setUp()
	{
		$this->slim = new \Slim\Slim([
			'mode' => ENVIRONMENT,
			'debug'=> (ENVIRONMENT == 'development'),
			'view' => new Slim\Views\Twig(),
			'templates.path' => MODULE_PATH. '/views',
		]);
	}

	public function testLoginView()
	{
		ob_start();

		$controller = new \Nmr\Mobile\Controller\AuthController($this->slim);
		$controller->loginView();
		$html = ob_get_clean();

		$this->assertTrue(strpos($html, 'Sign In') !== FALSE);
	}

	public function testMissingPostLoginSubmit()
	{
		\Slim\Environment::mock([
			'REQUEST_METHOD' => 'POST',
			'debug' => false
			//No post
		]);

		ob_start();

		$controller = new AuthControllerSpy($this->slim);
		$controller->loginSubmit();

		$data = json_decode(ob_get_clean(), true);

		$this->assertArrayHasKey('status', $data);
		$this->assertEquals(1, $data['status']);

	}

	public function testWrongPasswordLoginSubmit()
	{
		$post = ['email_address' => 'kevin.liu@nomorerack.com', 'password' => 'notmypasswd', 'ip_address' => '123.2.1.4'];

		\Slim\Environment::mock([
			'REQUEST_METHOD' => 'POST',
			'debug' => false,
			'QUERY_STRING' => http_build_query($post)
		]);

		ob_start();

		$config = require APP_ROOT . '/config/config.php';
		$api = new Nmr\ApiClient($config['api']);

		$controller = new AuthControllerSpy($this->slim);

		$mockSession = $this->getMock(
			'\Nmr\Session',
			['validateLogin', 'prepareAuthPost','setSessionCookie'],
			[$api]
		);

		$mockSession->method('validateLogin')
			->will($this->returnValue(true));

		$mockSession->method('prepareAuthPost')
			->will($this->returnValue(true));

		$mockSession->method('setSessionCookie')
			->will($this->returnValue('myfakesession'));

		$controller->setPost($post);
		$controller->session = $mockSession;

		$controller->loginSubmit();

		$data = json_decode(ob_get_clean(), true);

		$this->assertArrayHasKey('status', $data);

		$this->assertEquals(1, $data['status']);
		$this->assertEquals('Email address and password do not match our records', $data['error']);
	}

	public function testValidLoginSubmit()
	{
		$post = ['email_address' => 'kevin.liu@nomorerack.com', 'password' => '123456', 'ip_address' => '123.2.1.4'];

		\Slim\Environment::mock([
			'REQUEST_METHOD' => 'POST',
			'debug' => false,
			'QUERY_STRING' => http_build_query($post)
		]);

		ob_start();

		$config = require APP_ROOT . '/config/config.php';
		$api = new Nmr\ApiClient($config['api']);

		$controller = new AuthControllerSpy($this->slim);

		$mockSession = $this->getMock(
			'\Nmr\Session',
			['validateLogin', 'prepareAuthPost','setSessionCookie'],
			[$api]
		);

		$mockSession->method('validateLogin')
			->will($this->returnValue(true));

		$mockSession->method('prepareAuthPost')
			->will($this->returnValue(true));

		$mockSession->method('setSessionCookie')
			->will($this->returnValue('myfakesession'));

		$controller->setPost($post);
		$controller->session = $mockSession;

		$controller->loginSubmit();

		$data = json_decode(ob_get_clean(), true);

		$this->assertArrayHasKey('status', $data);

		$this->assertEquals(0, $data['status']);

		$this->assertArrayHasKey('customer', $data);
		$this->assertArrayHasKey('id', $data['customer']);
		$this->assertArrayHasKey('status', $data['customer']);
		$this->assertArrayHasKey('state', $data['customer']);
		$this->assertArrayHasKey('first_name', $data['customer']);
		$this->assertArrayHasKey('last_name', $data['customer']);
		$this->assertArrayHasKey('ip_address', $data['customer']);
		$this->assertArrayHasKey('email_address', $data['customer']);
		$this->assertArrayHasKey('created_on', $data['customer']);

		$this->assertNotEmpty($data['customer']['id']);
		$this->assertNotEmpty($data['customer']['email_address']);
		$this->assertNotEmpty($data['customer']['status']);
		$this->assertNotEmpty($data['customer']['state']);
	}

	public function testRegView()
	{
		ob_start();

		$controller = new \Nmr\Mobile\Controller\AuthController($this->slim);
		$controller->registerView();
		$html = ob_get_clean();

		$this->assertTrue(strpos($html, '<h3>Register</h3>') !== FALSE);
	}

	public function testMissingEmailRegisterSubmit()
	{
		$post = ['customer' => ['password' => 'omgf', 'ip_address' => '123.2.1.4']];

		\Slim\Environment::mock([
			'REQUEST_METHOD' => 'POST',
			'debug' => false,
			'QUERY_STRING' => http_build_query($post)
		]);

		ob_start();

		$config = require APP_ROOT . '/config/config.php';
		$api = new Nmr\ApiClient($config['api']);

		$controller = new AuthControllerSpy($this->slim);

		$mockSession = $this->getMock(
			'\Nmr\Session',
			['validateLogin', 'prepareAuthPost','setSessionCookie'],
			[$api]
		);

		$mockSession->method('validateLogin')
			->will($this->returnValue(true));

		$mockSession->method('prepareAuthPost')
			->will($this->returnValue(true));

		$mockSession->method('setSessionCookie')
			->will($this->returnValue('myfakesession'));

		$controller->setPost($post);
		$controller->session = $mockSession;

		$controller->registerSubmit();

		$data = json_decode(ob_get_clean(), true);

		$this->assertArrayHasKey('status', $data);

		$this->assertEquals(1, $data['status']);
		$this->assertEquals('Missing required field: customer[email_address]', $data['error']);
	}

	public function testMissingPasswordRegisterSubmit()
	{
		$post = ['customer' => ['email_address' => 'omgf@wtf.com', 'ip_address' => '123.2.1.4']];

		\Slim\Environment::mock([
			'REQUEST_METHOD' => 'POST',
			'debug' => false,
			'QUERY_STRING' => http_build_query($post)
		]);

		ob_start();

		$config = require APP_ROOT . '/config/config.php';
		$api = new Nmr\ApiClient($config['api']);

		$controller = new AuthControllerSpy($this->slim);

		$mockSession = $this->getMock(
			'\Nmr\Session',
			['validateLogin', 'prepareAuthPost','setSessionCookie'],
			[$api]
		);

		$mockSession->method('validateLogin')
			->will($this->returnValue(true));

		$mockSession->method('prepareAuthPost')
			->will($this->returnValue(true));

		$mockSession->method('setSessionCookie')
			->will($this->returnValue('myfakesession'));

		$controller->setPost($post);
		$controller->session = $mockSession;

		$controller->registerSubmit();

		$data = json_decode(ob_get_clean(), true);

		$this->assertArrayHasKey('status', $data);

		$this->assertEquals(1, $data['status']);
		$this->assertEquals('Missing required field: customer[password]', $data['error']);
	}

	public function testValidRegisterSubmit()
	{
		$email = uniqid();

		$post = ['customer' => ['email_address' => "{$email}@nmr.com", 'password' => '123456', 'ip_address' => '123.2.1.4']];

		\Slim\Environment::mock([
			'REQUEST_METHOD' => 'POST',
			'debug' => false,
			'QUERY_STRING' => http_build_query($post)
		]);

		ob_start();

		$config = require APP_ROOT . '/config/config.php';
		$api = new Nmr\ApiClient($config['api']);

		$controller = new AuthControllerSpy($this->slim);

		$mockSession = $this->getMock(
			'\Nmr\Session',
			['validateLogin', 'prepareAuthPost','setSessionCookie'],
			[$api]
		);

		$mockSession->method('validateLogin')
			->will($this->returnValue(true));

		$mockSession->method('prepareAuthPost')
			->will($this->returnValue(true));

		$mockSession->method('setSessionCookie')
			->will($this->returnValue('myfakesession'));

		$controller->setPost($post);
		$controller->session = $mockSession;

		$controller->registerSubmit();

		$data = json_decode(ob_get_clean(), true);

		$this->assertArrayHasKey('status', $data);

		$this->assertEquals(0, $data['status']);

		$this->assertArrayHasKey('customer', $data);
		$this->assertArrayHasKey('id', $data['customer']);
		$this->assertArrayHasKey('status', $data['customer']);
		$this->assertArrayHasKey('first_name', $data['customer']);
		$this->assertArrayHasKey('last_name', $data['customer']);
		$this->assertArrayHasKey('email_address', $data['customer']);
		$this->assertArrayHasKey('phone_number', $data['customer']);
		$this->assertArrayHasKey('created_on', $data['customer']);
		$this->assertArrayHasKey('gender', $data['customer']);
		$this->assertArrayHasKey('birth_date', $data['customer']);

		$this->assertNotEmpty($data['customer']['id']);
		$this->assertNotEmpty($data['customer']['email_address']);
		$this->assertNotEmpty($data['customer']['status']);
	}

	public function testFbconnectMissingStrategy()
	{
		$post = ['email_address' => "kvnliu@gmail.com", 'password' => '123456', 'ip_address' => '123.2.1.4'];

		\Slim\Environment::mock([
			'REQUEST_METHOD' => 'POST',
			'debug' => false,
			'QUERY_STRING' => http_build_query($post)
		]);


		ob_start();

		$config = require APP_ROOT . '/config/config.php';
		$api = new Nmr\ApiClient($config['api']);

		$controller = new AuthControllerSpy($this->slim);

		$mockSession = $this->getMock(
			'\Nmr\Session',
			['validateLogin', 'prepareAuthPost','setSessionCookie'],
			[$api]
		);

		$mockSession->method('validateLogin')
			->will($this->returnValue(true));

		$mockSession->method('prepareAuthPost')
			->will($this->returnValue(true));

		$mockSession->method('setSessionCookie')
			->will($this->returnValue('myfakesession'));

		$controller->setPost($post);
		$controller->session = $mockSession;

		$controller->fbConnect();

		$data = json_decode(ob_get_clean(), true);


		$this->assertArrayHasKey('status', $data);

		$this->assertEquals(1, $data['status']);

		$this->assertArrayHasKey('error', $data);

	}

	public function testValidFbconnect()
	{
		$config = require APP_ROOT . '/config/config.php';

		$post = [
			'email_address' => "kvnliu@gmail.com",
			'id' => 545546230,
			'strategy' => 'facebook',
			'first_name' => 'kevin',
			'last_name' => 'liu',
			'gender' => 'M',
			'ip_address' => '123.2.1.4',
			'access_token' => $config['facebook']['appId'] .'|' . $config['facebook']['appSecret']
		];

		\Slim\Environment::mock([
			'REQUEST_METHOD' => 'POST',
			'debug' => false,
			'QUERY_STRING' => http_build_query($post)
		]);


		ob_start();


		$api = new Nmr\ApiClient($config['api']);

		$controller = new AuthControllerSpy($this->slim);

		$mockSession = $this->getMock(
			'\Nmr\Session',
			['validateLogin', 'prepareAuthPost','setSessionCookie'],
			[$api]
		);

		$mockSession->method('validateLogin')
			->will($this->returnValue(true));

		$mockSession->method('prepareAuthPost')
			->will($this->returnValue(true));

		$mockSession->method('setSessionCookie')
			->will($this->returnValue('myfakesession'));

		$controller->setPost($post);
		$controller->session = $mockSession;

		$controller->fbConnect();

		$data = json_decode(ob_get_clean(), true);


		$this->assertArrayHasKey('status', $data);

		$this->assertEquals(0, $data['status']);

		$this->assertArrayHasKey('customer', $data);
		$this->assertArrayHasKey('id', $data['customer']);
		$this->assertArrayHasKey('status', $data['customer']);
		$this->assertArrayHasKey('first_name', $data['customer']);
		$this->assertArrayHasKey('last_name', $data['customer']);
		$this->assertArrayHasKey('email_address', $data['customer']);
		$this->assertArrayHasKey('phone_number', $data['customer']);
		$this->assertArrayHasKey('created_on', $data['customer']);
		$this->assertArrayHasKey('gender', $data['customer']);
		$this->assertArrayHasKey('birth_date', $data['customer']);

		$this->assertNotEmpty($data['customer']['id']);
		$this->assertNotEmpty($data['customer']['email_address']);
		$this->assertNotEmpty($data['customer']['status']);

	}

	//TODO: test logout()..
}

class AuthControllerSpy extends \Nmr\Mobile\Controller\AuthController {

	protected $post;
	public $session;

	public function setPost($post)
	{
		$this->post = $post;
	}

	public function getPost()
	{
		return $this->post;
	}

	public function renderJson($result)
	{
		echo json_encode($result);
	}
}