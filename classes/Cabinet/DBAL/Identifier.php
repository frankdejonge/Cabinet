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

namespace Cabinet\DBAL;

class Identifier extends Expression
{
	/**
	 * Handles identifier quoting.
	 *
	 * @param   Compiler  $compiler  compiler instance
	 * @return  string    quoted identifier
	 */
	public function handle(Compiler $compiler)
	{
		return $compiler->quoteIdentifier($this->value);
	}
}
