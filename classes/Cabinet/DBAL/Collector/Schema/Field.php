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

namespace Cabinet\DBAL\Collector\Schema;

use Cabinet\DBAL\Collector;

class Field extends Collector
{
	/**
	 * @var  string  $name  field name
	 */
	public $name = null;

	/**
	 * @var  string  $newName  field name
	 */
	public $newName = null;

	/**
	 * @var  integer  $name  field constraint
	 */
	public $constraint = null;

	/**
	 * @var  string  $defaultValue  default value
	 */
	public $defaultValue = null;

	/**
	 * @var  string  $comments  field comments
	 */
	public $comments = null;

	/**
	 * @var  string  $collation  field collation
	 */
	public $charset = null;

	/**
	 * @var  boolean  $nullable  is nullable
	 */
	public $null = false;

	/**
	 * @var  boolean  $incremental  wether the field auto increments
	 */
	public $incremental = false;

	/**
	 * @var  boolean  $unique  wether the field is unique
	 */
	public $unique = false;

	/**
	 * @var  boolean  $fulltext  wether the field is fulltext searchable
	 */
	public $fulltext = false;

	/**
	 * @var  boolean  $first  wether the field should be prepended
	 */
	public $first = false;

	/**
	 * @var  string  $after  after which field the field should be appended
	 */
	public $after;

	/**
	 * @var  boolean  $unsigned  wether to use UNSIGNED
	 */
	public $unsigned = false;

	/**
	 * @var  object  $table  table reference
	 */
	protected $_table;

	/**
	 * Constructor, sets the field name.
	 *
	 * @param  string  $name   field name
	 */
	public function __construct($name, Table $table)
	{
		$this->name = $name;
		$this->_table = $table;
	}

	/**
	 * New field name.
	 *
	 * @param   string  $name  field name
	 * @return  object  $this
	 */
	public function newName($name)
	{
		$this->newName = $name;

		return $this;
	}

	/**
	 * Sets the field type.
	 *
	 * @param   string  $type  field type
	 * @return  object  $this
	 */
	public function type($type, $constraint = null)
	{
		$this->type = $type;
		$constraint !== null and $this->constraint = $constraint;

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
	 * Sets the field type.
	 *
	 * @param   string  $default  field default value
	 * @return  object  $this
	 */
	public function defaultValue($default)
	{
		$this->defaultValue = $default;

		return $this;
	}

	/**
	 * Sets the field charset.
	 *
	 * @param   string  $constraint  field charset
	 * @return  object  $this
	 */
	public function charset($charset)
	{
		$this->charset = $charset;

		return $this;
	}

	/**
	 * Set wether the field auto increments
	 *
	 * @param   boolean  $incremental
	 * @return  object   $this
	 */
	public function incremental($increment = true)
	{
		$this->incremental = $increment;

		return $this;
	}

	/**
	 * Determine wether the field is nullable.
	 *
	 * @param   string  $null  wether the field is nullable
	 * @return  object  $this
	 */
	public function null($null = true)
	{
		$this->null = $null;

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

	/**
	 * Sets a unique index on the field.
	 *
	 * @param   boolean  $unique  wether the field is unique
	 * @return  object  $this
	 */
	public function unique($unique = true)
	{
		$this->unique = $unique;

		return $this;
	}

	/**
	 * Sets the fields as fulltext searchable
	 *
	 * @param   boolean  $ulltext  wether the field is fulltext searchable
	 * @return  object   $this
	 */
	public function fulltext($fulltext = true)
	{
		$this->fulltext = $fulltext;

		return $this;
	}
}