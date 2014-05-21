<?php

namespace Nmr;
use Slim;

class Application {

	private $slim;
	private $config;

	public function __construct(\Slim\Slim &$slim)
	{
		$this->slim = $slim;
		$this->slim->setName(MODULE);
	}

	public function configureRoutes()
	{
		$slim = $this->slim;

		$routes = require(MODULE_PATH . '/config/routes.php');

		foreach ($routes as $data) {

			$method = key($data);
			$route = current($data);

			$path = $route[0];
			$resource = $route[1];
			$action = $route[2];

			$this->slim->{$method}($path, function () use($slim, $resource, $action) {

				$class = sprintf('Nmr\%s\Controller\%sController', ucfirst(MODULE), $resource);

				if (!class_exists($class)) {
					throw new \Nmr\Exception\ServerErrorException("Controller '{$class}'' does not exist");
				}

				$controller = new $class($slim);

				//set default view template to "[module]/views/[resource]/[action]"
				$controller->setDefaultViewTemplate($resource , $action);

				if (!method_exists($controller, $action)) {
					throw new \Nmr\Exception\ServerErrorException("Method '{$action}' does not exist");
				}

				call_user_func_array([$controller, $action], func_get_args());
			});
		}

		return $this;
	}

	public function run()
	{
		$this->slim->run();
	}
}