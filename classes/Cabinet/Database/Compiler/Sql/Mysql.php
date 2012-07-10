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

namespace Cabinet\Database\Compiler\Sql;

use Cabinet\Database\Compiler\Sql;

class Mysql extends Sql
{
	/**
	 * Compiles MySQL functions
	 *
	 * @param   object  $value  function object
	 * @return  string  compiles MySQL function
	 */
	protected function compilePartFn($value)
	{
		$fn = strtoupper($value->getFn());
		$quoteFn = ($value->quoteAs() === 'identifier') ? 'quoteIdentifier' : 'quote';

		return $fn.'('.join(', ', array_map(array($this, $quoteFn), $value->getParams())).')';
	}
}