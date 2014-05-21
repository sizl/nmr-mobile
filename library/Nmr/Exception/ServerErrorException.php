<?php

namespace Nmr\Exception;


/**
 * Class ServerErrorException
 *
 * @package Nmr\Exception
 */
class ServerErrorException extends \Exception
{
	protected $code = 500;
}