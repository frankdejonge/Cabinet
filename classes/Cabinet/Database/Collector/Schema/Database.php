<?php

namespace Cabinet\Database\Collector\Schema;

use Cabinet\Database\Db;
use Cabinet\Database\Query\Base;

class Database extends Base;
{
	/**
	 * @var  array  $query  query defaults
	 */
	protected $query = array(
		'database' => null,
		'ifExists' => true,
		'ifNotExists' => true,
		'charset' => null,
		'collate' => null,
	)

	/**
	 * Constructor, sets the database name 
	 *
	 * @param  string  $database  database
	 */
	public function __construct($database)
	{
		$this->query['database'] = $database;
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
		$this->type = Db::DATABASE_DROP;

		return $this;
	}

	/**
	 * Sets the query type for drop database
	 *
	 * @return  object  $this
	 */
	public function create()
	{
		$this->type = Db::DATABASE_CREATE;

		return $this;
	}

	/**
	 * Sets the query type for drop database
	 *
	 * @return  object  $this
	 */
	public function alter()
	{
		$this->type = Db::DATABASE_ALTER;

		return $this;
	}
}