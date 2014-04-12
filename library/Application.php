<?php

namespace Nmr;
use Slim;

class Application {

	private $slim;
	private $controller;
	private $action;

	public function __construct()
	{
		$this->slim = new \Slim\Slim([
			'mode' => ENVIRONMENT,
			'debug'=> (ENVIRONMENT == 'development'),
			'view' => new Slim\Views\Twig(),
			'templates.path' => APP_ROOT . '/app/views'
		]);
	}

	public function getController()
	{
		//Default to index.
		$this->controller = 'index';
		$this->action = 'index';

		if($_SERVER["REQUEST_URI"] != '/'){
			$parts = explode('/', $_SERVER["REQUEST_URI"]);
			$this->controller = strtolower($parts[1]);
			if(isset($parts[2]) && $parts[2] != '/') {
				$this->action = strtolower($parts[2]);
			}
		}

		return $this;
	}

	public function run()
	{
		$class = sprintf('\NmrController\%sController', ucfirst($this->controller));
		$controller = new $class($this->slim, $this->controller, $this->action);
		$controller->{$this->action}();

		$this->slim->run();
	}
}