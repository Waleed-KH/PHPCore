<?php
namespace Core\UserManagement;

use Core\Database\Database;
use Core\DateTime;
use Core\Client;

class UserNotExistsException extends UserException {}

class WrongPasswordException extends UserException
{
	protected $_userId;

	public function __construct($message = "", $userId = 0)
	{
		$this->_userId = $userId;
		parent::__construct($message);
	}

	public function GetUserID()
	{
		return $this->_userId;
	}
}

class InvalidUsernamePasswordException extends UserException {}
class UserLockedException extends UserException {}
class BruteForceAttackDetectedException extends WrongPasswordException {}
class UserExistsException extends UserException {}
class UserIpBlockedException extends UserException {}
class UserLoginFailedException extends UserException {}
class UserAddFailedException extends UserException {}
class UserNotLoggedInException extends UserException {}
class UserActivisionFailedException extends UserException {}
class InvalidActiveCodeException extends UserNotExistsException {}

// TODO: use DataTable class with this model

class UserManagement
{
	private $_tableName;
	private $_attemptTable;
	private $_blockIpTable;
	private $session;
	private $loggedIn = false;
	private $userObj;

	public function __construct($tableName, $sessionName)
	{
		$this->_tableName = $tableName;

		$this->_attemptTable = $tableName . '_attempts';
		$this->_blockIpTable = $tableName . '_blocked_ip';
		$this->session = new Session($this->_tableName . '_sessions', $sessionName);
		$this->loggedIn = $this->session->ExistingSession();

	}

	public function Login($username, $password)
	{
		if ($this->IsBlocked(Client::GetIP()))
			throw new UserIpBlockedException("Error: User ip blocked");

		try {
			$userObj = $this->GetUserObject(self::XSSFilter($username), self::XSSFilter($password));

			if ($userObj->isLocked == 1)
				throw new UserLockedException("Error: This account is locked");

			if (!$this->session->NewSession($userObj->id))
				throw new UserLoginFailedException("Error: User login failed");

			$this->loggedIn = true;
			$this->userObj = $userObj;
			$this->DeleteAttempt($this->userObj->id, Client::GetIP());

			return $userObj;
		}
		catch (WrongPasswordException $e) {
			if ($this->IsBruteForce($e->GetUserID(), Client::GetIP()))
				throw new BruteForceAttackDetectedException("Error: Brute Force Attack Detected.");
			else {
				$this->LogAttempt($e->GetUserID(), Client::GetIP());
				throw new InvalidUsernamePasswordException("Error: Username or Password is invalid");
			}
		}
		catch (UserNotExistsException $e) {
			if ($this->IsBruteForce(0, Client::GetIP()))
				throw new BruteForceAttackDetectedException("Error: Brute Force Attack Detected.");
			else {
				$this->LogAttempt(0, Client::GetIP());
				throw new InvalidUsernamePasswordException("Error: Username or Password is invalid");
			}
		}
	}

	public function Logout()
	{
		if ($this->IsLoggedIn())
			$this->session->DestroySession();
	}

	public function IsLoggedIn()
	{
		return $this->loggedIn;
	}

	public function GetLoggedInUser()
	{
		if (!$this->loggedIn)
			throw new UserNotLoggedInException();

		if (empty($this->userObj))
			$this->userObj = $this->GetUserObjectById($this->session->GetUserId());

		return $this->userObj;
	}

	private function LogAttempt($userId, $userIp)
	{
		$currentTime = DateTime::UTCTimestamp();

		if (empty($userId)) $userId = 0;

		if (Database::Where('userId', $userId)->Where('userIp', $userIp)->Has($this->_attemptTable)) {
			Database::Where('userId', $userId)->Where('userIp', $userIp)->Update($this->_attemptTable, ['last_login_attempt' => $currentTime, 'total_login_attempt' => Database::Increment()]);
			return true;
		} else {
			Database::Insert($this->_attemptTable, ['userId'=> $userId, 'userIp'=> $userIp, 'first_login_attempt' => $currentTime, 'last_login_attempt' => $currentTime, 'total_login_attempt' => 1]);
			return false;
		}
	}

	private function DeleteAttempt($userId, $userIp)
	{
		if (!empty($userId) || $userId == 0)
			Database::Where('userId', $userId);
		if (!empty($userIp))
			Database::Where('userIp', $userIp);

		Database::Delete($this->_attemptTable);

	}

	private function IsBruteForce($userId, $userIp)
	{
		$currentTime = DateTime::UTCTimestamp();

		$userAttempt = Database::Where('userId', $userId)->GroupBy('userId')->GetOne($this->_attemptTable, ['MIN(first_login_attempt) AS first', 'MAX(last_login_attempt) AS last', 'SUM(total_login_attempt) AS total']);
		$ipAttempt = Database::Where('userIp', $userIp)->GroupBy('userIp')->GetOne($this->_attemptTable, ['MIN(first_login_attempt) AS first', 'MAX(last_login_attempt) AS last', 'SUM(total_login_attempt) AS total']);
		$userIpAttempt = Database::Where('userId', $userId)->Where('userIp', $userIp)->GetOne($this->_attemptTable, ['first_login_attempt AS first', 'last_login_attempt AS last', 'total_login_attempt AS total']);

		if ($userIpAttempt && ((($currentTime - $userIpAttempt['last']) <= 1) ||
			((($currentTime - $userIpAttempt['first']) <= 25) && ($userIpAttempt['total'] >= 5)) ||
			((($currentTime - $userIpAttempt['first']) <= 86400) && ($userIpAttempt['total'] >= 7)) ||
			($userIpAttempt['total'] >= 10)))
		{
			$this->BlockIp($userIp);
			$this->DeleteAttempt($userId, $userIp);
			return true;
		}
		if ($ipAttempt && (((($currentTime - $ipAttempt['first']) <= 35) && ($ipAttempt['total'] >= 7)) ||
			((($currentTime - $ipAttempt['first']) <= 86400) && ($ipAttempt['total'] >= 7)) ||
			($ipAttempt['total'] >= 10)))
		{
			$this->BlockIp($userIp);
			$this->DeleteAttempt(null, $userIp);
			return true;
		}
		if ($userAttempt && (((($currentTime - $userAttempt['first']) <= 50) && ($userAttempt['total'] >= 10)) ||
			((($currentTime - $userAttempt['first']) <= 86400) && ($userAttempt['total'] >= 7)) ||
			($userAttempt['total'] >= 10)))
		{
			$this->LockUser($userId);
			$this->DeleteAttempt($userId, null);
			return true;
		}

		return false;
	}

	private function BlockIp($ip)
	{
		$currentTime = DateTime::UTCTimestamp();

		if (Database::Where('ip', $ip)->Has($this->_blockIpTable))
			Database::Where('ip', $ip)->Update($this->_blockIpTable, ['blockTime' => $currentTime, 'blockTimes' => Database::Increment()]);
		else
			Database::Insert($this->_blockIpTable, ['ip' => $ip, 'blockTime' => $currentTime, 'blockTimes' => 1]);
	}

	private function LockUser($userId)
	{
		Database::Where('id', $userId)->Update($this->_tableName, ['isLocked' => 1]);
	}

	private function IsBlocked($ip)
	{
		$lockedIp = Database::Where('ip', $ip)->GetOne($this->_blockIpTable);

		if (!$lockedIp)
			return false;
		else
		{
			$currentTime = DateTime::UTCTimestamp();

			if ($lockedIp['blockTimes'] == 0)
				return true;

			switch ($lockedIp['blockTimes'] % 3)
			{
				case 1:
					return (($currentTime - $lockedIp['blockTime']) < 1800);
				case 2:
					return (($currentTime - $lockedIp['blockTime']) < 7200);
				case 0:
					return (($currentTime - $lockedIp['blockTime']) < 86400);
			}

		}
	}

	private function IsLocked($userId)
	{
		return Database::Where('id', $userId)->Where('isLocked', 1)->Has($this->_tableName);
	}

	public function CheckUserExist($username, $exId = null)
	{
		if (isset($exId))
			Database::Where('id', $exId, '!=');
		return Database::Where('username', $username)->Has($this->_tableName);
	}

	public function CheckUserIdExist($id)
	{
		return Database::Where('id', $id)->Has($this->_tableName);
	}

	public function CheckActiveCode($id, $activeCode, $bruteForce = true)
	{
		if ($bruteForce && $this->IsBlocked(Client::GetIP()))
			throw new UserIpBlockedException("Error: User ip blocked");

		if (Database::Where('id', $id)->Where('isActive', 0)->Where('activeCode', $activeCode)->Has($this->_tableName))
			return true;
		elseif ($bruteForce && $this->IsBruteForce(0, Client::GetIP()))
			throw new BruteForceAttackDetectedException("Error: Brute Force Attack Detected.");
		else {
			if ($bruteForce)
				$this->LogAttempt(0, Client::GetIP());
			return false;
		}
	}

	public function GetActiveUser($id, $activeCode, $bruteForce = true)
	{
		if ($bruteForce && $this->IsBlocked(Client::GetIP()))
			throw new UserIpBlockedException("Error: User ip blocked");

		Database::Where('isActive', 0);
		$user = $this->GetUserObjectById($id);
		if ($user->activeCode !== $activeCode) {
			if ($bruteForce && $this->IsBruteForce(0, Client::GetIP()))
				throw new BruteForceAttackDetectedException("Error: Brute Force Attack Detected.");
			else {
				if ($bruteForce)
					$this->LogAttempt(0, Client::GetIP());
				throw new InvalidActiveCodeException("Error: Active Code is invalid");
			}
		}

		return $user;
	}

	public function ActiveUser($id, $activeCode, $data)
	{
		$user = $this->GetActiveUser($id, $activeCode);

		$data['activeCode'] = null;
		$data['isActive'] = 1;
		$this->UpdateUser($id, $data);

		return $user;
	}

	public function DeleteById($userId)
	{
		return Database::Where('id', $userId)->Delete($this->_tableName);
	}

	private function GetUserId($username)
	{
		return Database::Where('username', $username)->GetValue($this->_tableName, 'id');
	}

	private function GetUserObject($username, $password)
	{
		$userData = Database::Where('username', $username)->GetOne($this->_tableName);
		if (!$userData)
			throw new UserNotExistsException("Error: User Not found.");

		if (!self::VerifyPassword($password, $userData['password']))
			throw new WrongPasswordException("Error: Wrong Password.", $userData['id']);

		unset($userData['password']);

		return User::InitializeUser($userData);
	}

	public function GetUserObjectById($id)
	{
		$userData = Database::Where('id', $id)->GetOne($this->_tableName);
		if (!$userData)
			throw new UserNotExistsException("Error: User Not found.");

		unset($userData['password']);

		return User::InitializeUser($userData);
	}

	public function GetUsers($data = [], $limit = null)
	{
		$usersData = Database::Get($this->_tableName, $limit, $data);
		for ($i = 0; $i < count($usersData); $i++)
		{
			if (isset($usersData[$i]['password']))
				unset($usersData[$i]['password']);
		}

		return User::InitializeUsers($usersData);
	}

	public function AddUser($data)
	{
		if (isset($data['username']) && Database::Where('username', $data['username'])->Has($this->_tableName))
			throw new UserExistsException("Error: username is used before.");

		if ($userId = Database::Insert($this->_tableName, $data))
			return $userId;
		else
			throw new UserAddFailedException("Error: user adding faild.");
	}

	public function UpdateUser($id, $data)
	{
		if (isset($data['username']) && self::CheckUserExist($data['username'], $id))
			throw new UserExistsException("Error: username is used before");

		unset($data['id']);

		if (isset($data['password']))
			$data['password'] = self::HashPassword($data['password']);

		if (!Database::Where('id', $id)->Update($this->_tableName, $data))
			throw new UserNotExistsException("Error: User Not found.");
	}

	public static function HashPassword($password)
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}

	public static function VerifyPassword($password, $hash)
	{
		return password_verify($password, $hash);
	}

	public static function XSSFilter($value)
	{
		if (is_string($value))
			$value = htmlspecialchars($value, ENT_QUOTES, 'utf-8');
		return $value;
	}
}
