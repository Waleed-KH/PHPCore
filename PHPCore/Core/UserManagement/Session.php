<?php
namespace Core\UserManagement;

use Core\Database\Database;
use Core\DateTime;
use Core\Rand;

class SessionException extends \Exception {}

class SessionNotExisitException extends SessionException {}
class NullUserException extends SessionException {}
class SessionExpiredException extends SessionException {}

// TODO: use DataTable class with this model

class Session
{
	private $_tableName;
	private $_dataTableName;
	private $_sessionName;
	protected $sessionId = null;
	protected $userId = null;
	public $InactivityMaxTime = 7200;
	public $ExpireMaxTime = 604800;
	public static $SweepRatio = 0.75;

	public function __construct($tableName, $sessionName)
	{
		$this->_tableName = $tableName;
		$this->_sessionName = $sessionName;
		$this->_dataTableName = $tableName . '_data';
	}

	private function ClearExpiredSession($force = false)
	{
		if (!$force && (rand(0, 1000) / 1000.0) > self::$SweepRatio)
			return;

		$currentTime = DateTime::UTCTimestamp();
		$sessions = Database::Where('createdTime', $currentTime - $this->ExpireMaxTime, '<')->OrWhere('lastActivity', $currentTime - $this->InactivityMaxTime, '<')->Get($this->_tableName);

		foreach ($sessions as $id)
		{
			Database::Where('sessionId', $id['sessionId'])->Delete($this->_dataTableName);
			Database::Where('sessionId', $id['sessionId'])->Delete($this->_tableName);
		}
	}

	private function CheckSessionExist()
	{
		if (empty($this->sessionId))
			throw new SessionNotExisitException("ERROR: This session is not exisit.");
	}
	private function CheckSessionExpired()
	{
		if ($this->IsExpired())
			throw new SessionExpiredException("ERROR: This session has expired.");
	}

	private function GetSessionId()
	{
		$this->CheckSessionExpired();
		return $this->sessionId;
	}

	public function GetUserId()
	{
		$this->CheckSessionExist();
		$this->CheckSessionExpired();
		return $this->userId;
	}

	public function NewSession($userId)
	{
		if (empty($userId))
			throw new NullUserException("ERROR: UserID cannot be null.");

		$sessionId = Rand::RandStr(128);
		$currentTime = DateTime::UTCTimestamp();

		if (!Database::Insert($this->_tableName, ['sessionId' => $sessionId, 'userId' => $userId, 'createdTime' => $currentTime, 'lastActivity' => $currentTime]))
			return false;

		$this->userId = $userId;
		$this->sessionId = $sessionId;
		$this->UpdateUserCookie();
		$this->ClearExpiredSession();

		return true;
	}

	public function ExistingSession()
	{
		if (!isset($_COOKIE[$this->_sessionName]['sessionId']))
			return false;

		$sessionId = $_COOKIE[$this->_sessionName]['sessionId'];

		$session;
		if (!$session = Database::Where('sessionId', $sessionId)->GetOne($this->_tableName))
		{
			$this->DeleteUserCookie();
			return false;
		}

		$this->sessionId = $sessionId;
		$this->userId = $session['userId'];

		if ($this->IsExpired())
			return false;

		$this->UpdateLastActivity();
		return true;
	}

	private function UpdateUserCookie()
	{
		\setcookie($this->_sessionName . '[sessionId]', $this->sessionId, DateTime::UTCTimestamp() + $this->ExpireMaxTime, '/', '', false, true);
	}
	private function DeleteUserCookie()
	{
		\setcookie($this->_sessionName . '[sessionId]', null, DateTime::UTCTimestamp() - $this->ExpireMaxTime, '/', '', false, true);
	}

	public function SetData($key, $value)
	{
		$this->CheckSessionExist();
		$this->CheckSessionExpired();
		if (Database::Where('sessionId', $this->sessionId)->Where('key', $key)->Has($this->_dataTableName))
			Database::Where('sessionId', $this->sessionId)->Where('key', $key)->Update($this->_dataTableName, ['value' => $value]);
		else
			Database::Insert($this->_dataTableName, ['sessionId' => $this->sessionId, 'key' => $key, 'value' => $value]);
	}

	public function GetData($key)
	{
		$this->CheckSessionExist();
		$this->CheckSessionExpired();
		$value = Database::Where('sessionId', $this->sessionId)->Where('key', $key)->GetValue($this->_dataTableName, 'value');
		return $value;
	}

	public function IsExpired()
	{
		if (empty($this->sessionId))
			return true;

		if ($session = Database::Where('sessionId', $this->sessionId)->GetOne($this->_tableName))
		{
			$currentTime = DateTime::UTCTimestamp();
			if ($session['createdTime'] < $currentTime - $this->ExpireMaxTime ||
				$session['lastActivity'] < $currentTime - $this->InactivityMaxTime)
			{
				$this->DestroySession();
				return true;
			}
			return false;
		}
		else
		{
			$this->EmptySession();
			return true;
		}
	}

	public function RefreshSession()
	{
		$this->CheckSessionExist();
		$this->CheckSessionExpired();

		$currentTime = DateTime::UTCTimestamp();
		Database::Where('sessionId', $this->sessionId)->Update($this->_tableName, ['createdTime' => $currentTime, 'lastActivity' => $currentTime]);
		$this->UpdateUserCookie();
	}

	public function EmptySession()
	{
		$this->sessionId = null;
		$this->userId = null;
		$this->DeleteUserCookie();
	}

	public function DestroySession()
	{
		$this->CheckSessionExist();

		Database::Where('sessionId', $this->sessionId)->Delete($this->_tableName);
		Database::Where('sessionId', $this->sessionId)->Delete($this->_dataTableName);
		$this->EmptySession();
	}

	public function RollSession()
	{
		$this->CheckSessionExist();
		$this->CheckSessionExpired();

		$sessionData = Database::Where('sessionId', $this->sessionId)->Get($this->_dataTableName);
		$userId = $this->userId;

		$this->DestroySession();

		if (!$this->NewSession($userId))
			return false;

		foreach ($sessionData as $data)
			$this->SetData($data['key'], $data['value']);

		return true;
	}

	public function UpdateLastActivity()
	{
		$this->CheckSessionExist();
		$this->CheckSessionExpired();

		Database::Where('sessionId', $this->sessionId)->Update($this->_tableName, ['lastActivity' => DateTime::UTCTimestamp()]);
	}
}
