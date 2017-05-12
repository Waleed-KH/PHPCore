<?php
namespace WMS;
use Core\Database\Database;
use Core\Request;
use WMS\Controller;
use WMS\Admin\Users\User;
use Core\Http\Exceptions\NotFoundException;

class WMS
{
	private static $url;
	private static $controller_name;
	private static $parameters = [];
	public static function Initialize()
	{
		Database::InitializeConnection();
		try {
			//$controller = __NAMESPACE__ . '\\Controller\\' . self::$controller_name;
			//if (!class_exists($controller)) {
			//    if (isset(self::$parameters[0]) && class_exists($newController = $controller . '\\' . self::$parameters[0])) {
			//        unset(self::$parameters[0]);
			//        self::$parameters = array_values(self::$parameters);
			//        $controller = $newController;
			//    } elseif (class_exists($newController = $controller . '\\' . self::$controller_name)){
			//        $controller = $newController;
			//    } else {
			//        $controller = __NAMESPACE__ . '\\Controller\\Pages';
			//        if (!empty(self::$controller_name))
			//            array_unshift(self::$parameters, self::$controller_name);
			//    }
			//}
			self::checkCtrl();
			call_user_func(self::$controller_name . '::Initialize', array_filter(self::$parameters));
		}
		catch (HttpErrorException $e)
		{
			http_response_code($e->getCode());
			die($e->getMessage());
		}
	}

	private static function checkCtrl()
	{
		if ($reqUrl = Request::Request('url')) {
			self::$url = filter_var(trim($reqUrl, '/'), FILTER_SANITIZE_URL);
			self::$controller_name = preg_replace('/\\/.*$/', '', self::$url);
			//$urlParams = explode('/', self::$url);
			//self::$controller_name = isset($urlParams[0]) ? $urlParams[0] : null;
			//unset($urlParams[0]);
			//self::$parameters = array_values($urlParams);
		}

		$ctrlPrefix = __NAMESPACE__ . '\\Controller\\';
		if (self::$controller_name == 'WMS-Admin') {
			$adminUrl = preg_replace('/^[^\\\\]*\\\\*/', '', str_replace('/', '\\', self::$url));
			$adminCtrl = preg_replace('/\\\\.*$/', '', $adminUrl);
			$adminPrams = [];
			$adminCtrlPrefix = $ctrlPrefix . 'Admin\\';
			$ignoreAjax = intval(Request::Request('_ignoreAjax'));
			$isAjax = (Request::IsAjax() || $ignoreAjax);
			if (!$isAjax) {
				$adminCtrl = $adminCtrlPrefix . 'Main';
			} else {
				User::Initialize();
				if (!$adminCtrl || (!User::IsLoggedIn() && $adminCtrl !== 'User')) {
					$adminCtrl = $adminCtrlPrefix . 'User';
					self::$parameters = [];
				} else {
					$aUrl = $adminUrl;
					$ctrlName = '';
					$params = [];
					while ($aUrl) {
						$ctrl = $adminCtrlPrefix . $aUrl;
						$ctrlParam = preg_replace('/^.*\\\\(?!$)/', '', $aUrl);
						if (class_exists($newCtrl = $ctrl) || class_exists($newCtrl = $ctrl . '\\' . $ctrlParam)) {
							$ctrlName = $newCtrl;
							break;
						} else {
							array_unshift($params, $ctrlParam);
							$aUrl = preg_replace('/\\\\?[^\\\\]*$/', '', $aUrl);
						}
					}
					if ($ctrlName) {
						$adminCtrl = $ctrlName;
						$adminPrams = $params;
					} else {
						throw new NotFoundException();
					}
				}
			}
			self::$controller_name = $adminCtrl;
			self::$parameters = $adminPrams;
		} else {
			self::$controller_name = $ctrlPrefix . 'Pages';
			self::$parameters = explode('/', self::$url);
		}
	}
	//private static function SplitURL()
	//{
	//}
}
