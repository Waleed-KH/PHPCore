<?php
namespace Core\Http\Exceptions;

class ClientErrorException extends \Exception
{
	public function __construct($message = "Http Client Error", $code = 400)
	{
		parent::__construct($message, $code);
	}
}