<?php
/**
 * Cabinet is an easy flexible PHP 5.3+ Database Abstraction Layer
 *
 * @package    Cabinet
 * @version    1.0
 * @author     Frank de Jonge
 * @license    MIT License
 * @copyright  2011 - 2012 Frank de Jonge
 * @link       http://cabinetphp.com
 */

namespace Cabinet\Database\Collector\Schema;

use Cabinet\Database\Collector;

class Field extends Collector;
{
	/**
	 * @var  string  $name  field name
	 */
	public $name = null;

	/**
	 * @var  integer  $name  field constraint
	 */
	public $constraint = null;

	/**
	 * @var  string  $default  default value
	 */
	public $default = null;

	/**
	 * @var  string  $comments  field comments
	 */
	public $comments = null;

	/**
	 * @var  string  $engine  field storage engine
	 */
	public $engine = null;

	/**
	 * @var  string  $collation  field collation
	 */
	public $collation = null;

	/**
	 * @var  boolean  $nullable  is nullable
	 */
	public $nullable = false;

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