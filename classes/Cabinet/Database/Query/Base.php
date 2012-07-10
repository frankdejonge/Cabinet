<?php
/**
 * Cabinet is an easy flexible PHP 5.3+ Database Abstraction Layer
 *
 * @package    Cabinet
 * @version    1.0
 * @author     Frank de Jonge
 * @license    MIT License
 * @copyright  2011 - 2012 Frank de Jonge
 * @link       http://cabinetphp.com
 */

namespace Cabinet\Database\Query;

use Cabibet\Database\Connection;

abstract class Base
{
	/**
	 * @var  string  $asOjbect  true for stCLass or string classname
	 */
	protected $asObject = false;

	/**
	 * @var  array  $bindings  query bindings
	 */
	protected $bindings = array();

	/**
	 * @var  string  $type  query type
	 */
	protected $type;

	/**
	 * @var  object  $connection  connection object
	 */
	protected $_connection;

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

		$this->_connection = $connection;

		return $this;
	}

	/**
	 * Get the connection object.
	 *
	 * @return  object  connection object
	 * @throws  Cabinet\Database\Exception  when no connection object is set.
	 */
	public function getConnection()
	{
		return $this->_connection;
	}

	/**
	 * Get the query value.
	 *
	 * @return  mixed  query contents
	 */
	abstract public function getContents();

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
	 * As object setter.
	 *
	 * @param   $object  falsey (falls/null) for array, true for stdClass or string classname
	 */
	public function asObject($object = true)
	{
		$this->asObject = $object ?: null;

		return $this;
	}

	/**
	 * Returns wether to get as array or object
	 *
	 * @return  mixed  null for array, true for stdClass or string for classname
	 */
	public function getAsObject()
	{
		return $this->asObject;
	}

	/**
	 * Executes the query on a given connection.
	 *
	 * @param   object  $connection  Cabinet\Database\Connection
	 * @return  mixed   Query result.
	 */
	public function execute($connection = null)
	{
		$connection or $connection = $this->getConnection();

		if ( ! $connection)
		{
			throw new Exception('Cannot execute a query without a valid connection');
		}

		return $connection->execute($this);
	}

	/**
	 * Compiles the query on a given connection.
	 *
	 * @param   object  $connection  Cabinet\Database\Connection
	 * @return  mixed   compiled query
	 */
	public function compile($connection = null)
	{
		$connection or $connection = $this->getConnection();

		if ( ! $connection)
		{
			throw new Exception('Cannot compile a query without a valid connection');
		}

		return $connection->compile($this, $this->getType());
	}
}