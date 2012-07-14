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
	 * Transaction functions.
	 */
	abstract public function startTransaction($name = null);
	abstract public function commitTransaction($name = null);
	abstract public function rollbackTransaction($name = null);

	/**
	 * Db class call forwarding. Sets the current connection if setter is available.
	 *
	 * @param   string  $func  function name
	 * @param   array   $args  function arguments
	 * @return  forwarded result (with set connection)
	 * @throws  \BadMethodCallException when method doesn't exist.
	 */
	public function __call($func, $args)
	{
		$call = '\\Cabinet\\Database\\Db::'.$func;

		if (is_callable($call))
		{
			$return = call_user_func_array($call, $args);
			
			if (is_object($return) and method_exists($return, 'setConnection'))
			{
				$return->setConnection($this);
			}

			return $return;
		}
		
		throw new \BadMethodCallException($func.' is not a method of '.get_called_class());
	}

	/**
	 * List databases.
	 *
	 * @return  array  databases.
	 */
	public function listDatabases()
	{
		throw new Exception('List database is not supported by this driver.');
	}

	/**
	 * List database tables.
	 *
	 * @return  array  tables fields.
	 */
	public function listTables()
	{
		throw new Exception('List tables is not supported by this driver.');
	}

	/**
	 * List table fields.
	 *
	 * @return  array  databases.
	 */
	public function listFields($table)
	{
		throw new Exception('List fields is not supported by this driver.');
	}
}