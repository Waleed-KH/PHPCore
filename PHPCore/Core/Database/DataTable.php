<?php
namespace Core\Database;
use Core\Database\Database;

class DataException extends \Exception {}
class DataIdNotExistException extends DataException {}
class DataAddFailedException extends DataException {}
class DataDeleteFailedException extends DataException {}
class DataUpdateFailedException extends DataException {}

class DataTable
{
	protected $_tableName;
	protected $_primaryKey;

	public function __construct($tableName, $primaryKey = 'id')
	{
		$this->_tableName = $tableName;
		$this->_primaryKey = $primaryKey;
	}

	public function Get($numRows = null, $columns = '*', $where = [], $orderBy = [])
	{
		$this->BuildQuery($where, $orderBy);
		return Database::Get($this->_tableName, $numRows, $columns);
	}

	public function GetOne($columns = '*', $where = [], $orderBy = [])
	{
		$this->BuildQuery($where, $orderBy);
		return Database::GetOne($this->_tableName, $columns);
	}

	public function GetById($id, $columns = '*', $where = [])
	{
		$this->BuildQuery($where, [], $id);
		return $this->GetOne($columns);
	}

	public function GetValue($column, $where = [], $orderBy = [])
	{
		$this->BuildQuery($where, $orderBy);
		return Database::GetValue($this->_tableName, $column);
	}

	public function GetValues($column, $num = null, $where = [], $orderBy = [])
	{
		$this->BuildQuery($where, $orderBy);
		return Database::GetValue($this->_tableName, $column, $num);
	}

	public function GetValueById($id, $column, $where = [])
	{
		$this->BuildQuery($where, [], $id);
		return $this->GetValue($column);
	}

	public function Add($data)
	{
		unset($data[$this->_primaryKey]);

		if ($id = Database::Insert($this->_tableName, $data))
			return $id;
		else
			throw new DataAddFailedException();
	}

	public function Update($data, $where = [])
	{
		unset($data[$this->_primaryKey]);

		$this->BuildQuery($where);
		return Database::Update($this->_tableName, $data);
	}

	public function Count()
	{
		return Database::WithTotalCount();
	}

	public function UpdateById($id, $data, $where = [])
	{
		if (!static::IdExists($id))
			throw new DataIdNotExistException();

		$this->BuildQuery($where, [], $id);

		unset($data[$this->_primaryKey]);

		if (!static::Update($data))
			throw new DataUpdateFailedException();

		return true;
	}

	public function Delete($numRows = null, $where = [])
	{
		$this->BuildWhere($where);
		return Database::Delete($this->_tableName, $numRows);
	}

	public function DeleteById($id, $where = [])
	{
		if (!static::IdExists($id))
			throw new DataIdNotExistException();

		$this->BuildQuery($where, [], $id);

		if (!static::Delete())
			throw new DataDeleteFailedException();

		return true;
	}

	public function Join($joinCondition, $joinType = 'INNER', $alias = '')
	{
		if (empty($joinType)) $joinType = 'INNER';
		return Database::Join($this->_tableName . (empty($alias) ? '' : ' ' . $alias), $joinCondition, $joinType);
	}

	public function Has($where = [])
	{
		$this->BuildWhere($where);
		return Database::Has($this->_tableName);
	}

	public function IdExists($id, $where = [])
	{
		$this->BuildQuery($where, [], $id);
		return $this->Has();
	}

	public function GetSubQuery($subQueryAlias = "", $numRows = null, $columns = '*', $where = [], $orderBy = [], $groupBy = [])
	{
		$sq = Database::SubQuery($subQueryAlias);

		$this->BuildQuery($where, $orderBy, null, $sq);
		if (is_string($groupBy)) $groupBy = [$groupBy];
		if (!empty($groupBy))
			foreach ($groupBy as $col)
				$sq->GroupBy($col);

		return $sq->Get($this->_tableName, $numRows, $columns);
	}

	/**
	 * @param mixed $where
	 * @param MySQL $dbObj
	 */
	public function BuildWhere($where, $dbObj = null)
	{
		if (!isset($dbObj)) $dbObj = Database::GetInstance();

		if (!empty($where))
		{
			$this->initArr($where);
			foreach ($where as $value) {
				if (empty($value))
					continue;
				if (!isset($value[1]))
					$value[1] = 'DBNULL';
				if (!isset($value[2]))
					$value[2] = '=';
				if (!isset($value[3]))
					$value[3] = 'AND';
				$dbObj->Where($value[0], $value[1], $value[2], $value[3]);
			}
		}
	}

	/**
	 * @param mixed $orderBy
	 * @param MySQL $dbObj
	 */
	public function BuildOrderBy($orderBy, $dbObj = null)
	{
		if (!isset($dbObj)) $dbObj = Database::GetInstance();

		if (!empty($orderBy))
		{
			$this->initArr($orderBy);
			foreach ($orderBy as $value) {
				if(!isset($value[1]))
					$value[1] = 'DESC';
				if(!isset($value[2]))
					$value[2] = null;
				$dbObj->OrderBy($value[0], $value[1], $value[2]);
			}
		}
	}

	public function BuildQuery($where = [], $orderBy = [], $id = null, $dbObj = null)
	{
		if (empty($where))
			$where = [];
		else
			$this->initArr($where);
		if (empty($orderBy))
			$orderBy = [];
		else
			$this->initArr($orderBy);

		if (isset($id))
			$where[] = [(Database::HasJoin() ? $this->_tableName . '.' : '') . $this->_primaryKey, $id];

		$this->BuildWhere($where, $dbObj);
		$this->BuildOrderBy($orderBy, $dbObj);

	}

	private function initArr(&$arr)
	{
		if (is_array($arr)) {
			if (!is_array($arr[0]))
				$arr = [$arr];
		} else {
			$arr = [[$arr]];
		}
	}
}
