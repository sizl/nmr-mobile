<?php

namespace Nmr;

class Facebook {

	const APP_ID = 575448139145877;
	const APP_SECRET = 'dd68ab5aebf2eb05f89372ffe5b37507';

	public $facebook;

	static public function instance()
	{
		return new \Facebook([
			'appId' => self::APP_ID,
			'secret' => self::APP_SECRET,
			'fileUpload' => false,
			'allowSignedRequest' => false
		]);
	}
}
