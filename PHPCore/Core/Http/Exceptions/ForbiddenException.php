<?php
namespace Core\Http\Exceptions;

class ForbiddenException extends ClientErrorException
{
	public function __construct()
	{
		parent::__construct("403 Forbidden", 403);
	}
}