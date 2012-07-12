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

use Cabinet\Database\Db;

class Select extends Where
{
	/**
	 * @var  int  $type  query type
	 */
	protected $type = Db::SELECT;

	/**
	 * @var  array  $having  having conditions
	 */
	public $having = array();

	/**
	 * @var  array  $groupBy  GROUP BY clause
	 */
	public $groupBy = array();
	
	/**
	 * @var  object  $lastJoin  last join object
	 */
	protected $_lastJoin = null;
	
	/**
	 * Constructor
	 *
	 * @param  array  $columns  an array of columns to select
	 */
	public function __construct($column = null)
	{
		$columns = func_get_args();

		if (count($columns))
		{
			$this->columns = $columns;
		}		
	}
	
	/**
	 * Set the table to select from
	 *
	 * @param   string  $table  table to select from
	 * @param   ...
	 * @return  object  current instance
	 */
	public function from($table)
	{
		$tables = func_get_args();

		$this->table = array_merge($this->table, $tables);

		return $this;
	}
	
	/**
	 * Sets/adds columns to select
	 *
	 * @param   mixed   column name or array($column, $alias) or object
	 * @param   ...
	 * @return  object  current instance
	 */
	public function select($column = null)
	{
		$this->columns = array_merge($this->columns, func_get_args());
		
		return $this;
	}
	
	/**
	 * Choose the columns to select from, using an array.
	 *
	 * @param   array  $columns  list of column names or aliases
	 * @return  object current instance
	 */
	public function selectArray(array $columns = array())
	{
		! empty($columns) and $this->columns = array_merge($this->columns, $columns);
		
		return $this;
	}
	
	/**
	 * Enables or disables selecting only unique (distinct) values
	 *
	 * @param   bool    $distinct  enable or disable distinct values
	 * @return  object  current instance
	 */
	public function distinct($distinct = true)
	{
		$this->distinct = $distinct;
		
		return $this;
	}

	/**
	 * Creates a "GROUP BY ..." filter.
	 *
	 * @param   mixed   column name or array($column, $alias)
	 * @param   ...
	 * @return  object  $this
	 */
	public function groupBy($columns)
	{
		$columns = func_get_args();

		$this->groupBy = array_merge($this->groupBy, $columns);

		return $this;
	}

	/**
	 * Adds a new join.
	 *
	 * @param   string  $table  string column name or alias array
	 * @param   string  $type   join type
	 * @return  object  current instance
	 */
	public function join($table, $type = null)
	{
		$this->join[] = $this->_lastJoin = new Join($table, $type);

		return $this;
	}
	
	/**
	 * Sets an "AND ON" clause on the last join.
	 *
	 * @param   string  $column1  column name
	 * @param   string  $op       logic operator
	 * @param   string  $column2  column name
	 * @return  object  current instance
	 */
	public function on($column1, $op, $column2 = null)
	{
		if( ! $this->_lastJoin)
		{
			throw new Exception('You must first join a table before setting an "ON" clause.');
		}

		call_user_func_array(array($this->_lastJoin, 'on'), func_get_args());

		return $this;
	}

	/**
	 * Sets an "AND ON" clause on the last join.
	 *
	 * @param   string  $column1  column name
	 * @param   string  $op       logic operator
	 * @param   string  $column2  column name
	 * @return  object  current instance
	 */
	public function andOn($column1, $op, $column2 = null)
	{
		if( ! $this->_lastJoin)
		{
			throw new Exception('You must first join a table before setting an "AND ON" clause.');
		}

		call_user_func_array(array($this->_lastJoin, 'andOn'), func_get_args());

		return $this;
	}

	/**
	 * Sets an "OR ON" clause on the last join.
	 *
	 * @param   string  $column1  column name
	 * @param   string  $op       logic operator
	 * @param   string  $column2  column name
	 * @return  object  current instance
	 */
	public function orOn($column1, $op, $column2 = null)
	{
		if( ! $this->_lastJoin)
		{
			throw new Exception('You must first join a table before setting an "OR ON" clause.');
		}

		call_user_func_array(array($this->_lastJoin, 'orOn'), func_get_args());

		return $this;
	}

	/**
	 * Alias for andHaving.
 	 *
	 * @param   mixed   $column  array of 'and having' statements or column name
	 * @param   string  $op      having logic operator
	 * @param   mixed   $value   having value
	 * @return  object  current instance
	 */
	public function having($column, $op, $value = null)
	{
		return call_user_func_array(array($this, 'andHaving'), func_get_args());
	}
	
	/**
	 * Adds an 'and having' statement to the query.
	 *
	 * @param   mixed   $column  array of 'and having' statements or column name
	 * @param   string  $op      having logic operator
	 * @param   mixed   $value   having value
	 * @return  object  current instance
	 */
	public function andHaving($column, $op = null, $value = null)
	{
		if($column instanceof \Closure)
		{
			$this->andHavingOpen();
			$column($this);
			$this->andHavingClose();
			return $this;
		}

		if (func_num_args() == 2)
		{
			$value = $op;
			$op = '=';
		}

		return $this->_having('and', $column, $op, $value);
	}
	
	/**
	 * Adds an 'or having' statement to the query.
	 *
	 * @param   mixed   $column  array of 'or having' statements or column name
	 * @param   string  $op      having logic operator
	 * @param   mixed   $value   having value
	 * @return  object  current instance
	 */
	public function orHaving($column, $op = null, $value = null)
	{
		if($column instanceof \Closure)
		{
			$this->orHavingOpen();
			$column($this);
			$this->orHavingClose();
			return $this;
		}

		if (func_num_args() == 2)
		{
			$value = $op;
			$op = '=';
		}

		return $this->_having('or', $column, $op, $value);
	}
	
	/**
	 * Opens an 'and having' nesting.
	 *
	 * @return  object  current instance
	 */
	public function havingOpen()
	{
		$this->having[] = array(
			'type' => 'and',
			'nesting' => 'open',
		);

		return $this;
	}
	
	/**
	 * Closes an 'and having' nesting.
	 *
	 * @return  object  current instance
	 */
	public function havingClose()
	{
		$this->having[] = array(
			'type' => 'and',
			'nesting' => 'close',
		);

		return $this;
	}
	
	/**
	 * Opens an 'and having' nesting.
	 *
	 * @return  object  current instance
	 */
	public function andHavingOpen()
	{
		$this->having[] = array(
			'type' => 'and',
			'nesting' => 'open',
		);

		return $this;
	}
	
	/**
	 * Closes an 'and having' nesting.
	 *
	 * @return  object  current instance
	 */
	public function andHavingClose()
	{
		$this->having[] = array(
			'type' => 'and',
			'nesting' => 'close',
		);

		return $this;
	}
	
	/**
	 * Opens an 'or having' nesting.
	 *
	 * @return  object  current instance
	 */
	public function orHavingOpen()
	{
		$this->having[] = array(
			'type' => 'or',
			'nesting' => 'open',
		);

		return $this;
	}
	
	/**
	 * Closes an 'or having' nesting.
	 *
	 * @return  object  current instance
	 */
	public function orHavingClose()
	{
		$this->having[] = array(
			'type' => 'or',
			'nesting' => 'close',
		);

		return $this;
	}
	
	/**
	 * Adds an 'and having' statement to the query
	 *
	 * 
	 * @param   mixed   $column  array of 'and having' statements or column name
	 * @param   string  $op      having logic operator
	 * @param   mixed   $value   having value
	 * @return  object  current instance
	 */
	protected function _having($type, $column, $op, $value)
	{
		if (is_array($column) and $op = null and $value = null)
		{
			foreach ($column as $key => $val)
			{
				if (is_array($val))
				{
					if (count($val) === 2)
					{
						$this->having[] = array(
							'type' => $type,
							'field' => $val[0],
							'op' => '=',
							'value' => $val[1],
						);
					}
					else
					{
						$this->having[] = array(
							'type' => $type,
							'field' => $val[0],
							'op' => $val[1],
							'value' => $val[2],
						);
					}
				}
			}
		}
		else
		{
			$this->having[] = array(
				'type' => $type,
				'field' => $column,
				'op' => $op,
				'value' => $value,
			);
		}

		return $this;
	}
}