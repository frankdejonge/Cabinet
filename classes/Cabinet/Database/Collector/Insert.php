<?php

namespace Cabinet\Database\Collector;

use Cabinet\Database\Collector;
use Cabinet\Database\Db;

class Insert extends Collector
{
	/**
	 * @var  string  $type  query type
	 */
	protected $type = Db::INSERT;

	/**
	 * @param
	 */
	protected $lastValues = false;
	
	public function __construct($table, $values = array())
	{
		$this->into($table);
		$this->values($values);
	}

	/**
	 * Sets the table to insert into.
	 *
	 * @param   string  $table  table to insert into
	 * @return  object  $this
	 */
	public function into($table)
	{
		$this->query['table'] = $table;
		return $this;
	}

	/**
	 * Adds values to insert
	 *
	 * @param   array   $values  array or collection of arrays to insert
	 * @param   bool    $merge   wether to merge the values with the last inserted set
	 * @return  object  $this
	 */
	public function values($values = array(), $merge = false)
	{
		if (empty($values))
		{
			return $this;
		}

		is_array(reset($values)) or $values = array($values);

		foreach($values as $v)
		{
			$keys = array_keys($v);
			$this->query['columns'] = array_merge($this->query['columns'], $keys);

			if($merge and count($this->query['values']))
			{
				$last = array_pop($this->query['values']);
				$this->query['values'][] = array_merge($last, $v);
			}
			else
			{
				$this->query['values'][] = $this->lastValues = $v;
			}
		}

		return $this;
	}
}