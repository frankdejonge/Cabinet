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

use Cabinet\Database\Collector;
use Cabinet\Database\Db;

class Insert extends Collector
{
	/**
	 * @var  string  $type  query type
	 */
	protected $type = Db::INSERT;

	/**
	 * @var  string  $insertIdField  field used for lastInsertId
	 */
	public $insertIdField;

	/**
	 * @var  array  $columns  columns to use
	 */
	public $columns = array();

	/**
	 * @var  array  $values  values for insert
	 */
	public $values = array();
	
	public function __construct($table, $values = array())
	{
		$this->into($table);
		$this->values($values);
	}

	/**
	 * Sets/Gets the field used for lastInsertId
	 *
	 * @param   string  
	 * @return  mixed  current instance when setting, string fieldname when gettting.
	 */
	public function insertIdField($field = null)
	{
		if ($field)
		{
			$this->insertIdField = $field;

			return $this;
		}

		return $this->insertIdField;
	}

	/**
	 * Sets the table to insert into.
	 *
	 * @param   string  $table  table to insert into
	 * @return  object  $this
	 */
	public function into($table)
	{
		$this->table = $table;
		return $this;
	}

	/**
	 * Adds values to insert
	 *
	 * @param   array   $values  array or collection of arrays to insert
	 * @param   bool    $merge   wether to merge the values with the last inserted set
	 * @return  object  $this
	 */
	public function values($values = array(), $merge = false)
	{
		if (empty($values))
		{
			return $this;
		}

		is_array(reset($values)) or $values = array($values);

		foreach($values as $v)
		{
			$keys = array_keys($v);
			$this->columns = array_merge($this->columns, $keys);

			if($merge and count($this->values))
			{
				$last = array_pop($this->values);
				$this->values[] = array_merge($last, $v);
			}
			else
			{
				$this->values[] = $v;
			}
		}

		return $this;
	}
}