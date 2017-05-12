<?php

namespace Core\Database;

class Database
{
	private static $mysql;

	public static function InitializeConnection()
	{
		self::$mysql = new MySQL('localhost', 'root', '', '', 3306);
		self::$mysql->connect();
	}

	public static function Get($tableName, $numRows = null, $columns = '*')
	{
		return self::$mysql->Get($tableName, $numRows, $columns);
	}

	public static function GetOne($tableName, $columns = '*')
	{
		return self::$mysql->GetOne($tableName, $columns);
	}

	public static function GetValue($tableName, $column, $limit = 1)
	{
		return self::$mysql->GetValue($tableName, $column, $limit);
	}

	public static function Where($whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
	{
		return self::$mysql->Where($whereProp, $whereValue, $operator, $cond);
	}

	public static function OrderBy($orderByField, $orderbyDirection = "DESC", $customFields = null)
	{
		return self::$mysql->OrderBy($orderByField, $orderbyDirection, $customFields);
	}

	public static function GroupBy($groupByField)
	{
		return self::$mysql->GroupBy($groupByField);
	}

	public static function Having($havingProp, $havingValue = 'DBNULL', $operator = '=', $cond = 'AND')
	{
		return self::$mysql->Having($havingProp, $havingValue, $operator, $cond);
	}

	public static function Join($joinTable, $joinCondition, $joinType = 'INNER')
	{
		return self::$mysql->Join($joinTable, $joinCondition, $joinType);
	}

	public static function Update($tableName, $tableData)
	{
		return self::$mysql->Update($tableName, $tableData);
	}

	public static function Insert($tableName, $insertData)
	{
		return self::$mysql->Insert($tableName, $insertData);
	}

	public static function Map($idField)
	{
		return self::$mysql->Map($idField);
	}

	public static function Increment($num = 1)
	{
		return self::$mysql->inc($num);
	}

	public static function Delete($tableName, $numRows = null)
	{
		return self::$mysql->Delete($tableName);
	}

	public static function Has($tableName)
	{
		return self::$mysql->Has($tableName);
	}

	public static function WithTotalCount()
	{
		return self::$mysql->WithTotalCount();
	}

	public static function TotalCount()
	{
		return self::$mysql->totalCount;
	}

	public static function HasJoin()
	{
		return self::$mysql->HasJoin();
	}


	public static function SubQuery($subQueryAlias = "")
	{
		return MySQL::SubQuery($subQueryAlias);
	}

	public static function QueryArray($array)
	{
		if (!empty($array['where']))
			foreach ($array['where'] as $where)
				Database::Where($where[0], $where[1]);
		if (!empty($array['orderBy']))
			foreach ($array['orderBy'] as $orderBy)
				Database::OrderBy($orderBy[0], $orderBy[1]);
	}

	public static function Func($expr, $bindParams = null)
	{
		return self::$mysql->Func($expr, $bindParams);
	}

	/**
	 * Get MySQL instance
	 * @return MySQL
	 */
	public static function GetInstance()
	{
		return self::$mysql;
	}
}
