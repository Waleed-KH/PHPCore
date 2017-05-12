<?php
namespace WMS\Controller\Admin;

use Core\MVC\IController;
use WMS\Viewer\AdminViewer;

class Main implements IController
{
	public static function Initialize()
	{
		AdminViewer::View('Main');
	}
}
