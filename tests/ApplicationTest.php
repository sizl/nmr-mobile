<?php

class ApplicationTest extends PHPUnit_Framework_TestCase {

	protected $slim;
	protected $app;

	public function setUp()
	{
		$this->slim = new \Slim\Slim(array(
			'version'        => '0.0.0',
			'debug'          => false,
			'mode'           => 'testing',
			'templates.path' => MODULE_PATH. '/views'
		));

		$this->app = new \Nmr\Application($this->slim);
	}

	public function testRoutesDoNotThrowException()
	{
		$routes = require_once(MODULE_PATH . '/config/routes.php');

		foreach ($routes as $data) {

			$route = current($data);
			$resource = $route[1];
			$action = $route[2];

			$class = sprintf('Nmr\%s\Controller\%sController', ucfirst(MODULE), $resource);

			if (!class_exists($class)) {
				throw new \Exception("Controller '{$class}'' does not exist");
			}

			$controller = new $class($this->slim);

			if (!method_exists($controller, $action)) {
				throw new \Exception("Method '{$action}' does not exist");
			}
		}
	}
}