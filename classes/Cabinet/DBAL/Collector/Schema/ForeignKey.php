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
	 * @var  string  $key  key name
	 */
	public $key;

	/**
	 * @var  string  $references  table and field references
	 */
	public $reference = array(
		'table' => null,
		'columns' => array(),
	);

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
	 * @param  string  $key  key name
	 */
	public function __construct($key)
	{
		$this->key = $key;
	}

	/**
	 * Sets the referencing field.
	 *
	 * @param   string  $identifier  foreign key reference
	 * @return  object  $this;
	 */
	public function references($table, $columns)
	{
		$this->reference['table'] = $table;
		$this->reference['columns'] = is_array($columns) ? $columns : array($columns);

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