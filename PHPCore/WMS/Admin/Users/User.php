<?php
namespace WMS\Admin\Users;

use Core\UserManagement;
use Core\Database\Database;
use Core\Database\DataTable;
use Core\Rand;

class User extends UserManagement\User
{
	const AdminUser = 1;

	private static $_tableName = "users";
	private static $userMng;

	private static function initUser($userObj)
	{
		$userObj->data['name'] = $userObj->data['firstName'] . ((!empty($userObj->data['firstName']) && !empty($userObj->data['lastName']))? " " : "") . $userObj->data['lastName'];

		switch ($userObj->data['userType'])
		{
			case User::AdminUser:
				$userObj->data['userTypeStr'] = 'Admin';
				$userObj->data['namePrefix'] = '';
				break;
			default:
				$userObj->data['userTypeStr'] = '';
				$userObj->data['namePrefix'] = '';
				break;
		}

		return $userObj;
	}

	public static function Initialize()
	{
		self::$userMng = new UserManagement\UserManagement(self::$_tableName, 'WMS_User');
	}

	public static function Login($userId, $password)
	{
		return self::initUser(self::$userMng->Login($userId, $password));
	}

	public static function Logout()
	{
		return self::$userMng->Logout();
	}

	public static function IsLoggedIn()
	{
		return self::$userMng->IsLoggedIn();
	}

	public static function GetLoggedInUser()
	{
		return self::initUser(self::$userMng->GetLoggedInUser());
	}

	public static function LoggedInUserIs($userType)
	{
		return (self::IsLoggedIn() && self::GetLoggedInUser()->userType === $userType);
	}

	public static function GetUserObjectById($id)
	{
		return self::initUser(self::$userMng->GetUserObjectById($id));
	}

	public static function GetById($id)
	{
		return self::GetUserObjectById($id)->data;
	}

	public static function GetUsers($data = [], $limit = null, $obj = true)
	{
		$usersData = self::$userMng->GetUsers($data, $limit);

		for ($i = 0; $i < count($usersData); $i++)
		{
			$userObj = self::initUser($usersData[$i]);
			$usersData[$i] = (($obj) ? $userObj : $userObj->data);
		}

		return $usersData;
	}

	public static function AddUser($data)
	{
		$data['activeCode'] = Rand::RandStr(12);
		return self::$userMng->AddUser($data);
	}

	public static function UpdateUser($id, $data)
	{
		return self::$userMng->UpdateUser($id, $data);
	}

	public static function GetActiveUser($id, $activeCode)
	{
		return self::$userMng->GetActiveUser($id, $activeCode);
		//$user = self::GetUserObjectById($id);
		//if ($user->activeCode !== $activeCode)
		//    throw new InvalidActiveCodeException("Error: Active Code is invalid");

		//return $user;
	}

	public static function ActiveUser($id, $activeCode, $data)
	{
		return self::$userMng->ActiveUser($id, $activeCode, $data);
		//$user = self::GetActiveUser($id, $activeCode);

		//$data['activeCode'] = null;
		//return self::UpdateUser($user->id, $data);
	}

	public static function ValidateUsername($username, $exId = null)
	{
		return !self::$userMng->CheckUserExist($username, $exId);
	}

	public static function DeleteById($userId)
	{
		return self::$userMng->DeleteById($userId);
	}

	public static function ValidateUserEmail($email, $exId = null)
	{
		if (isset($exId))
			Database::Where('id', $exId, '!=');

		return !Database::Where('email', $email)->Has(self::$_tableName);
	}
}
