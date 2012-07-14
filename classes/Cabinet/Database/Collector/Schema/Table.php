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

namespace Cabinet\Database\Collector\Schema;

use Cabinet\Database\Db;
use Cabinet\Database\Collector;

class Table extends Collector
{
	/**
	 * @var  string  $database  database name
	 */
	public $database;

	/**
	 * @var  string  $table  table name
	 */
	public $table;

	/**
	 * @var  string  $newName  new table name
	 */
	public $newName;

	/**
	 * @var  boolean  $ifExists  wether to use IF EXISTS
	 */
	public $ifExists = false;

	/**
	 * @var  boolean  $ifNotExists  wether to use IF NOT EXISTS
	 */
	public $ifNotExists = false;

	/**
	 * @var  string  $charset  table charset
	 */
	public $charset;

	/**
	 * @var  string  $engine  field storage engine
	 */
	public $engine = null;

	/**
	 * @var  array  $fields  table fields
	 */
	public $fields = array();

	/**
	 * @var  array  $indexes  table indexes
	 */
	public $indexes = array();

	/**
	 * Constructor, sets the table name
	 *
	 * @param  string  $table  table name
	 */
	public function __construct($table)
	{
		$this->table = $table;
	}

	/**
	 * Adds a field
	 *
	 * @param   mixed    $field     field name
	 * @param   Closure  $callback  field config callback
	 * @return  object   $this
	 */
	public function addFields($fields, \Closure $callback = null)
	{
		$this->type = Db::TABLE_ADD_FIELDS;

		return call_user_func_array(array($this, 'fields'),	func_get_args());
	}

	/**
	 * Adds a field
	 *
	 * @param   mixed    $field     field name
	 * @param   Closure  $callback  field config callback
	 * @return  object   $this
	 */
	public function alterFields($fields, \Closure $callback = null)
	{
		$this->type = Db::TABLE_ALTER_FIELDS;

		return call_user_func_array(array($this, 'fields'),	func_get_args());
	}

	/**
	 * Adds one or more fields to be removed.
	 *
	 * @param   mixed   $field  string field name or array of fields
	 * @return  object  $this
	 */
	public function dropFields($field)
	{
		$this->type = Db::TABLE_DROP_FIELDS;

		is_array($field) or $field = array($field);

		$this->fields = array_merge($this->fields, $field);
	}

	/**
	 * Sets the IF EXISTS CLAUSE
	 *
	 * @param   boolean  $useExists  wether to use IF EXISTS
	 */
	public function ifExists($useExists = true)
	{
		$this->ifExists = $useExists;

		return $this;
	}

	/**
	 * Sets the IF NOT EXISTS CLAUSE
	 *
	 * @param   boolean  $useExists  wether to use IF EXISTS
	 */
	public function ifNotExists($useNotExists = true)
	{
		$this->ifNotExists = $useNotExists;

		return $this;
	}

	/**
	 * Sets the charset.
	 *
	 * @param   string  $charset  database charset
	 * @return  object  $this
	 */
	public function charset($charset)
	{
		$this->charset = $charset;

		return $this;
	}

	/**
	 * Sets the storage engine.
	 *
	 * @param   string  $engine  database engine
	 * @return  object  $this
	 */
	public function engine($engine)
	{
		$this->engine = $engine;

		return $this;
	}

	/**
	 * Sets the query type for drop database
	 *
	 * @return  object  $this
	 */
	public function drop()
	{
		$this->type = Db::TABLE_DROP;

		return $this;
	}

	/**
	 * Sets the query type for drop database
	 *
	 * @param   string  $newName  new table name
	 * @return  object  $this
	 */
	public function renameTo($newName)
	{
		$this->type = Db::TABLE_RENAME;
		$this->newName = $newName;

		return $this;
	}

	/**
	 * Sets the query type for drop database
	 *
	 * @return  object  $this
	 */
	public function create()
	{
		$this->type = Db::TABLE_CREATE;

		return $this;
	}

	/**
	 * Sets the query type for drop database
	 *
	 * @return  object  $this
	 */
	public function alter()
	{
		$this->type = Db::TABLE_ALTER;

		return $this;
	}

	/**
	 * Adds a field to the query
	 *
	 * @param   string|array   $fields    string field name or fields array
	 * @param   closure        $callback  configuration callback
	 */
	public function fields($fields, \Closure $callback = null)
	{
		is_array($fields) or $fields = array($fields => $callback);

		foreach ($fields as $f => $c)
		{
			$field = new Field($f);
			$c($field);
			$this->fields[$field->name] = $field;
		}

		return $this;
	}

	/**
	 * Adds a indexes to the query
	 *
	 * @param   string|array   $indexes   string field name or fields array
	 * @param   closure        $callback  configuration callback
	 */
	public function indexes($indexes)
	{
		is_array($indexes) or $indexes = func_get_args();

		foreach ($indexes as $name => $i)
		{
			$index = new Index();
			is_numeric($name) or $index->name($name);
			$i($index);
			$this->indexes[] = $index;
		}

		return $this;
	}

	/**
	 * Adds a field to the query
	 *
	 * @param   string|array   $fields    string field name or fields array
	 * @param   closure        $callback  configuration callback
	 */
	public function foreignKeys($foreignKeys, \Closure $callback = null)
	{
		is_array($foreignKeys) or $foreignKeys = array($foreignKeys => $callback);

		foreach ($foreignKeys as $k => $c)
		{
			$foreignKey = new ForeignKey($k);
			$c($foreignKey);
			$this->foreignKeys[$foreignKeys->on] = $foreignKey;
		}

		return $this;
	}
}