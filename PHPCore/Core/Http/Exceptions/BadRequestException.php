<?php
namespace Core\Http\Exceptions;

class BadRequestException extends ClientErrorException
{
	public function __construct()
	{
		parent::__construct("400 Bad Request", 400);
	}
}