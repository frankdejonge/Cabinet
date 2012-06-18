<?php

namespace Cabinet\Database\Collector;

use Cabinet\Database\Db;

class Delete extends Where
{
	protected $type = Db::DELETE;
	
	public function __construct($table = null)
	{
		$table and $this->query['table'] = $table;
	}

	/**
	 * Sets the table to update
	 *
	 * @param   string  $table  table to update
	 * @return  object  $this
	 */
	public function from($table)
	{
		$this->query['table'] = $table;

		return $this;
	}
}