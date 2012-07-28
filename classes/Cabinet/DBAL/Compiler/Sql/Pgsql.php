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

	/**
	 * Prepares the fields for rendering.
	 *
	 * @param   array  $fields  array with field objects
	 * @return  array  array with prepped field objects
	 */
	protected function prepareFields($fields)
	{
		return array_map(function($field){
			$data = $field->getContents();
			
			if ($data['incremental'])
			{
				$data['type'] = 'serial';
				$data['incremental'] = false;
			}
			
			return $data;
		}, $fields);
	}

	/**
	 * Compiles the ENGINE statement
	 *
	 * @return  string  compiled ENGINE statement
	 */
	protected function compilePartEngine()
	{
		return '';
	}
}
