<?php

namespace Cabinet\Database\Collector\Schema;

use Cabinet\Database\Db;
use Cabinet\Database\Query\Base;

class Table extends Base;
{
	/**
	 * @var  array  $query  query defaults
	 */
	protected $query = array(
		'database' => null,
		'table' => null,
		'ifExists' => true,
		'ifNotExists' => true,
		'charset' => null,
		'collate' => null,
		'fields' => array(),
	)

	/**
	 * Constructor, sets the database name
	 *
	 * @param  string  $database  database
	 */
	public function __construct($database)
	{
		$this->query['table'] = $table;
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
		$this->query['fields'][$field->getName()] = $field;

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

		$this->query['fields'] = array_merge($this->query['fields'], $field);
	}

	/**
	 * Sets the IF EXISTS CLAUSE
	 *
	 * @param   boolean  $useExists  wether to use IF EXISTS
	 */
	public function ifExists($useExists = true)
	{
		$this->query['ifExists'] = $useExists;

		return $this;
	}

	/**
	 * Sets the IF NOT EXISTS CLAUSE
	 *
	 * @param   boolean  $useExists  wether to use IF EXISTS
	 */
	public function ifNotExists($useNotExists = true)
	{
		$this->query['ifNotExists'] = $useNotExists;

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
		$this->query['charset'] = $charset;

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
		$this->query['collate'] = $collate;

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

		$this->query['fields'][] = $field;

		returns $this;
	}
}