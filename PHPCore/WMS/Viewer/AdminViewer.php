<?php
namespace WMS\Viewer;

use Core\Request;

class AdminViewer
{
	public static function View($viewer, $data = null)
	{
		if (!isset($data) || !isset($data['fullContainer']))
			$data['fullContainer'] = self::FullContainer();


		Viewer::View('Admin/' . $viewer, $data, false);
	}

	public static function PrintView($viewer, $data = null)
	{
		if (!isset($data) || !isset($data['fullContainer']))
			$data['fullContainer'] = self::FullContainer();


		Viewer::PrintView('Admin/' . $viewer, $data, false);
	}

	public static function Exists($viewer)
	{
		Viewer::Exists('Admin/' . $viewer, false);
	}

	public static function FullContainer()
	{
		return Request::Header('X-Ajax-Container') === '#contentWrapper';
	}

}
