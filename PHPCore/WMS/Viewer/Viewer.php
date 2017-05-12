<?php
namespace WMS\Viewer;

use Core\Request;
use Core\Client;

class Viewer
{
	public static function View($viewer, $data = null)
	{
		self::PrintView($viewer, $data); exit();
	}

	public static function PrintView($viewer, $data = null)
	{
		if ($file = self::Exists($viewer)) {
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

	public static function Exists($viewer)
	{
		$viewer = str_replace('/', DS, $viewer);

		if (preg_match('/\\.[A-z1-9]+$/', $viewer) && file_exists(WMS_VIEWER_DIR . $viewer)) {
			return $viewer;
		} else {
			if (preg_match('/\\.html$/i', $viewer)) {
				$file = preg_replace('/\\.html$/i', '.php', $viewer);
				if (file_exists(WMS_VIEWER_DIR . $file))
					return $file;
			}

			foreach (['', 'index'] as $postfix) {
				$viewer = $viewer . $postfix;

				foreach (['.php', '.html'] as $ext) {
					$file = $viewer . $ext;
					if (file_exists(WMS_VIEWER_DIR . $file))
						return $file;
				}

				$viewer = preg_replace('/\\' . DS . '+$/', '', substr($viewer, -strlen($postfix))) . DS;
			}
		}

		return false;
	}
}
