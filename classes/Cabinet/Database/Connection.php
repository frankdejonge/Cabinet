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

	/**
	 * Transaction functions.
	 */
	abstract public function start_transaction($name = null);
	abstract public function commit_transaction($name = null);
	abstract public function rollback_transaction($name = null);
}