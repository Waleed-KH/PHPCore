<?php
namespace Core\UserManagement;

use Core\Database\Data;

class User extends Data
{
	protected function __construct($data)
	{
		parent::__construct($data);
	}

	public static function InitializeUser($data)
	{
		$obj = new User($data);
		return $obj;
	}

	public static function InitializeUsers($data)
	{
		$users = [];

		foreach ($data as $user)
			$users[] = self::InitializeUser($user);

		return $users;
	}
}
