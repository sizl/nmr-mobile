<?php

namespace Nmr\Application;

use Slim\Slim;

class Controller {

	/*
	 * Slim Instance
	 */
	protected $app;

	/*
	 * Route String
	 */
	protected $route;

	/*
	 * Current Controller
	 */
	protected $controller;

	/*
	 * Current Action
	 */
	protected $action;

	/*
	 * View Data
	 */
	protected $data = [];

	public function __construct(Slim $app, $controller, $action)
	{
		$this->app = $app;
		$this->controller = $controller;
		$this->action = $action;

		$this->route = \Nmr\Application::build_route($controller, $action);
	}

	public function route($method, $params = '', $callback = '')
	{
		if(is_callable($params)){
			$callback = $params;
		}else if(!empty($params)){
			$this->route .= $params;
		}

		$this->app->$method($this->route, $callback);
	}

	/*
	 * Renders default template based on scaffolding
	 * or can send in a different template
	 */
	public function render($template='', array $data = [])
	{
		if(is_array($template)) {
			$this->data = array_merge($this->data, $template);
			$template = $this->controller . '/' . $this->action . '.html';
		}

		if(empty($template)){
			$template = $this->controller . '/' . $this->action . '.html';
		}

		if(!empty($data)){
			$this->data = array_merge($this->data, $data);
		}

		if($this->is_ajax()){
			$this->render_json($this->data);
		}else{
			$this->app->render($template, $this->data);
		}
	}

	public function render_json($data)
	{
		header('Content-Type: application/json');
		print json_encode($data);
		exit(0);
	}

	public function is_ajax()
	{
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
}