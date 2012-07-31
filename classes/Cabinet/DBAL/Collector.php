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

class Collector extends Base
{
	/**
	 * @var  array|string  $table  tables to use
	 */
	public $table = array();

	/**
	 * Get the query contents
	 *
	 * @return  array  query contents
	 */
	public function getContents()
	{
		$return = array();
		$vars = get_object_vars($this);

		foreach ($vars as $k => $v)
		{
			if ( ! preg_match('/^_/', $k))
			{
				$return[$k] = $v;
			}
		}

		return $return;
	}
}
