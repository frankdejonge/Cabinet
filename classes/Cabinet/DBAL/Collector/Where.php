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

namespace Cabinet\DBAL\Collector;

use Cabinet\DBAL\Collector;
use Cabinet\DBAL\Db;

class Where extends Collector
{
	/**
	 * @var  array  $where  where conditions
	 */
	public $where = array();

	/**
	 * @var  array  $orderBy  ORDER BY clause
	 */
	public $orderBy = array();

	/**
	 * @var  integer  $limit  query limit
	 */
	public $limit;


	/**
	 * @var  array  $offset  query offset
	 */
	public $offset;

	/**
	 * Alias for andWhere.
 	 *
	 * @param   mixed   $column  array of 'and where' statements or column name
	 * @param   string  $op      where logic operator
	 * @param   mixed   $value   where value
	 * @return  object  current instance
	 */
	public function where($column, $op = null, $value = null)
	{
		return call_user_func_array(array($this, 'andWhere'), func_get_args());
	}

	/**
	 * Adds an 'and where' statement to the query.
	 *
	 * @param   mixed   $column  array of 'and where' statements or column name
	 * @param   string  $op      where logic operator
	 * @param   mixed   $value   where value
	 * @return  object  current instance
	 */
	public function andWhere($column, $op = null, $value = null)
	{
		if($column instanceof \Closure)
		{
			$this->andWhereOpen();
			$column($this);
			$this->whereClose();

			return $this;
		}

		if (func_num_args() === 2)
		{
			$value = $op;
			$op = is_array($value) ? 'in' : '=';
		}

		return $this->_where('and', $column, $op, $value);
	}

	/**
	 * Adds an 'or where' statement to the query.
	 *
	 * @param   mixed   $column  array of 'or where' statements or column name
	 * @param   string  $op      where logic operator
	 * @param   mixed   $value   where value
	 * @return  object  current instance
	 */
	public function orWhere($column, $op = null, $value = null)
	{
		if($column instanceof \Closure)
		{
			$this->orWhereOpen();
			$column($this);
			$this->whereClose();

			return $this;
		}

		if (func_num_args() === 2)
		{
			$value = $op;
			$op = is_array($value) ? 'in' : '=';
		}

		return $this->_where('or', $column, $op, $value);
	}

	/**
	 * Alias for andWhere.
 	 *
	 * @param   mixed   $column  array of 'and not where' statements or column name
	 * @param   string  $op      where logic operator
	 * @param   mixed   $value   where value
	 * @return  object  current instance
	 */
	public function notWhere($column, $op = null, $value = null)
	{
		return call_user_func_array(array($this, 'andNotWhere'), func_get_args());
	}

	/**
	 * Adds an 'and not where' statement to the query.
	 *
	 * @param   mixed   $column  array of 'and where' statements or column name
	 * @param   string  $op      where logic operator
	 * @param   mixed   $value   where value
	 * @return  object  current instance
	 */
	public function andNotWhere($column, $op = null, $value = null)
	{
		if($column instanceof \Closure)
		{
			$this->andNotWhereOpen();
			$column($this);
			$this->whereClose();

			return $this;
		}

		if (func_num_args() === 2)
		{
			$value = $op;
			$op = is_array($value) ? 'in' : '=';
		}

		return $this->_where('and', $column, $op, $value, true);
	}

	/**
	 * Adds an 'or not where' statement to the query.
	 *
	 * @param   mixed   $column  array of 'or where' statements or column name
	 * @param   string  $op      where logic operator
	 * @param   mixed   $value   where value
	 * @return  object  current instance
	 */
	public function orNotWhere($column, $op = null, $value = null)
	{
		if($column instanceof \Closure)
		{
			$this->orNotWhereOpen();
			$column($this);
			$this->whereClose();

			return $this;
		}

		if (func_num_args() === 2)
		{
			$value = $op;
			$op = is_array($value) ? 'in' : '=';
		}

		return $this->_where('or', $column, $op, $value, true);
	}

	/**
	 * Opens an 'and where' nesting.
	 *
	 * @return  object  current instance
	 */
	public function whereOpen()
	{
		$this->where[] = array(
			'type' => 'and',
			'nesting' => 'open',
		);

		return $this;
	}

	/**
	 * Closes an 'and where' nesting.
	 *
	 * @return  object  current instance
	 */
	public function whereClose()
	{
		$this->where[] = array(
			'nesting' => 'close',
		);

		return $this;
	}

	/**
	 * Opens an 'and where' nesting.
	 *
	 * @return  object  current instance
	 */
	public function andWhereOpen()
	{
		$this->where[] = array(
			'type' => 'and',
			'nesting' => 'open',
		);

		return $this;
	}

	/**
	 * Closes an 'and where' nesting.
	 *
	 * @return  object  current instance
	 */
	public function andWhereClose()
	{
		return $this->whereClose();
	}

	/**
	 * Opens an 'or where' nesting.
	 *
	 * @return  object  current instance
	 */
	public function orWhereOpen()
	{
		$this->where[] = array(
			'type' => 'or',
			'nesting' => 'open',
		);

		return $this;
	}

	/**
	 * Closes an 'or where' nesting.
	 *
	 * @return  object  current instance
	 */
	public function orWhereClose()
	{
		return $this->whereClose();
	}

	/**
	 * Opens an 'and not where' nesting.
	 *
	 * @return  object  current instance
	 */
	public function notWhereOpen()
	{
		$this->where[] = array(
			'type' => 'and',
			'not' => true,
			'nesting' => 'open',
		);

		return $this;
	}

	/**
	 * Closes an 'and not where' nesting.
	 *
	 * @return  object  current instance
	 */
	public function notWhereClose()
	{
		return $this->whereClose();
	}

	/**
	 * Opens an 'and not where' nesting.
	 *
	 * @return  object  current instance
	 */
	public function andNotWhereOpen()
	{
		$this->where[] = array(
			'type' => 'and',
			'not' => true,
			'nesting' => 'open',
		);

		return $this;
	}

	/**
	 * Closes an 'and not where' nesting.
	 *
	 * @return  object  current instance
	 */
	public function andNotWhereClose()
	{
		return $this->whereClose();
	}

	/**
	 * Opens an 'or not where' nesting.
	 *
	 * @return  object  current instance
	 */
	public function orNotWhereOpen()
	{
		$this->where[] = array(
			'type' => 'or',
			'not' => true,
			'nesting' => 'open',
		);

		return $this;
	}

	/**
	 * Closes an 'or where' nesting.
	 *
	 * @return  object  current instance
	 */
	public function orNotWhereClose()
	{
		return $this->whereClose();
	}

	/**
	 * Adds an 'and where' statement to the query
	 *
	 * @param   string   $type    chain type
	 * @param   mixed    $column  array of 'where' statements or column name
	 * @param   string   $op      where logic operator
	 * @param   mixed    $value   where value
	 * @param   boolean  $not     wether to use NOT
	 * @return  object   current instance
	 */
	protected function _where($type, $column, $op, $value, $not = false)
	{
		if (is_array($column) and $op = null and $value = null)
		{
			foreach ($column as $key => $val)
			{
				if (is_array($val))
				{
					$numArgs = count($val);

					if ($numArgs === 2)
					{
						$this->where[] = array(
							'type' => $type,
							'field' => $val[0],
							'op' => '=',
							'value' => $val[1],
							'not' => false,
						);
					}
					elseif ($numArgs === 3)
					{
						$this->where[] = array(
							'type' => $type,
							'field' => $val[0],
							'op' =>  $val[1],
							'value' => $val[2],
							'not' => false,
						);
					}
					else
					{
						$this->where[] = array(
							'type' => $type,
							'field' => $val[0],
							'op' => $val[1],
							'value' => $val[2],
							'not' => $val[3]
						);
					}
				}
			}
		}
		else
		{
			$this->where[] = array(
				'type' => $type,
				'field' => $column,
				'op' => $op,
				'value' => $value,
				'not' => $not,
			);
		}

		return $this;
	}

	/**
	 * Adds an 'order by' statment to the query.
	 *
	 * @param   string|array  $column     array of staments or column name
	 * @param   string        $direction  optional order direction
	 * @return  object        current instance
	 */
	public function orderBy($column, $direction = null)
	{
		if (is_array($column))
		{
			foreach ($column as $key => $val)
			{
				if (is_numeric($key))
				{
					$key = $val;
					$val = null;
				}

				$this->orderBy[] = array(
					'column' => $key,
					'direction' => $val,
				);
			}
		}
		else
		{
			$this->orderBy[] = array(
				'column' => $column,
				'direction' => $direction,
			);
		}

		return $this;
	}

	/**
	 * Sets a limit [and offset] for the query
	 *
	 * @param   int     limit integer
	 * @param   int     offset integer
	 * @return  object  current instance
	 */
	public function limit($limit, $offset = null)
	{
		$this->limit = (int) $limit;
		func_num_args() > 1 and $this->offset = (int) $offset;

		return $this;
	}

	/**
	 * Sets an offset for the query
	 *
	 * @param   int     offset integer
	 * @return  object  current instance
	 */
	public function offset($offset)
	{
		$this->offset = (int) $offset;

		return $this;
	}
}
