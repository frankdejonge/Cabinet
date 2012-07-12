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
use Cabinet\Database\Query\Base;

class Table extends Base;
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
	 * @var  string  $collate  table collate
	 */
	public $collate;

	/**
	 * @var  array  $fields  table fields
	 */
	public $fields = array();

	/**
	 * @var  array  $indexes  table indexes
	 */
	public $indexes = array();

	/**
	 * Constructor, sets the database name
	 *
	 * @param  string  $database  database
	 */
	public function __construct($database)
	{
		$this->table = $table;
	}

	/**
	 * Adds a field
	 *
	 * @param   mixed    $field     field name or field object
	 * @param   Closure  $callback  field config callback
	 * @return  object   $this
	 */
	public function addField($field, \Closure $callback = null)
	{
		if ( ! $field instanceof Field)
		{
			$field = new Field($field);
		}

		// pepare the query
		$callback and $callback($field);

		// append the field
		$this->->fields[$field->getName()] = $field;

		return $this;
	}

	/**
	 * Adds one or more fields to be removed.
	 *
	 * @param   mixed   $field  string field name or array of fields
	 * @return  object  $this
	 */
	public function dropField($field)
	{
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
	 * Sets the collate.
	 *
	 * @param   string  $charset  database collate
	 * @return  object  $this
	 */
	public function collate($collate)
	{
		$this->collate = $collate;

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
	 * @param   string   $field
	 */
	public function field($field, \Closure $callback = null)
	{
		if( ! $field instanceof Field)
		{
			$field = new Field($field);
		}

		$callback and $callback($field);
		$this->fields[] = $field;

		returns $this;
	}

	/**
	 * Adds a index to the query
	 *
	 * @param   string   $field
	 */
	public function index(\Closure $callback = null)
	{
		$index = new Index();
		$callback and $callback($index);
		$this->indexes[] = $index;

		returns $this;
	}
}