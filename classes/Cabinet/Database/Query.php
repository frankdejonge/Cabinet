<?php

namespace Cabinet\Database;

class Query extends Query\Base
{
	/**
	 * @var  mixed  raw query (string for sql, array for NoSQL)
	 */
	protected $query;

	/**
	 * Constructor, sets the query, type and bindings
	 *
	 * @param  mixed   raw query
	 * @param  string  query type
	 * @param  array   query bindings
	 */
	public function __construct($query, $type, $bindings = array())
	{
		$this->query = $query;
		$this->type = $type;
		$this->bindings = $bindings;
	}
	
	/**
	 * Get the query value.
	 *
	 * @return  mixed  query contents
	 */
	public function getContents()
	{
		return $this->query;
	}
}