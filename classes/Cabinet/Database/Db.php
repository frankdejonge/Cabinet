<?php

namespace Cabinet\Database;

class Db
{
	/**
	 * Query type contants.
	 */
	const PLAIN = 'Plain';
	const INSERT = 'Insert';
	const SELECT = 'Select';
	const UPDATE = 'Update';
	const DELETE = 'Delete';
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
	
	public static function query($query, $type, $bindings = array())
	{
		return new Query($query, $type, $bindings);
	}
	
	public static function select($columns = array())
	{
		return new Collector\Select($columns);
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