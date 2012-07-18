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

namespace Cabinet\DBAL;

class Db
{
	/**
	 * Query type contants.
	 */
	const PLAIN                 = 'Plain';
	const INSERT                = 'Insert';
	const SELECT                = 'Select';
	const UPDATE                = 'Update';
	const DELETE                = 'Delete';
	const DATABASE_DROP         = 'DatabaseDrop';
	const DATABASE_ALTER        = 'DatabaseAlter';
	const DATABASE_CREATE       = 'DatabaseCreate';
	const TABLE_DROP            = 'TableDrop';
	const TABLE_ALTER           = 'TableAlter';
	const TABLE_CREATE          = 'TableCreate';
	const TABLE_ADD_FIELDS      = 'TableAddFields';
	const TABLE_DROP_FIELDS     = 'TableDropFields';
	const TABLE_ALTER_FIELDS    = 'TableAlterFields';

	/**
	 * Retrieve a database connection.
	 *
	 * @param   array   $config  database connection config
	 * @return  object  a new Cabinet\DBAL\Cconnection\[type] object
	 */
	public static function connection($config = array())
	{
		return Connection::instance($config);
	}

	/**
	 * Database expression shortcut.
	 *
	 * @param   mixed  $expression
	 * @return  object  a new Cabinet\DBAL\Expression object.
	 */
	public static function expr($expression)
	{
		return new Expression($expression);
	}

	/**
	 * Database value shortcut.
	 *
	 * @param   mixed   $value  value
	 * @return  object  a new Cabinet\DBAL\Value object.
	 */
	public static function value($value)
	{
		return new Value($value);
	}

	/**
	 * Database identifier shortcut.
	 *
	 * @param   mixed   $identifier  identifier
	 * @return  object  a new Cabinet\DBAL\Value object.
	 */
	public static function identifier($identifier)
	{
		return new Identifier($identifier);
	}

	/**
	 * Database function shortcut.
	 *
	 * @param   string  $fn      function
	 * @param   array   $params  function params
	 * @return  object  a new Cabinet\DBAL\Fn object.
	 */
	public static function fn($fn, $params = array())
	{
		return new Fn($fn, $params);
	}

	/**
	 * Returns a query object.
	 *
	 * @param   mixed   $query     raw database query
	 * @param   string  $type      query type
	 * @param   array   $bindings  query bindings
	 * @return  object  Cabinet\DBAL\Query
	 */
	public static function query($query, $type, $bindings = array())
	{
		return new Query($query, $type, $bindings);
	}

	/**
	 * Created a select collector object.
	 *
	 * @param   mixed  string field name or arrays for alias
	 * ....
	 * @return  object  select query collector object
	 */
	public static function select($column = null)
	{
		$query =  new Collector\Select();
		return $query->selectArray(func_get_args());
	}

	/**
	 * Creates a select collector object.
	 *
	 * @param   array   $columns  array of fields to select
	 * @return  object  select query collector object
	 */
	public static function selectArray($columns = array())
	{
		return static::select()->selectArray($columns);
	}

	/**
	 * Creates an update collector object.
	 *
	 * @param   string   $table  table to update
	 * @param   array    $set    associative array of new values
	 * @return  object   update query collector object
	 */
	public static function update($table, $set = array())
	{
		return new Collector\Update($table, $set);
	}

	/**
	 * Creates a delete collector object.
	 *
	 * @param   string   $table  table to delete from
	 * @return  object   delete query collector object
	 */
	public static function delete($table)
	{
		return new Collector\Delete($table);
	}

	/**
	 * Creates an insert collector object.
	 *
	 * @param   string   $table  table to insert into
	 * @return  object   insert query collector object
	 */
	public static function insert($table)
	{
		return new Collector\Insert($table);
	}

	/**
	 * Creates a schema collector object.
	 *
	 * @return  object   schema query collector object
	 */
	public static function schema()
	{
		return new Collector\Schema();
	}
}
