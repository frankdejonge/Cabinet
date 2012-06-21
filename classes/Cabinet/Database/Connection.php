<?php

namespace Cabinet\Database;

abstract class Connection
{
	public static function instance($config = array())
	{
		$config = $config + array(
			'type' => 'pdo',
		);
		
		$type = ucfirst(strtolower($config['type']));
		if( ! class_exists($class = __NAMESPACE__.'\\Connection\\'.$type))
		{
			throw new Exception('Cannot load database connection of type: '.$config['type']);
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
		$this->start_transaction();

		try
		{
			$result = $callback($this);
		}
		catch(Exception $e)
		{
			$this->rollback_transaction();
			throw $e;
		}
		
		$this->commit_transaction();
		return $result;
	}
	
	public function query($query, $type, $bindings = array())
	{
		return Db::query($query, $type, $bindings)->setConnection($this);	
	}
	
	public function select($columns = array())
	{
		return Db::select($columns)->setConnection($this);
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
	abstract public function start_transaction($name = null);
	abstract public function commit_transaction($name = null);
	abstract public function rollback_transaction($name = null);
	
	
}