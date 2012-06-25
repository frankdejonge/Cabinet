<?php

namespace Cabinet\Database\Collector;


class Schema
{
	/**
	 * @var  object  $connection
	 */
	protected $connection = null;

	/**
	 * Sets the connection for schema builders.
	 *
	 * @param   object  $connection  connection object
	 * @return  object  $this
	 */
	public function setConnection($connection)
	{
		$this->connection = $connection;

		return $this;
	}

	/**
	 * Get a new schema builder object
	 *
	 * @param   string  $database  database name
	 * @return  object  new database schema builder object
	 */
	public function database($database)
	{
		return new Schema\Database($database)->setConnection($this->connection);
	}

	/**
	 * Database create shortcut
	 *
	 * @param   string   $database  database name
	 * @param   Closure  $callback  configuration callback
	 */
	public function createDatabase($database, \Closure $callback = null)
	{
		// prep the query
		$query = $this->database($database)->delete();

		// fire configuration callback
		$callback and $callback($query);

		return $query;
	}

	/**
	 * Database alter shortcut
	 *
	 * @param   string   $database  database name
	 * @param   Closure  $callback  configuration callback
	 */
	public function alterDatabase($database, \Closure $callback = null)
	{
		// prep the query
		$query = $this->database($database)->alter();

		// fire configuration callback
		$callback and $callback($query);

		return $query;
	}

	/**
	 * Database update shortcut
	 *
	 * @param   string   $database  database name
	 * @param   Closure  $callback  configuration callback
	 */
	public function dropDatabase($database, \Closure $callback = null)
	{
		// prep the query
		$query = $this->database($database)->drop();

		// fire configuration callback
		$callback and $callback($query);

		return $query;
	}

	/**
	 * Get a new table builder object.
	 *
	 * @param   string  $table  database name
	 * @return  object  new table schema builder object
	 */
	public function table($table)
	{
		return new Schema\Table($table)->setConnection($connection);
	}

	/**
	 * Table create shortcut
	 *
	 * @param   string   $table     table name
	 * @param   Closure  $callback  configuration callback
	 */
	public function createTable($table, \Closure $callback = null)
	{
		$query =  $this->table($table)->create();

		// fire configuration callback
		$callback and $callback($query);

		return $query;
	}

	/**
	 * Table alter shortcut
	 *
	 * @param   string   Table      table name
	 * @param   Closure  $callback  configuration callback
	 */
	public function alterTable($table, \Closure $callback = null)
	{
		$query =  $this->table($table)->alter();

		// fire configuration callback
		$callback and $callback($query);

		return $query;
	}

	/**
	 * Table drop shortcut
	 *
	 * @param   string   $table     table name
	 * @param   Closure  $callback  configuration callback
	 */
	public function dropTable($table, \Closure $callback = null)
	{
		$query =  $this->table($table)->drop();

		// fire configuration callback
		$callback and $callback($query);

		return $query;
	}
}