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

class Collector extends Query\Base
{
	/**
	 * @var  array|string  $table  tables to use
	 */
	public $table = array();

	/**
	 * @var  array  $columns  columns to use
	 */
	public $columns = array();


	/**
	 * @var  array  $values  values for insert
	 */
	public $values = array();


	/**
	 * @var  array  $groupBy  GROUP BY clause
	 */
	public $groupBy = array();


	/**
	 * @var  array  $joins  query joins
	 */
	public $joins = array();


	/**
	 * @var  integer  $limit  query limit
	 */
	public $limit;


	/**
	 * @var  array  $offset  query offset
	 */
	public $offset;


	/**
	 * @var  boolean  $columns  wether to use distinct
	 */
	public $distinct = false;

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