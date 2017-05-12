<?php
namespace Core\MVC;

use Core\Request;
use Core\Client;

class Viewer
{
	public static function View($viewer, $data = null, $checkCountry = true)
	{
		self::PrintView($viewer, $data, $checkCountry); exit();
	}

	public static function PrintView($viewer, $data = null, $checkCountry = true)
	{
		if ($file = self::Exists($viewer, $checkCountry)) {
			require WMS_VIEWER_DIR . $file;
		} else {
			require WMS_VIEWER_DIR . '404.php';
		}
	}

	public static function PrintJson($data = null)
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data); exit();
	}

	public static function Exists($viewer, $checkCountry = true)
	{
		$viewer = str_replace('/', DS, $viewer);

		if (preg_match('/\\.[A-z1-9]+$/', $viewer) && file_exists(WMS_VIEWER_DIR . $viewer)) {
			return  ($checkCountry ? self::CheckCountryPage($viewer) : $viewer);
		} else {
			if (preg_match('/\\.html$/i', $viewer)) {
				$file = preg_replace('/\\.html$/i', '.php', $viewer);
				if (file_exists(WMS_VIEWER_DIR . $file))
					return ($checkCountry ? self::CheckCountryPage($file) : $file);
			}

			foreach (['', 'index'] as $postfix) {
				$viewer = $viewer . $postfix;

				foreach (['.php', '.html'] as $ext) {
					$file = $viewer . $ext;
					if (file_exists(WMS_VIEWER_DIR . $file))
						return ($checkCountry ? self::CheckCountryPage($file) : $file);
				}

				$viewer = preg_replace('/\\' . DS . '+$/', '', substr($viewer, -strlen($postfix))) . DS;
			}
		}

		return false;
	}

	public static function CheckCountryPage($file)
	{
		$file = preg_replace('/\\.qa\\.php/i', '.php', $file);
		if (Client::GetCountryCode() == 'QA') {
			$cFile = preg_replace('/\\.php/i', '.qa.php', $file);
			if (file_exists(WMS_VIEWER_DIR . $cFile))
				return $cFile;
			else
				return $file;
		} else {
			return $file;
		}
	}
}
