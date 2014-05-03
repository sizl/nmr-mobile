<?php

namespace Nmr\Application;

use Nmr;
use Slim\Slim;
use Handlebars\Handlebars;

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

	/*
	 * Api Client
	 */

	protected $api;

	/*
	 * Session Handler
	 */

	protected $session;

	protected $handlebars;

	public function __construct(Slim $app, $controller, $action)
	{
		$this->app = $app;

		$this->action = $action;
		$this->controller = $controller;

		$this->api = new Nmr\ApiClient();
		$this->session = new Nmr\Session($this->api);

		$this->setupRoute();
		$this->setCustomer();
		$this->setJsOptions();
	}

	private function setCustomer()
	{
		$fb_uid = $this->session->getFacebookUid();
		$customer = $this->session->defaultCustomer();
		$customer['fb_uid'] = $fb_uid;

		if ($this->session->isAuthenticated()) {
			$logged_in_user = $this->session->getCustomer();
			if (!empty($logged_in_user)) {
				$customer = $logged_in_user;
				$customer['authenticated'] = true;
				$customer['fb_uid'] = $fb_uid;
			}
		}

		$this->data['customer'] = $customer;
	}

	private function setJsOptions()
	{
		$this->data['facebook'] = [
			'appId' => \Nmr\Facebook::APP_ID,
			'permissions' => \Nmr\Facebook::$permissions,
			'fields' => \Nmr\Facebook::$fields
		];
	}

	private function setupRoute()
	{
		$route = '/';
		if($this->controller != 'index'){
			$route .= $this->controller;
		}
		if($this->action != 'index'){
			$route .= '/'. $this->action;
		}

		$this->route = $route;
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
	 * @param mixed $template - template name or data
	 */
	public function render($template='', array $data = [])
	{
		//$data provided as first parameter. use scaffolded template
		//and merge data to global view data
		if(is_array($template) && !empty($template) && empty($data)) {
			$this->data = array_merge($this->data, $template);
			$template = $this->controller . '/' . $this->action . '.html';
			$this->app->render($template, $this->data);
			return;
		}

		//render scaffolded view template
		if(empty($template) && empty($data)){
			$template = $this->controller . '/' . $this->action . '.html';
			$this->app->render($template, $this->data);
			return;
		}

		//render alternate view template and merge view data if present
		if(!is_array($template)){
			if(!empty($data)){
				$this->data = array_merge($this->data, $data);
			}
			$this->app->render($template, $this->data);
			return;
		}
	}

	public function requireSession($callback)
	{
		if($this->session->hasNmrCookie()){
			call_user_func($callback, $_COOKIE['NMRSESSID']);
		}else{
			$result = $this->api->post('/customersession');

			if($result['error'] == 0){
				$this->session->setNmrCookie($result['data']['session_id']);
				call_user_func($callback, $result['data']['session_id']);
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

	protected function getHandlebarsTemplate($template_name)
	{
		return file_get_contents(MODULE_PATH .'/views/' . $template_name);
	}

	protected function renderHandlebars($template, $data = [])
	{
		if(!isset($this->handlebars)){
			$this->handlebars = new Handlebars();
		}

		return $this->handlebars->render($template, $data);
	}
}