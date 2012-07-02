<?php

namespace Cabinet\Database;

class Db
{
	/**
	 * Query type contants.
	 */
	const PLAIN                 = 'Plain';
	const INSERT                = 'Insert';
	const SELECT                = 'Select';
	const UPDATE                = 'Update';
	const DELETE                = 'Delete';
	const DATABASE_DROP         = 'DatabaseDrop';
	const DATABASE_ALTER        = 'DatabaseAlter';
	const DATABASE_CREATE       = 'DatabaseCreate';
	const TABLE_DROP            = 'TableDrop';
	const TABLE_ALTER           = 'TableAlter';
	const TABLE_CREATE          = 'TableCreate';
	/**
	 * Retrieve a database connection.
	 *
	 * @param   array   $config  database connection config
	 * @return  object  a new Cabinet\Database\Cconnection\[type] object
	 */
	public static function connection($config = array())
	{
		return Connection::instance($config);
	}

	/**
	 * Database expression shortcut.
	 *
	 * @param   mixed  $expression
	 * @return  object  a new Cabinet\Database\Expression object.
	 */
	public static function expr($expression)
	{
		return new Expression($expression);
	}
	
	public static function increment($field, $by = null)
	{
		$increment = new Expression\Increment($field);
		$by and $increment->by($by);
		
		return $increment;
	}
	
	public static function query($query, $type, $bindings = array())
	{
		return new Query($query, $type, $bindings);
	}
	
	public static function select($column = null)
	{
		$query =  new Collector\Select();
		return $query->selectArray(func_get_args());
	}
	
	public static function selectArray($columns = array())
	{
		return static::select()->selectArray($columns);
	}
	
	public static function update($table, $columns = array())
	{
		return new Collector\Update($table, $columns);
	}
	
	public static function delete($table, $columns = array())
	{
		return new Collector\Delete($table, $columns);
	}
	
	public static function insert($table, $columns = array())
	{
		return new Collector\Insert($table, $columns);
	}
}