<?php

namespace Cabinet\Database;

class Query extends Query\Base
{
	public function __construct($query, $type, $bindings = array())
	{
		$this->query = $query;
		$this->type = $type;
		$this->bindings = $bindings;
	}
}