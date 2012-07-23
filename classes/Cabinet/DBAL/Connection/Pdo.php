<?php

namespace Cabinet\DBAL\Connection;

use Cabinet\DBAL\Db;
use Cabinet\DBAL\Query;
use Cabinet\DBAL\Result;
use Cabinet\DBAL\Exception;
use Cabinet\DBAL\Expression;
use Cabinet\DBAL\Connection;

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
	 * @var  string  $charset  connection charset
	 */
	public $charset;

	/**
	 * @var  integer  $savepoint  auto savepoint level
	 */
	protected $savepoint = 0;

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
			'charset' => null,
			'persistent' => true,
		);

		// exception mode
		$config['attrs'][\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;

		// set persistent connection
		$config['persistent'] and $config['attr'][\PDO::ATTR_PERSISTENT] = true;

		// store the driver
		$this->driver = strtolower($config['driver']);

		// get connected
		$this->connect($config);

		// set the charset
		$this->setCharset($config['charset']);
		
		parent::__construct($config);
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
	 * @throws  Cabinet\DBAL\Exception when unable to connect
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

	public function setCharset($charset)
	{
		if (empty($charset))
		{
			return $this;
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
		if ($value instanceof Expression)
		{
			if ($value instanceof Value)
			{
				return $this->connection->quote($value->value());
			}
			elseif ($value instanceof Identifier)
			{
				return $this->quoteIdentifier($value->value());
			}

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
			$class = 'Cabinet\\DBAL\\Compiler\\Sql\\'.ucfirst($this->driver);

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

		$this->profilerCallbacks['start'] instanceOf \Closure and $this->profilerCallbacks['start']($profilerData);

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
		elseif($type === Db::INSERT)
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

		// clear out any previous queries when profiling is turned off.
		if ($this->config['profiling'] === false)
		{
			$this->queries = array();
		}

		$this->queries[] = $profilerData;

		$this->profilerCallbacks['end'] instanceOf \Closure and $this->profilerCallbacks['end']($profilerData);

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

	public function setSavepoint($savepoint = null)
	{
		$savepoint or $savepoint = 'CABINET_SP_LEVEL_'. ++$this->savepoint;
		$this->connection->query('SAVEPOINT '.$savepoint);

		return $this;
	}

	public function rollbackSavepoint($savepoint = null)
	{
		if ( ! $savepoint)
		{
			$savepoint = 'CABINET_SP_LEVEL_'. $this->savepoint;
			$this->savepoint--;
		}

		$this->connection->query('ROLLBACK TO SAVEPOINT '.$savepoint);

		return $this;
	}

	public function releaseSavepoint($savepoint = null)
	{
		if ( ! $savepoint)
		{
			$savepoint = 'CABINET_SP_LEVEL_'. $this->savepoint;
			$this->savepoint--;
		}

	$this->connection->query('RELEASE SAVEPOINT '.$savepoint);

		return $this;
	}
}
