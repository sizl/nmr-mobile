<?php
namespace Nmr;

class Session {

	public function validateLogin($post, &$error)
	{
		if(empty($post['email_address'])){
			$error = 'Email address cannot be blank';
			return false;
		}

		if(empty($post['password'])){
			$error = 'Password cannot be blank';
			return false;
		}

		return true;
	}

	public function prepareLoginPost(&$post)
	{
		//set ip of client
		$post['ip_address'] = $_SERVER['REMOTE_ADDR'];

		//set cookie if present
		if($this->hasNmrCookie()){
			$post['session_id'] = $_COOKIE['NMRSESSID'];
		}
	}

	public function hasNmrCookie()
	{
		return isset($_COOKIE['NMRSESSID']);
	}

	public function getSessionId()
	{
		return $_COOKIE['NMRSESSID'];
	}

	public function setNmrCookie($session_id)
	{
		setcookie("NMRSESSID", $session_id, time()+3600 ,'/');
	}
}