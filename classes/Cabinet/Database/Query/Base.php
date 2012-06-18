<?php

namespace Cabinet\Database\Query;

use Cabibet\Database\Connection;

abstract class Base
{
	/**
	 * @var  mixed  $query  raw query
	 */
	protected $query = null;

	/**
	 * @var  string  $asOjbect  true for stCLass or string classname
	 */
	protected $asObject = null;

	/**
	 * @var  array  $bindings  query bindings
	 */
	protected $bindings = array();

	/**
	 * @var  string  $type  query type
	 */
	protected $type = null;

	/**
	 * @var  object  $connection  connection object
	 */
	protected $connection = null;

	/**
	 * Bind a value to the query.
	 *
	 * @param   mixed  $key    binding key or associative array of bindings
	 * @param   mixed  $value  binding value
	 */
	public function bind($key, $value = null)
	{
		is_array($key) or $key = array($key => $value);

		foreach ($key as $k => $v)
		{
			$this->bindings[$k] = $v;
		}

		return $this;
	}

	/**
	 * Get the query value.
	 *
	 * @param   object  $connection  database connection object
	 * @return  object  $this
	 */
	public function setConnection($connection)
	{
		if( ! $connection instanceof \Cabinet\Database\Connection)
		{
			throw new \Cabinet\Database\Exception('Supplied invalid connection object');
		}

		$this->connection = $connection;

		return $this;
	}

	/**
	 *
	 *
	 * @return  object  connection object
	 * @throws  Cabinet\Database\Exception  when no connection object is set.
	 */
	public function getConnection()
	{
		if ( ! $this->connection)
		{
			throw new \Cabinet\Database\Exception('Can\'t retrieve Connection object when none is set.');
		}

		return $this->connection;
	}

	/**
	 * Get the query value.
	 *
	 * @return  mixed  query contents
	 */
	public function getContents()
	{
		return $this->query;
	}

	/**
	 * Returns the query's bindings.
	 *
	 * @return  array  query bindings
	 */
	public function getBindings()
	{
		return $this->bindings;
	}

	/**
	 * Returns the query type.
	 *
	 * @return  array  query bindings
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * As object getter/setter.
	 *
	 * @param   $object  null for array, true for stdClass or string classname
	 */
	public function asObject($object = true)
	{
		if ( ! func_num_args())
		{
			return $this->asObject;
		}

		$this->asObject = $object ?: null;

		return $this;
	}

	/**
	 * Executes the query on a given connection.
	 *
	 * @param   object  $connection  Cabinet\Database\Connection
	 * @return  mixed   Query result.
	 */
	public function execute($connection = null)
	{
		$connection and $this->setConnection($connection);

		return $this->getConnection()->execute($this);
	}

	/**
	 * Compiles the query on a given connection.
	 *
	 * @param   object  $connection  Cabinet\Database\Connection
	 * @return  mixed   compiled query
	 */
	public function compile($connection = null)
	{
		$connection and $this->setConnection($connection);

		return $this->getConnection()->compile($this, $this->getType());
	}
}