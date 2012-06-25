<?php

namespace Cabinet\Database\Collector\Schema;

class Field
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
	 * @var  boolean  $null  is nullable
	 */
	protected $null = false;

	/**
	 * Constructor, sets the field name
	 *
	 * @param  string  $name  field name
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Sets the field type
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
	 * Sets the field type
	 *
	 * @param   string  $type  field type
	 * @return  object  $this
	 */
	public function constraint($constraint)
	{
		$this->constraint = $constraint;

		return $this;
	}

	/**
	 * Returns the field setup as an array
	 *
	 * @return  array  field setup array
	 */
	public function asArray()
	{
		return array(
			'name' => $this->name,
			'type' => $this->type,
			'constraint' => $this->constraint,
			'default' => $this->default,
			'collation' => $this->collation,
			'engine' => $this->engine,
			'comments' => $this->comments,
		);
	}
}