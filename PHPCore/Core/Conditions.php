<?php
namespace Core;

class Conditions
{
	public static function Comparison($x, $opt, $y)
	{
		switch ($opt)
		{
			case '==':
				return $x == $y;
			case '===':
				return $x === $y;
			case '!=':
				return $x != $y;
			case '<>':
				return $x <> $y;
			case '!==':
				return $x !== $y;
			case '>':
				return $x > $y;
			case '<':
				return $x < $y;
			case '>=':
				return $x >= $y;
			case '<=':
				return $x <= $y;
			default:
				return false;
		}
	}

	public static function Logical($x, $opt, $y)
	{
		switch ($opt)
		{
			case 'and':
			case '&&':
				return $x && $y;
			case 'or':
			case '||':
				return $x || $y;
			case 'xor':
				return $x xor $y;
			default:
				return false;
		}
	}
}