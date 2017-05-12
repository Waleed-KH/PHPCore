<?php
namespace Core\Database;

class StaticDataTable
{
	protected static $_tableName;
	protected static $_primaryKey = 'id';
	protected static $_dataTable;

	public static function Initialize()
	{
		static::$_dataTable = new DataTable(static::$_tableName, static::$_primaryKey);
	}

	public static function Get($numRows = null, $columns = '*', $where = [], $orderBy = [])
	{
		return static::$_dataTable->Get($numRows, $columns, $where, $orderBy);
	}
	public static function GetOne($columns = '*', $where = [], $orderBy = [])
	{
		return static::$_dataTable->GetOne($columns, $where, $orderBy);
	}

	public static function GetById($id, $columns = '*', $where = [])
	{
		return static::$_dataTable->GetById($id, $columns, $where);
	}

	public static function GetValue($column, $where = [], $orderBy = [])
	{
		return static::$_dataTable->GetValue($column, $where, $orderBy);
	}

	public static function GetValues($column, $num = null, $where = [], $orderBy = [])
	{
		return static::$_dataTable->GetValues($column, $num, $where, $orderBy);
	}


	public static function GetValueById($id, $column, $where = [])
	{
		return static::$_dataTable->GetValueById($id, $column, $where);
	}

	public static function Add($data)
	{
		return static::$_dataTable->Add($data);
	}

	public static function Update($data, $where = [])
	{
		return static::$_dataTable->Update($data, $where);
	}

	public static function UpdateById($id, $data, $where = [])
	{
		return static::$_dataTable->UpdateById($id, $data, $where);
	}

	public static function Delete($numRows = null, $where = [])
	{
		return static::$_dataTable->Delete($numRows, $where);
	}

	public static function DeleteById($id, $where = [])
	{
		return static::$_dataTable->DeleteById($id, $where);
	}

	public static function Join($joinCondition, $joinType = 'INNER')
	{
		return static::$_dataTable->Join($joinCondition, $joinType);
	}

	public static function Has($where = [])
	{
		return static::$_dataTable->Has($where);
	}

	public static function IdExists($id, $where = [])
	{
		return static::$_dataTable->IdExists($id, $where);
	}

	public function GetSubQuery($subQueryAlias = "", $numRows = null, $columns = '*', $where = [], $orderBy = [], $groupBy = [])
	{
		return static::$_dataTable->GetSubQuery($subQueryAlias, $numRows, $columns, $where, $orderBy, $groupBy);
	}

	// TODO: Build functions can be static in DataTable class, even Database class
	public static function BuildWhere($where)
	{
		return static::$_dataTable->BuildWhere($where);
	}

	public static function BuildOrderBy($orderBy)
	{
		return static::$_dataTable->BuildOrderBy($orderBy);
	}

	public static function BuildQuery($where = [], $orderBy = [], $id = null)
	{
		return static::$_dataTable->BuildQuery($where, $orderBy, $id);
	}

}
