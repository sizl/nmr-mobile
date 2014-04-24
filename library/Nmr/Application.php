<?php

namespace Nmr;
use Slim;

class Application {

	private $slim;
	private $route;
	private $action;

	public function __construct()
	{
		$this->slim = new \Slim\Slim([
			'mode' => ENVIRONMENT,
			'debug'=> (ENVIRONMENT == 'development'),
			'view' => new Slim\Views\Twig(),
			'templates.path' => MODULE_PATH. '/views'
		]);
	}

	public function getController($path)
	{
		$this->route  = 'index';
		$this->action = 'index';

		if(strpos($path, '?')) {
			$path = strstr($path, '?', true);
		}

		if($path != '/'){
			$parts = explode('/', $path);

			$this->route = strtolower($parts[1]);

			if(isset($parts[2])) {
				if($parts[2] == '' || is_numeric($parts[2])){
					$this->action = 'index';
				}else{
					$this->action = strtolower(str_replace('-','_',$parts[2]));
				}
			}
		}

		return $this;
	}

	public function getRoute()
	{
		return $this->route;
	}

	public function getAction()
	{
		return $this->action;
	}

	public function run()
	{
		$class = sprintf('Nmr\%s\Controller\%sController', ucfirst(MODULE), ucfirst($this->route));

		if(!class_exists($class)){
			$this->error($class . ' not found', 404);
		}else{
			//Instantiate Controller Class
			$controller = new $class($this->slim, $this->route, $this->action);
			if(!method_exists($controller, $this->action)){
				$this->error("$class::" . $this->action . "(). Method Not Found.", 404);
			}else{

				$controller->{$this->action}();
				$this->slim->run();
			}
		}
	}

	private function error($message, $code)
	{
		if(ENVIRONMENT == 'development'){
			$this->slim->render("errors/{$code}.html", ['message' => $message], $code);
		}else{
			$this->slim->run();
		}
	}
}