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
		if ($this->session->hasSessionCookie()) {
			//Is cookied user. Get API Session Id
			call_user_func($callback, $_COOKIE['NMRSESSID']);
		} else {

			//Create new Session
			$result = $this->api->post('/customersession');
			if($result['error'] == 0 && !empty($result['data']['session_id'])){
				$this->session->setSessionCookie($result['data']['session_id']);
				call_user_func($callback, $result['data']['session_id']);
			}else{
				if ($this->slim->request->isAjax()) {
					$this->renderJson(['status' => 1 , 'error' => 'Could not obtain session']);
				} else {
					$this->slim->notFound();
				}
			}
		}
	}

	private function setCustomer()
	{
		$customer = $this->session->defaultCustomer();
		$fb_uid = $this->session->getFacebookUid();
		$customer['fb_uid'] = $fb_uid;

		$logged_in_user = $this->session->getCustomer();

		if (!empty($logged_in_user)) {
			$customer = $logged_in_user;
			$customer['authenticated'] = true;
			$customer['fb_uid'] = $fb_uid;
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
		//default to scaffolded view path
		$this->template = $this->defaultTemplate;

		//render alternate view template and merge view data if present
		if (!empty($template) && is_string($template)) {
			$this->template = $template;
		}

		//merge action view data into main view data
		if (!empty($template) && is_array($template)) {
			$this->data = array_merge($this->data, $template);
		}

		if (!empty($data) && is_array($data)) {
			$this->data = array_merge($this->data, $data);
		}

		$this->slim->render($this->template, $this->data);
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