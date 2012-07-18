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

class ForeignKey
{
	/**
	 * @var  string  $on  field name
	 */
	public $on;

	/**
	 * @var  string  $references  table and field references
	 */
	public $references;

	/**
	 * @var  string  $onUpdate  update action
	 */
	public $onUpdate;

	/**
	 * @var  string  $ondelete  delete action
	 */
	public $onDelete;

	/**
	 * @var  string  $constraint  constraint
	 */
	public $constaint;

	/**
	 * Constructor, sets the key field
	 *
	 * @param  string  $on  field name
	 */
	public function __construct($on)
	{
		$this->on = $on;
	}

	/**
	 * Sets the referencing field.
	 *
	 * @param   string  $identifier  foreign key reference
	 * @return  object  $this;
	 */
	public function references($identifier)
	{
		$this->references = func_get_args();

		return $this;
	}

	/**
	 * Sets the update action.
	 *
	 * @param   string  $action  update action
	 * @return  object  $this;
	 */
	public functin onUpdate($action)
	{
		$this->onUpdate = $action;

		return $this;
	}

	/**
	 * Sets the delete action.
	 *
	 * @param   string  $action  delete action
	 * @return  object  $this;
	 */
	public function onDelete($action)
	{
		$this->onDelete = $action;

		return $this;
	}

	/**
	 * Sets the key constraint.
	 *
	 * @param   string  $constraint  key constraint
	 * @return  object  $this;
	 */
	public function constraint($constraint)
	{
		$this->constraint = $constraint;

		return this;
	}
}
