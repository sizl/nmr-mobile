<?php

namespace Nmr\Mobile\Controller;
use Nmr;

class LogoutController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', function() {

			if($this->session->hasNmrCookie()){
				$result = $this->api->post('/logout', ['session_id' => $_COOKIE['NMRSESSID']]);
				if($result['error'] == 0){
					$this->session->destroy();
				}
			}

			$this->app->redirect('/');
		});
	}
}