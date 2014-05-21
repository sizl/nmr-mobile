<?php

namespace Nmr;

class FacebookClient {

	protected $appId;
	protected $appSecret;

	public function __construct($config)
	{
		$this->appId = $config['appId'];
		$this->appSecret = $config['appSecret'];
	}

	public function getUser()
	{
		$fb = $this->getClient();
		return $fb->getUser();
	}

	public function getClient()
	{
		return new \Facebook([
			'appId' => $this->appId,
			'secret' => $this->appSecret,
			'fileUpload' => false,
			'allowSignedRequest' => false
		]);
	}

	public function hasFbAppCookie()
	{
		return isset($_COOKIE['fbsr_' . $this->appId]);
	}
}
