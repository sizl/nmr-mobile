<?php

namespace Nmr\Mobile\Controller;
use Nmr;

class RegisterController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', function() {
			$this->render('/account/register.html');
		});
	}
}