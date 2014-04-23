<?php

namespace Nmr\Application;

use Nmr;
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

		$this->api = new Nmr\ApiClient();

		$this->setupRoute($controller, $action);
		$this->setupViewData();
	}

	private function setupRoute($controller, $action)
	{
		$this->action = $action;
		$this->controller = $controller;

		$this->route = \Nmr\Application::build_route($controller, $action);
	}

	private function setupViewData()
	{
		$this->data['app_id'] = \Nmr\Facebook::APP_ID;

		$facebook = \Nmr\Facebook::instance();
		$uid = $facebook->getUser();

		$this->data['user'] = [
			'authenticated' => false,
			'fbconnected' => !empty($uid),
			'fb_uid' => $uid
		];
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

		$this->app->render($template, $this->data);
	}

	public function requireSession($callback)
	{
		if(isset($_COOKIE['NMRSESSID'])){
			call_user_func($callback, $_COOKIE['NMRSESSID']);
		}else{
			$result = $this->api->get('/customersession');
			if($result['error'] == 0){
				$session_id = $result['data']['session_id'];
				setcookie("NMRSESSID", $session_id, time()+3600 ,'/');
				call_user_func($callback, $session_id);
			}else{
				$this->renderJson(['status' => 1 , 'error' => 'Could not obtain session']);
			}
		}
	}

	public function renderJson($data)
	{
		header('Content-Type: application/json');
		print json_encode($data);
		exit(0);
	}

	public function isAjax()
	{
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

	public function isPost()
	{
		return ($_SERVER['REQUEST_METHOD'] === 'POST');
	}
}