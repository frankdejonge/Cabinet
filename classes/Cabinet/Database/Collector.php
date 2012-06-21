<?php

namespace Cabinet\Database;

class Collector extends Query\Base
{
	protected $query = array(
		'table' => array(),
		'columns' => array(),
		'values' => array(),
		'where' => array(),
		'having' => array(),
		'orderBy' => array(),
		'groupBy' => array(),
		'joins' => array(),
		'limit' => null,
		'offset' => null,
		'asObject' => false,
	);
}