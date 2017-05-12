<?php
namespace Core;

class Request
{
	public static function IsAjax()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

	public static function Post($key, $clean = false)
	{
		if (isset($_POST[$key]))
			return ($clean) ? trim(strip_tags($_POST[$key])) : $_POST[$key];
		return null;
	}

	public static function Get($key)
	{
		if (isset($_GET[$key]))
			return $_GET[$key];
		return null;
	}
	public static function Request($key)
	{
		if (isset($_GET[$key]))
			return $_GET[$key];
		if (isset($_POST[$key]))
			return $_POST[$key];
		return null;
	}

	public static function File($key)
	{
		if (isset($_FILES[$key]))
			return $_FILES[$key];
		return null;
	}

	public static function Header($key)
	{
		//TODO: Cache headers data
		$headers = getallheaders();
		if (isset($headers[$key]))
			return $headers[$key];
		return null;
	}
}
