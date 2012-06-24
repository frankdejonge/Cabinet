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
	 * Returnes the last executed query.
	 *
	 * @return  mixed  last executed query
	 */
	public function lastQuery()
	{
		return $this->lastQuery;
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
	 * @param   array  $columns  
	 */
	public function select($columns = '*')
	{
		return Db::selectArray(func_get_args())->setConnection($this);
	}
	
	public function update($table, $columns = array())
	{
		return Db::update($table, $columns)->setConnection($this);
	}
	
	public function delete($table, $columns = array())
	{
		return Db::delete($table, $columns)->setConnection($this);
	}
	
	public function insert($table, $columns = array())
	{
		return Db::insert($table, $columns)->setConnection($this);
	}

	/**
	 * Transaction functions.
	 */
	abstract public function startTransaction($name = null);
	abstract public function commitTransaction($name = null);
	abstract public function rollbackTransaction($name = null);
	
	
}