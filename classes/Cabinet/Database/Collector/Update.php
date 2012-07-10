<?php

namespace Cabinet\Database\Collector;

use Cabinet\Database\Db;

class Update extends Where
{
	/**
	 * @var  string  $type  query type
	 */
	protected $type = Db::UPDATE;

	/**
	 * Constructor, sets the table name
	 */
	public function __construct($table = null)
	{
		$table and $this->table = $table;
	}

	/**
	 * Sets the table to update
	 *
	 * @param   string  $table  table to update
	 * @return  object  $this
	 */
	public function table($table)
	{
		$this->table = $table;

		return $this;
	}

	/**
	 * Set the new values
	 *
	 * @param   mixed   $key    string field name or associative values array
	 * @param   mixed   $value  new value
	 * @return  object  $this
	 */
	public function set($key, $value = null)
	{
		is_array($key) or $key = array($key => $value);

		foreach ($key as $k => $v)
		{
			$this->values[$k] = $v;
		}

		return $this;
	}
}