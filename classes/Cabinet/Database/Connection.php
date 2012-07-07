<?php

namespace Cabinet\Database;

abstract class Connection
{
	/**
	 * Returns a connection instance based on the config.
	 *
	 * @param   array   connection config
	 * @return  object  a new connection instance
	 * @throws  Cainet\Database\Exception   when connection
	 */
	public static function instance($config = array())
	{
		$config = $config + array(
			'type' => 'pdo',
			'driver' => null,
		);

		$class = ucfirst(strtolower($config['type']));
		$config['driver'] and $class .= '\\'.ucfirst(strtolower($config['driver']));

		if( ! class_exists($class = __NAMESPACE__.'\\Connection\\'.$class))
		{
			throw new Exception('Cannot load database connection: '.$class);
		}

		return new $class($config);
	}

	/**
	 * @var collection of executed queries
	 */
	protected $queries = array();

	/**
	 * Returnes the last executed query.
	 *
	 * @return  mixed  last executed query
	 */
	public function lastQuery()
	{
		return ($last = end($this->queries)) ? $last['query'] : null;
	}

	/**
	 * Returns an array of fired queries.
	 *
	 * @retun  array  fired queries
	 */
	public function queries()
	{
		return array_map(function($i){
			return $i['query'];
		}, $this->queries);
	}

	/**
	 * Returns the fired queries with profiling data.
	 *
	 * @return  array  profiler data about the queries
	 */
	public function profilerQueries()
	{
		return $this->queries;
	}

	/**
	 * Run transactional queries.
	 *
	 * @param   closure  $callback  transaction callback
	 * @return  mixed    callback result
	 * @throws
	 */
	public function transaction(\Closure $callback)
	{
		// start the transaction
		$this->startTransaction();

		try
		{
			// execute the callback
			$result = $callback($this);
		}
		// catch any errors generated in the callback
		catch (Exception $e)
		{
			// rolleback on error
			$this->rollbackTransaction();
			throw $e;
		}

		// all fine, commit the transaction
		$this->commitTransaction();
		return $result;
	}

	/**
	 * Retrieve a Cabinet\Database\Query object with the current connection.
	 *
	 * @param   mixed    $query     query
	 * @param   integer  $type      query type
	 * @param   array    $bindings  query bindings
	 */
	public function query($query, $type, $bindings = array())
	{
		return Db::query($query, $type, $bindings)->setConnection($this);
	}

	/**
	 * Retrieve a Cabinet\Database\Collector\Select object with the current connection.
	 *
	 * @param   mixed   $column
	 * @return  object  Cabinet\Database\Collector\Select object
	 */
	public function select($column = null)
	{
		return Db::selectArray(func_get_args())->setConnection($this);
	}

	/**
	 * Retrieve a Cabinet\Database\Collector\Select object with the current connection.
	 *
	 * @param   array   $columns
	 * @return  object  Cabinet\Database\Collector\Select object
	 */
	public function selectArray($columns = array())
	{
		return Db::selectArray($columns)->setConnection($this);
	}

	/**
	 * Retrieve a Cabinet\Database\Collector\Update object with the current connection.
	 *
	 * @param   string  $table
	 * @return  object  Cabinet\Database\Collector\Update object
	 */
	public function update($table)
	{
		return Db::update($table)->setConnection($this);
	}

	/**
	 * Retrieve a Cabinet\Database\Collector\Delete object with the current connection.
	 *
	 * @param   string  $table
	 * @return  object  Cabinet\Database\Collector\Delete object
	 */
	public function delete($table)
	{
		return Db::delete($table)->setConnection($this);
	}

	/**
	 * Retrieve a Cabinet\Database\Collector\Insert object with the current connection.
	 *
	 * @param   string  $table
	 * @return  object  Cabinet\Database\Collector\Insert object
	 */
	public function insert($table)
	{
		return Db::insert($table)->setConnection($this);
	}

	/**
	 * Retrieve a Cabinet\Database\Collector\Schema object with the current connection.
	 *
	 * @return  object  Cabinet\Database\Collector\Schema object
	 */
	public function schema()
	{
		return Db::schema()->setConnection($this);
	}

	/**
	 * Transaction functions.
	 */
	abstract public function startTransaction($name = null);
	abstract public function commitTransaction($name = null);
	abstract public function rollbackTransaction($name = null);
}