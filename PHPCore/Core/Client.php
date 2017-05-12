<?php
namespace Core;

class Client
{
	private static $ipAddress = null;
	private static $countryCode = null;
	public static function GetIP()
	{
		//return '89.211.49.35';
		//
		if (self::$ipAddress)
			return self::$ipAddress;

		if (getenv('HTTP_CLIENT_IP'))
			self::$ipAddress = getenv('HTTP_CLIENT_IP');
		elseif (getenv('HTTP_X_FORWARDED_FOR'))
			self::$ipAddress = getenv('HTTP_X_FORWARDED_FOR');
		elseif (getenv('HTTP_X_FORWARDED'))
			self::$ipAddress = getenv('HTTP_X_FORWARDED');
		elseif (getenv('HTTP_FORWARDED_FOR'))
			self::$ipAddress = getenv('HTTP_FORWARDED_FOR');
		elseif (getenv('HTTP_FORWARDED'))
			self::$ipAddress = getenv('HTTP_FORWARDED');
		elseif (getenv('REMOTE_ADDR'))
			self::$ipAddress = getenv('REMOTE_ADDR');
		else
			return null;
		return self::$ipAddress;
	}

	public static function GetCountryCode()
	{
		if (self::$countryCode)
			return self::$countryCode;

		if (($val = Request::Request('countryCode')) !== null && in_array($val = strtoupper($val), ['EG', 'QA'])) {
			self::SetCookie('WMS_Client[CountryCode]', $val, false);
			return (self::$countryCode = $val);
		} elseif (($val = self::GetCookie('WMS_Client')) !== null && in_array($val['CountryCode'] = strtoupper($val['CountryCode']), ['EG', 'QA'])) {
			return (self::$countryCode = $val['CountryCode']);
		} else {
			$data = json_decode(file_get_contents('http://ip-api.com/json/' . self::GetIP()), true);
			$countryCode = 'EG';
			if (isset($data['countryCode']))
				$countryCode = strtoupper($data['countryCode']);
			self::SetCookie('WMS_Client[CountryCode]', $countryCode, false);
			return (self::$countryCode = $countryCode);
		}
	}

	public static function SetCookie($name, $value = "", $expire = 0, $path = "/", $httponly = false, $secure = false, $domain = "")
	{
		if (is_null($expire) || $expire === false)
			$expire = DateTime::UTCTimestamp() + (10 * 365 * 24 * 60 * 60);
		\setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}

	public static function GetCookie($key)
	{
		if (isset($_COOKIE[$key]))
			return $_COOKIE[$key];
		return null;
	}
}
