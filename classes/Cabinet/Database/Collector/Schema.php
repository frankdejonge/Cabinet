<?php

namespace Cabinet\Database\Collector;


class Schema
{
	/**
	 * Get a new schema builder object
	 *
	 * @param   string  $database  database name
	 * @return  object  new database schema builder object
	 */
	public function database($database)
	{
		return new Schema\Database($database);
	}

	/**
	 * Database delete shortcut
	 *
	 * @param   string   $database  database name
	 * @param   Closure  $callback  configuration callback
	 */
	public function deleteDatabase($database, \Closure $callback = null)
	{
		// prep the query
		$query = $this->database($database)->delete();

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
	public function updateDatabase($database, \Closure $callback = null)
	{
		// prep the query
		$query = $this->database($database)->update();

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
	public function table($table, $database = null)
	{
		return new Schema\Table($table, $database);
	}
}