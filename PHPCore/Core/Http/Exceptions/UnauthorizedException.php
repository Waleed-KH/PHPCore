<?php
namespace Core\Http\Exceptions;

class UnauthorizedException extends ClientErrorException
{
	public function __construct()
	{
		parent::__construct("401 Unauthorized", 401);
	}
}