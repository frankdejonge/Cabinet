<?php

namespace Cabinet\Database;

class Result implements ArrayAccess, C
{
	/**
	 * @var  mixed  $result  query result
	 */
	protected $result = null;

	/**
	 * @var  object  $query  query object
	 */
	protected $query = null;

	/**
	 * @var  integer  $type  query type
	 */
	protected $type = null;

	/**
	 * @var  integer  $insertId  the last inserted id
	 */
	protected $insertId = null;

	public function __construct($result, $query)
	{
		$this->result = $result;
		$this->query = $query;
		$this->connection = $query->getConnection();
		$this->type = $query->getType();
	}
	
	
	
	public function insertId($name = null)
	{
		return $this->insertId;
	}
}