<?php

namespace Cabinet\Database\Collector\Schema;

class Field extends \Cabinet\Database\Query\Base;
{
	/**
	 * @var  string  $name  field name
	 */
	protected $name = null;

	/**
	 * @var  integer  $name  field constraint
	 */
	protected $constraint = null;

	/**
	 * @var  string  $default  default value
	 */
	protected $default = null;

	/**
	 * @var  string  $comments  field comments
	 */
	protected $comments = null;

	/**
	 * @var  string  $engine  field storage engine
	 */
	protected $engine = null;

	/**
	 * @var  string  $collation  field collation
	 */
	protected $collation = null;

	/**
	 * @var  boolean  $nullable  is nullable
	 */
	protected $nullable = false;

	/**
	 * Constructor, sets the field name.
	 *
	 * @param  string  $name  field name
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Sets the field type.
	 *
	 * @param   string  $type  field type
	 * @return  object  $this
	 */
	public function type($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Sets the field type.
	 *
	 * @param   string  $constraint  field type
	 * @return  object  $this
	 */
	public function constraint($constraint)
	{
		$this->constraint = $constraint;

		return $this;
	}

	/**
	 * Determine wether the field is nullable.
	 *
	 * @param   string  $null  wether the field is nullable
	 * @return  object  $this
	 */
	public function nullable($null = true)
	{
		$this->nullable = $null;

		return $this;
	}

	/**
	 * Sets the field comments.
	 *
	 * @param   string  $comments  field comments
	 * @return  object  $this
	 */
	public function comments($comments)
	{
		$this->comments = $comments;

		return $this;
	}
}