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

namespace Cabinet\Database\Collector;

class Join
{
	/**
	 * @var  string  $table  table to join
	 */
	protected $table = null;

	/**
	 * @var  string  $type  join type
	 */
	protected $type = null;
	
	/**
	 * @var  array  $on  array of on statements
	 */
	protected $on = array();

	/**
	 * Join Contructor.
	 *
	 * @param  string  $table  table name
	 * @param  string  $type   type of join
	 */
	public function __construct($table, $type = null)
	{
		$this->table = $table;
		$this->type = $type;
	}
	
	/**
	 * Adds an 'on' clause for the join.
	 *
	 * @param   string|array  $column  string column name or array for alias
	 * @param   string        $op      logic operator
	 * @param   string|arrau  $value   value or array for alias
	 */
	public function on($column, $op = null, $value = null)
	{
		if (func_num_args() === 2)
		{
			$value = $op;
			$op = '=';
		}
		
		$this->on[] = array($column, $op, $value);
	}
	
	/**
	 * Returns the join as a command array.
	 *
	 * @return  array  join command array
	 */
	public function asArray()
	{
		return array(
			'table' => $this->table,
			'type' => $this->type,
			'on' => $this->on,
		);
	}
}