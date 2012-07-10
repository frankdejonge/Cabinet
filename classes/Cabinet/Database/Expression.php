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