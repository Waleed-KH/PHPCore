<?php
namespace Core;

class DateTime extends \DateTime
{
	public static function UTCNow()
	{
		return new DateTime('now', new \DateTimeZone('UTC'));
	}
	
	public static function UTCTimestamp()
	{
		return self::UTCNow()->getTimestamp();
	}

	public static function StrToTimestamp($time)
	{
		return (new DateTime($time, new \DateTimeZone('UTC')))->getTimestamp();
	}

	public static function TimestampFormat($time, $format)
	{
		$date = new DateTime();
		$date->setTimestamp($time);
		return $date->format($format);
	}

	public static function TimestampDateFormat($time)
	{
		return self::TimestampFormat($time, 'Y-m-d');
	}
}
