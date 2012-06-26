<?php

namespace Cabinet\Database\Collector;

use Cabinet\Database\Collector;
use Cabinet\Database\Db;

class Where extends Collector
{
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
			$this->andWhereClose();
			return $this;
		}

		if (func_num_args() === 2)
		{
			$value = $op;
			$op = '=';
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
			$this->orWhereClose();
			return $this;
		}

		if (func_num_args() === 2)
		{
			$value = $op;
			$op = '=';
		}

		return $this->_where('or', $column, $op, $value);
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
			'type' => 'and',
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
		$this->where[] = array(
			'type' => 'and',
			'nesting' => 'close',
		);

		return $this;
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
		$this->where[] = array(
			'type' => 'or',
			'nesting' => 'close',
		);

		return $this;
	}
	
	/**
	 * Adds an 'and where' statement to the query
	 *
	 * @param   string  $type    chain type
	 * @param   mixed   $column  array of 'where' statements or column name
	 * @param   string  $op      where logic operator
	 * @param   mixed   $value   where value
	 * @return  object  current instance
	 */
	protected function _where($type, $column, $op, $value)
	{
		if (is_array($column) and $op = null and $value = null)
		{
			foreach ($column as $key => $val)
			{
				if (is_array($val))
				{
					if (count($val) === 2)
					{
						$this->where[] = array(
							'type' => $type,
							'field' => $val[0],
							'op' => '=',
							'value' => $val[1],
						);
					}
					else
					{
						$this->where[] = array(
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
			$this->where[] = array(
				'type' => $type,
				'field' => $column,
				'op' => $op,
				'value' => $value,
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