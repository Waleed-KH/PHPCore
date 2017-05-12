<?php
namespace Core;

class String
{
	/**
	 * Convert special characters to HTML entities
	 * Certain characters have special significance in HTML, and should be represented by HTML entities if they are to preserve their meanings. This function returns a string with these conversions made. If you require all input substrings that have associated named entities to be translated, use htmlentities() instead.
	 *
	 * @param string $string The string being converted.
	 * @param int $flags A bitmask of one or more of the following flags, which specify how to handle quotes, invalid code unit sequences and the used document type. The default is ENT_COMPAT | ENT_HTML401 . Available flags constants  Constant Name  Description    ENT_COMPAT  Will convert double-quotes and leave single-quotes alone.   ENT_QUOTES  Will convert both double and single quotes.   ENT_NOQUOTES  Will leave both double and single quotes unconverted.   ENT_IGNORE  Silently discard invalid code unit sequences instead of returning an empty string. Using this flag is discouraged as it » may have security implications .   ENT_SUBSTITUTE  Replace invalid code unit sequences with a Unicode Replacement Character U+FFFD (UTF-8) or &#FFFD; (otherwise) instead of returning an empty string.   ENT_DISALLOWED  Replace invalid code points for the given document type with a Unicode Replacement Character U+FFFD (UTF-8) or &#FFFD; (otherwise) instead of leaving them as is. This may be useful, for instance, to ensure the well-formedness of XML documents with embedded external content.   ENT_HTML401  Handle code as HTML 4.01.   ENT_XML1  Handle code as XML 1.   ENT_XHTML  Handle code as XHTML.   ENT_HTML5  Handle code as HTML 5.
	 * @param string $encoding Defines encoding used in conversion. If omitted, the default value for this argument is ISO-8859-1 in versions of PHP prior to 5.4.0, and UTF-8 from PHP 5.4.0 onwards.
	 *                         For the purposes of this function, the encodings ISO-8859-1 , ISO-8859-15 , UTF-8 , cp866 , cp1251 , cp1252 , and KOI8-R are effectively equivalent, provided the string itself is valid for the encoding, as the characters affected by htmlspecialchars() occupy the same positions in all of these encodings.
	 *                         The following character sets are supported: Supported charsets  Charset  Aliases  Description    ISO-8859-1  ISO8859-1  Western European, Latin-1.   ISO-8859-5  ISO8859-5  Little used cyrillic charset (Latin/Cyrillic).   ISO-8859-15  ISO8859-15  Western European, Latin-9. Adds the Euro sign, French and Finnish letters missing in Latin-1 (ISO-8859-1).   UTF-8    ASCII compatible multi-byte 8-bit Unicode.   cp866  ibm866, 866  DOS-specific Cyrillic charset.   cp1251  Windows-1251, win-1251, 1251  Windows-specific Cyrillic charset.   cp1252  Windows-1252, 1252  Windows specific charset for Western European.   KOI8-R  koi8-ru, koi8r  Russian.   BIG5  950  Traditional Chinese, mainly used in Taiwan.   GB2312  936  Simplified Chinese, national standard character set.   BIG5-HKSCS    Big5 with Hong Kong extensions, Traditional Chinese.   Shift_JIS  SJIS, SJIS-win, cp932, 932  Japanese   EUC-JP  EUCJP, eucJP-win  Japanese   MacRoman    Charset that was used by Mac OS.   ''    An empty string activates detection from script encoding (Zend multibyte), default_charset and current locale (see nl_langinfo() and setlocale() ), in this order. Not recommended.     Note : Any other character sets are not recognized. The default encoding will be used instead and a warning will be emitted.
	 * @param bool $double_encode When double_encode is turned off PHP will not encode existing html entities, the default is to convert everything.
	 *
	 * @return string
	 */
	public static function HtmlSpecialChars($string, $flags = ENT_QUOTES, $encoding = 'UTF-8', $double_encode = true)
	{
		return htmlspecialchars($string, $flags, $encoding, $double_encode);
	}

	public static function Check($str, $minLen = null, $maxLen = null, $pattern = null)
	{
		$strRes = is_string($str);
		if (!$strRes) return false;

		if (isset($minLen))
			$strRes = $strRes && self::CheckMinLen($str, $minLen);
		if (!$strRes) return false;

		if (isset($maxLen))
			$strRes = $strRes && self::CheckMaxLen($str, $maxLen);
		if (!$strRes) return false;

		if (isset($pattern))
			$strRes = $strRes && self::CheckPattern($str, $pattern);

		return $strRes;
	}

	public static function CheckMinLen($str, $minLen)
	{
		return strlen($str) >= $minLen;
	}

	public static function CheckMaxLen($str, $maxLen)
	{
		return strlen($str) <= $maxLen;
	}

	public static function CheckLen($str, $minLen, $maxLen)
	{
		return self::CheckMinLen($str, $minLen) && self::CheckMaxLen($str, $maxLen);
	}

	public static function CheckPattern($str, $pattern)
	{
		return preg_match($pattern, $str);
	}

	public static function XSSFilter($value)
	{
		if (is_string($value))
			$value = self::HtmlSpecialChars($value);
		return $value;
	}

	public static function StartsWith($str, $value)
	{
		return (substr($str, 0, strlen($value)) === $value);
	}

	public static function EndsWith($str, $value)
	{
		return (substr($str, -strlen($value)) === $value);
	}
}