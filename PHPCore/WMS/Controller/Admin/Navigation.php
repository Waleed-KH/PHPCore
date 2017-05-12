<?php
namespace WMS\Controller\Admin;

use WMS\Admin\Users\User as Usr;

class Navigation
{
	public static function GetNavItems()
	{
		$navItems = [];
		switch (Usr::GetLoggedInUser()->userType)
		{
			case Usr::AdminUser:
				$navItems = [
					['label' => '', 'link' => '/']//,
				];
				break;
		}
		return $navItems;
	}
}
