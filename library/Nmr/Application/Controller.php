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

	/*
	 * Api Client
	 */

	protected $api;

	/*
	 * Session Handler
	 */

	protected $session;

	public function __construct(Slim $app, $controller, $action)
	{
		$this->app = $app;

		$this->action = $action;
		$this->controller = $controller;

		$this->api = new Nmr\ApiClient();
		$this->session = new Nmr\Session();

		$this->setupRouteFromUri();
		$this->setGlobalViewData();
		$this->setupAuthentication();
	}

	private function setGlobalViewData()
	{
		$this->data['app_id'] = \Nmr\Facebook::APP_ID;
	}

	private function setupRouteFromUri()
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

	private function getCustomer()
	{
		$session_id = $this->session->getSessionId();
		$result = $this->api->get('/customersession', ['session_id' => $session_id]);

		if(isset($result['data']['customer'])){
			return $result['data']['customer'];
		}

		return [];
	}

	private function getFacebookUid(){

		//Check if site is FB Connected
		if(isset($_COOKIE['fb_uid']) && isset($_COOKIE['fbsr_' . Nmr\Facebook::APP_ID])){
			return $_COOKIE['fb_uid'];
		}

		$facebook = \Nmr\Facebook::instance();
		$fb_uid = $facebook->getUser();

		if($fb_uid){
			setcookie("fb_uid", $fb_uid, time()+3600 ,'/');
		}

		return $fb_uid;
	}

	private function setupAuthentication()
	{
		$customer = [];
		$authenticated = false;

		if($this->session->hasNmrCookie()){
			$customer = $this->getCustomer();
		}

		if(!empty($customer)){
			$authenticated = true;
		}

		$fb_uid = $this->getFacebookUid();

		$data = [
			'authenticated' => $authenticated,
			'fbconnected' => !empty($fb_uid),
			'fb_uid' => $fb_uid
		];

		if(!empty($customer)){
			$this->data['customer'] = array_merge($data, $customer);
		}else{
			$this->data['customer'] = $data;
		}
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
}