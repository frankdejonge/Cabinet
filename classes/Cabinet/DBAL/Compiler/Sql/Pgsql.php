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

namespace Cabinet\DBAL\Compiler\Sql;

use Cabinet\DBAL\Compiler\Sql;

class Pgsql extends Sql
{
	/**
	 * @var  string  $tableQuote  table quote
	 */
	protected static $tableQuote = '"';

	/**
	 * Compiles a PGSQL concatination.
	 *
	 * @param   object  $value  Fn object
	 * @return  string  compiles concat
	 */
	protected function compileFnConcat($value)
	{
		$values = $value->getParams();
		$quoteFn = ($value->quoteAs() === 'identifier') ? 'quoteIdentifier' : 'quote';

		return join(' || ', array_map(array($this, $quoteFn), $value->getParams()));
	}
}
