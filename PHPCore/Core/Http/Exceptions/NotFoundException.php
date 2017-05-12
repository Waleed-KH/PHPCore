<?php
namespace Core\Http\Exceptions;

class NotFoundException extends ClientErrorException
{
	public function __construct()
	{
		parent::__construct("404 Not Found", 404);
	}
}