<?php

namespace Cabinet\Database;

class Result
{
	/**
	 * @var  mixed  $result  query result
	 */
	protected $result = null;

	public function __construct($result)
	{
		$this->result = $result;
	}
}