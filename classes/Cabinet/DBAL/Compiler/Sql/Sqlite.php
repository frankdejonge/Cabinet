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

class Sqlite extends Sql
{
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
				$data['type'] = 'integer';
				$data['primary'] = true;
				$data['incremental'] = false;
			}

			return $data;
		}, $fields);
	}
}
