<?php

namespace Cabinet\Database;

class Expression
{
	/**
	 * Expression constructor.
	 *
	 * @param  mixed  expression value
	 */
	public function __construct($value)
	{
		$this->value = $value;	
	}

	/**
	 * Returns the expression value.
	 *
	 * @param   object  $connection  connection
	 * @return  mixed   the expression value
	 */
	public function value()
	{
		return $this->value;
	}
}