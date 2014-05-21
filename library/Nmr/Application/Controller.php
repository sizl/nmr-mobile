<?php

namespace Nmr\Application;

use Nmr;
use Slim\Slim;
use Handlebars\Handlebars;

class Controller {

	protected $slim;
	protected $config;
	protected $data = [];
	protected $api;
	protected $session;
	protected $handlebars;
	protected $defaultTemplate;
	protected $template;

	public function __construct(Slim $slim)
	{
		$this->slim = $slim;
		$this->config = require(APP_ROOT . '/config/config.php');

		$this->api = new Nmr\ApiClient($this->config['api']);
		$this->session = new Nmr\Session($this->api);

		$this->setCustomer();
		$this->setJsOptions();
	}

	private function getConfig($key)
	{
		return $this->config[$key];
	}

	public function requireSession($callback)
	{
		if($this->session->hasCookie()){
			call_user_func($callback, $_COOKIE['NMRSESSID']);
		}else{
			$result = $this->api->post('/customersession');

			if($result['error'] == 0){
				$this->session->setSessionCookie($result['data']['session_id']);
				call_user_func($callback, $result['data']['session_id']);
			}else{
				$this->renderJson(['status' => 1 , 'error' => 'Could not obtain session']);
			}
		}
	}

	private function setCustomer()
	{
		$fb_uid = $this->session->getFacebookUid($_COOKIE);
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
		$fbConfig = $this->getConfig('facebook');

		$this->data['facebook'] = [
			'appId' => $fbConfig['appId'],
			'permissions' => $fbConfig['permissions'],
			'fields' => $fbConfig['userFields']
		];
	}

	/*
	 * Renders default template based on scaffolding
	 * or can send in a different template
	 * @param mixed $template - template name or data
	 */
	public function render($template='', array $data = [])
	{
		//render alternate view template and merge view data if present
		if (is_string($template)) {

			if (is_array($data)) {
				$this->data = array_merge($this->data, $data);
			}

			$this->template = $template;

			$this->slim->render($template, $this->data);
			return;
		}

		//render default template with view data
		if (is_array($template)) {
			$this->data = array_merge($this->data, $template);
		}

		$this->template = $this->defaultTemplate;

		$this->slim->render($this->defaultTemplate, $this->data);
	}

	public function getTemplate()
	{
		return $this->template;
	}

	public function setDefaultViewTemplate($controller, $action)
	{
		$this->defaultTemplate = strtolower($controller) . '/' . strtolower($action) . '.html';
	}

	public function renderJson($data)
	{
		header('Content-Type: application/json');
		print json_encode($data);
		exit(0);
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

	protected function getPost()
	{
		return $this->slim->request->post();
	}
}