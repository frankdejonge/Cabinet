<?php

namespace Cabinet\Database\Connection;

use Cabinet\Database\Exception;
use Cabinet\Database\Expression;
use Cabinet\Database\Connection;
use Cabinet\Database\Db;
use Cabinet\Database\Query;
use Cabinet\Database\Result;

class Pdo extends Connection
{
	/**
	 * @var  object  $connection  PDO connection
	 */
	protected $connection;

	/**
	 * @var  string  $driver  
	 */
	protected $driver;

	/**
	 * @var  object  $connection  Cabinet Compiler object
	 */
	protected $compiler;

	/**
	 * @var  string  $insertIdField  field used for lastInsertId
	 */
	public $insertIdField;

	/**
	 * Connection constructor.
	 * Connects to a database with the supplied config.
	 */
	public function __construct($config = array())
	{
		// set the config defaults
		$config = $config + array(
			'driver' => 'mysql',
			'host' => 'localhost',
			'database' => null,
			'username' => null,
			'password' => null,
			'attrs' => array(),
			'insertIdField' => null,
		);
		
		// exception mode
		$config['attrs'][\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
		
		// store the driver
		$this->driver = strtolower($config['driver']);

		// get connected
		$this->connect($config);
	}

	/**
	 * Returns the connection driver type.
	 *
	 * @return  string  connection driver type
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * Connects to the database.
	 *
	 * @param   array  $config  connection config
	 * @throws  Cabinet\Database\Exception when unable to connect
	 */
	protected function connect($config)
	{
		try
		{
			$this->connection = new \PDO($this->formatDsn($config), $config['username'], $config['password'], $config['attrs']);
		}
		catch (\PDOException $e)
		{
			throw new Exception($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Quotes a value.
	 *
	 * @param   string  $value  value to quote
	 * @return  string  quoted value
	 */
	public function quote($value)
	{
		if($value instanceof Expression)
		{
			return $value->value();
		}

		return $this->connection->quote($value);
	}

	/**
	 * Close the connection.
	 */
	public function disconnect()
	{
		$this->connection = null;
	}

	/**
	 * Formats the dsn if none supplied and returns it.
	 *
	 * @param   array   $config  connection config
	 * @return  string  formatted connection dsn
	 */
	protected function formatDsn($config)
	{
		// check for dsn, if set, return that
		if (isset($config['dsn']) and ! empty($config['dsn']))
		{
			return $config['dsn'];
		}
		
		// build the dsn
		return $config['driver'].':host='.
			$config['host'].';dbname='.
			$config['database'].
			(isset($config['port']) ? ';port='.$config['port'] : '');
	}

	/**
	 * Get the query compiler.
	 *
	 * @return  object  Cabinet compiler object
	 */
	protected function getCompiler()
	{
		if ( ! $this->compiler)
		{
			$class = 'Cabinet\\Database\\Compiler\\Sql\\'.ucfirst($this->driver);

			if ( ! class_exists($class))
			{
				throw new Exception('Cannot locate compiler for dialect: '.$class);
			}

			$this->compiler = new $class($this);
		}

		return $this->compiler;
	}

	/**
	 * Executes a query on a connection
	 *
	 * @param   object  $query     query object
	 * @param   string  $type      query type
	 * @param   array   $bindings  query bindings
	 */
	public function execute($query, $type = null, $bindings = array())
	{
		if( ! $query instanceof Query\Base)
		{
			$query = new Query($query, $type);	
		}

		$type = $type ?: $query->getType();
		$sql = $this->compile($query, $type, $bindings);
		
		$profilerData = array(
			'query' => $sql,
			'start' => microtime(true),
			'type' => $type,
			'driver' => get_class($this).':'.$this->driver,
		);
		
		//$this->queries[] = $sql;

		try
		{
			$result = $this->connection->query($sql);
		}
		catch (\PDOException $e)
		{
			throw new Exception($e->getMessage().' from QUERY: '.$sql, $e->getCode());
		}
		
		if($type === Db::SELECT)
		{
			$asObject = $query->getAsObject();
			
			if ( ! $asObject)
			{
				$result = $result->fetchAll(\PDO::FETCH_ASSOC);
			}
			elseif (is_string($asObject))
			{
				$result = $result->fetchAll(\PDO::FETCH_CLASS, $asObject);
			}
			else
			{
				$result = $result->fetchAll(\PDO::FETCH_CLASS, 'stdClass');
			}
		}
		elseif($type = Db::INSERT)
		{
			$result = array(
				$this->connection->lastInsertId($query->insertIdField() ?: $this->insertIdField),
				$result->rowCount(),
			);
		}
		else
		{
			$result = $result->errorCode() === '00000' ? $result->rowCount() : -1;
		}
		
		$profilerData['end'] = microtime(true);
		$profilerData['duration'] = $profilerData['end'] - $profilerData['start'];
		$this->queries[] = $profilerData;
		
		return $result;
	}

	/**
	 * Compile the query.
	 *
	 * @param   object  $query     query object
	 * @param   string  $type      query type
	 * @param   array   $bindings  query bindings
	 */
	public function compile($query, $type = null, $bindings = array())
	{
		if( ! $query instanceof Query\Base)
		{
			$query = new Query($query, $type);	
		}

		// Reretrieve the query type
		$type = $query->getType();

		return $this->getCompiler()->compile($query, $type, $bindings);
	}

	/**
	 * Object destruct closes the database connection.
	 */
	public function __destruct()
	{
		$this->disconnect();
	}
	
	public function startTransaction($name = null)
	{
		
	}

	public function commitTransaction($name = null)
	{
		
	}

	public function rollbackTransaction($name = null)
	{
		
	}
}