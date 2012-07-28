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

namespace Cabinet\DBAL\Compiler;

use Cabinet\DBAL\Compiler;
use Cabinet\DBAL\Query\Base;

abstract class Sql extends Compiler
{
	/**
	 * @var  string  $tableQuote  table quote
	 */
	protected static $tableQuote = '`';

	/**
	 * Compiles a INSERT query
	 *
	 * @return  string  compiled INSERT query
	 */
	public function compileSelect()
	{
		$sql = $this->compilePartSelect();
		$sql .= $this->compilePartFrom();
		$sql .= $this->compilePartJoin();
		$sql .= $this->compilePartWhere();
		$sql .= $this->compilePartGroupBy();
		$sql .= $this->compilePartHaving();
		$sql .= $this->compilePartOrderBy();
		$sql .= $this->compilePartLimitOffset();
		return $sql;
	}

	/**
	 * Compiles a UPDATE query
	 *
	 * @return  string  compiled UPDATE query
	 */
	public function compileUpdate()
	{
		$sql = $this->compilePartUpdate();
		$sql .= $this->compilePartSet();
		$sql .= $this->compilePartWhere();
		$sql .= $this->compilePartOrderBy();
		$sql .= $this->compilePartLimitOffset();
		return $sql;
	}

	/**
	 * Compiles a DELETE query
	 *
	 * @return  string  compiled DELETE query
	 */
	public function compileDelete()
	{
		$sql = $this->compilePartDelete();
		$sql .= $this->compilePartWhere();
		$sql .= $this->compilePartOrderBy();
		$sql .= $this->compilePartLimitOffset();
		return $sql;
	}

	/**
	 * Compiles a INSERT query
	 *
	 * @return  string  compiled INSERT query
	 */
	public function compileInsert()
	{
		$sql = $this->compilePartInsert();
		$sql .= $this->compilePartInsertValues();
		return $sql;
	}

	/**
	 * Compiles a DROP DATABASE query
	 *
	 * @return  string  compiles DROP DATABASE query
	 */
	public function compileDatabaseDrop()
	{
		return 'DROP DATABASE '
			.($this->query['ifExists'] ? 'IF EXISTS ' : '')
			.$this->quoteIdentifier($this->query['database']);
	}

	/**
	 * Compiles a CREATE DATABASE query
	 *
	 * @return  string  compiles DROP DATABASE query
	 */
	public function compileDatabaseCreate()
	{
		return 'CREATE DATABASE '
			.($this->query['ifNotExists'] ? 'IF NOT EXISTS ' : '')
			.$this->quoteIdentifier($this->query['database'])
			.$this->compilePartCharset($this->query['charset']);
	}

	/**
	 * Compiles a DROP TABLE query
	 *
	 * @return  string  compiles DROP TABLE query
	 */
	public function compileTableDrop()
	{
		return 'DROP TABLE '
			.($this->query['ifExists'] ? 'IF EXISTS ' : '')
			.$this->quoteIdentifier($this->query['table']);
	}

	/**
	 * Compiles a RENAME TABLE query
	 */
	public function compileTableRename()
	{
		return 'RENAME TABLE '.$this->quoteIdentifier($this->query['table']).' TO '.$this->quoteIdentifier($this->query['newName']);
	}

	public function compileTableDropFields()
	{
		return 'ALTER TABLE '.
			$this->quoteIdentifier($this->query['table']).
			' DROP '.
			join(', ', array_map(array($this, 'quoteIdentifier'), $this->query['fields']));
	}

	public function compileTableAlterFields()
	{
		return 'ALTER TABLE '.
			$this->quoteIdentifier($this->query['table']).' '.
			$this->compilePartFields('alter');
	}

	public function compileTableAddFields()
	{
		return 'ALTER TABLE '.
			$this->quoteIdentifier($this->query['table']).' '.
			$this->compilePartFields('add');
	}

	public function compileTableCreate()
	{
		$sql = 'CREATE TABLE ';

		$this->query['ifNotExists'] and $sql .= 'IF NOT EXISTS ';
		$sql .= $this->quoteIdentifier($this->query['table']).' ( ';
		$sql .= $this->compilePartFields('create');
		$sql .= $this->compilePartIndexes();
		$sql .= ' ) '.$this->compilePartEngine();
		$sql .= $this->compilePartCharset($this->query['charset']);
		return $sql;
	}

	/**
	 * Compiles field parts
	 *
	 * @param   string  $type  field/query type
	 * @return  string  compiled field sql
	 */
	protected function compilePartFields($type)
	{
		$fields = array();

		foreach ($this->query['fields'] as $field)
		{
			$data = $field->getContents();

			if($type === 'alter')
			{
				if ($data['newName'] and $data['name'] !== $data['newName'])
				{
					$type = 'change';
				}
				else
				{
					$type = 'modify';
				}
			}

			$fsql = $type !== 'create' ? strtoupper($type).' ' : '';
			$fsql .= $this->quoteIdentifier($data['name']).' ';

			if ($data['newName'])
			{
				$fsql .= $this->quoteIdentifier($data['newName']).' ';
			}

			$fsql .= strtoupper($data['type']);

			if ($data['constraint'])
			{
				$constraint = is_array($data['constraint']) ? $data['constraint'] : array($data['constraint']);
				$fsql .= '('.join(', ', array_map(array($this, 'quote'), $constraint)).')';
			}

			if ($data['charset'])
			{
				$fsql .= ' '.$this->compilePartCharset($data['charset']);
			}

			if ($data['unsigned'])
			{
				$fsql .= ' UNSIGNED';
			}

			if ($data['defaultValue'])
			{
				$fsql .= ' DEFAULT '.$this->quote($data['defaultValue']);
			}

			if ($data['null'])
			{
				$fsql .= ' NULL';
			}
			else
			{
				$fsql .= ' NOT NULL';
			}

			if($data['incremental'])
			{
				$fsql .= ' AUTO_INCREMENT';
			}

			if($data['first'])
			{
				$fsql .= ' FIRST';
			}

			if ($data['after'])
			{
				$fsql .= ' AFTER '.$this->quoteIdentifier($data['after']);
			}

			if ($data['comments'])
			{
				$fsql .= ' COMMENT '.$this->quote($data['comments']);
			}

			$fields[] = $fsql;
		}

		return join(', ', $fields);
	}

	public function compilePartIndexes()
	{
		if (empty($this->query['indexes']))
		{
			return '';
		}

		$parts = array();

		foreach ($this->query['indexes'] as $index)
		{
			$data = $index->getContents();

			// format the compiler function
			$compiler = 'compileIndex'.str_replace(' ', '', ucwords(strtolower($data['index'])));

			if(method_exists($this, $compiler))
			{
				$parts[] = $this->{$compiler}($index);
			}
			else
			{
				$name = empty($data['name']) ? join($data['on']) : $data['name'];
				$sql = strtoupper($data['index']).' '.$this->quoteIdentifier($name).' (';
				$sql .= join(', ', array_map(array($this, 'quoteIdentifier'), $data['on'])).')';
				$parts[] = $sql;
			}
		}

		return ', '.join(', ',$parts);
	}

	/**
	 * 
	 */
	public function compilePartEngine()
	{
		return $this->query['engine'] ? ' ENGINE = '.$this->query['engine'] : '';
	}

	/**
	 * Compiles charset statements.
	 *
	 * @param   string  $charset  charset to compile
	 * @return  string  compiled charset statement
	 */
	protected function compilePartCharset($charset)
	{
		if (empty($charset))
		{
			return '';
		}

		if (($pos = stripos($charset, '_')) !== false)
		{
			$charset = ' CHARACTER SET '.substr($charset, 0, $pos).' COLLATE '.$charset;
		}
		else
		{
			$charset = ' CHARACTER SET '.$charset;
		}

		isset($this->query['charsetIsDefault']) and $this->query['charsetIsDefault'] and $charset = ' DEFAULT'.$charset;

		return $charset;
	}

	/**
	 * Compiles conditions for where and having statements.
	 *
	 * @param   array   $conditions  conditions array
	 * @return  string  compiled conditions
	 */
	protected function compileConditions($conditions)
	{
		$parts = array();
		$last = false;

		foreach ($conditions as $c)
		{
			if (isset($c['type']) and count($parts) > 0)
			{
				$parts[] = ' '.strtoupper($c['type']).' ';
			}
			
			if ($useNot = (isset($c['not']) and $c['not']))
			{
				$parts[] = count($parts) > 0 ? 'NOT ' : ' NOT '; 
			}

			if (isset($c['nesting']))
			{
				if ($c['nesting'] === 'open')
				{
					if ($last === '(')
					{
						array_pop($parts);

						if ($useNot)
						{
							array_pop($parts);
							$parts[] = ' NOT ';
						}
					}

					$last = '(';
					$parts[] = '(';
				}
				else
				{
					$last = ')';
					$parts[] = ')';
				}
			}
			else
			{
				if($last === '(')
				{
					array_pop($parts);

					if ($useNot)
					{
						array_pop($parts);
						$parts[] = ' NOT ';
					}
				}

				$last = false;
				$c['op'] = trim($c['op']);

				if ($c['value'] === null)
				{
					if ($c['op'] === '!=')
					{
						$c['op'] = 'IS NOT';
					}
					elseif ($c['op'] === '=')
					{
						$c['op'] = 'IS';
					}
				}

				$c['op'] = strtoupper($c['op']);

				if($c['op'] === 'BETWEEN' and is_array($c['value']))
				{
					list($min, $max) = $c['value'];
					$c['value'] = $this->quote($min).' AND '.$this->quote($max);
				}
				else
				{
					$c['value'] = $this->quote($c['value']);
				}

				$c['field'] = $this->quoteIdentifier($c['field']);
				$parts[] = $c['field'].' '.$c['op'].' '.$c['value'];
			}
		}

		return trim(implode('', $parts));
	}

	/**
	 * Compiles SQL functions
	 *
	 * @param   object  $value   function object
	 * @return  string  compiles SQL function
	 */
	protected function compilePartFn($value)
	{
		$fn = ucfirst($value->getFn());

		if(method_exists($this, 'compileFn'.$fn))
		{
			return $this->{'compilFn'.$fn}($value);
		}

		$quoteFn = ($value->quoteAs() === 'identifier') ? 'quoteIdentifier' : 'quote';

		return strtoupper($fn).'('.join(', ', array_map(array($this, $quoteFn), $value->getParams())).')';
	}

	/**
	 * Compiles the INSERT INTO STATEMENT
	 *
	 *
	 */
	protected function compilePartInsert()
	{
		return 'INSERT INTO '.$this->query['table'];
	}

	/**
	 * Compiles the insert values.
	 *
	 * @return  string
	 */
	protected function compilePartInsertValues()
	{
		$sql = ' ('.join(' , ', $this->query['columns']).') VALUES (';
		$parts = array();

		foreach ($this->query['values'] as $row)
		{
			foreach ($this->query['columns'] as $c)
			{
				if (array_key_exists($c, $row))
				{
					$parts[] = $this->quote($row[$c]);
				}
				else
				{
					$parts[] = 'NULL';
				}
			}
		}

		return $sql.join(', ', $parts).')';
	}

	/**
	 * Compiles SELECT statement
	 *
	 * @return  string  compiled SELECT statement
	 */
	protected function compilePartSelect()
	{
		$columns = $this->query['columns'];
		empty($columns) and $columns = array('*');
		$columns = array_map(array($this, 'quoteIdentifier'), $columns);
		return 'SELECT'.($this->query['distinct'] === true ? ' DISTINCT ' : ' ').trim(join(', ', $columns));
	}

	/**
	 * Compiles DELETE statement
	 *
	 * @return  string  compiled DELETE statement
	 */
	protected function compilePartDelete()
	{
		return 'DELETE FROM '.$this->quoteIdentifier($this->query['table']);
	}

	/**
	 * Compiles UPDATE statement
	 *
	 * @return  string  compiled UPDATE statement
	 */
	protected function compilePartUpdate()
	{
		return 'UPDATE '.$this->quoteIdentifier($this->query['table']);
	}

	/**
	 * Compiles FROM statement
	 *
	 * @return  string  compiled FROM statement
	 */
	protected function compilePartFrom()
	{
		$tables = $this->query['table'];
		is_array($tables) or $tables = array($tables);
		return ' FROM '.join(', ', array_map(array($this, 'quoteIdentifier'), $tables));
	}

	/**
	 * Compiles the WHERE statement
	 *
	 * @return  string  compiled WHERE statement
	 */
	protected function compilePartWhere()
	{
		if ( ! empty($this->query['where']))
		{
			// Add selection conditions
			return ' WHERE '.$this->compileConditions($this->query['where']);
		}

		return '';
	}

	/**
	 * Compiles the SET statement
	 *
	 * @return  string  compiled WHERE statement
	 */
	protected function compilePartSet()
	{
		if ( ! empty($this->query['values']))
		{
			$parts = array();

			foreach ($this->query['values'] as $k => $v)
			{
				$parts[] = $this->quoteIdentifier($k).' = '.$this->quote($v);
			}

			return ' SET '.join(', ', $parts);
		}

		return '';
	}

	/**
	 * Compiles the HAVING statement
	 *
	 * @return  string  compiled HAVING statement
	 */
	protected function compilePartHaving()
	{
		if ( ! empty($this->query['having']))
		{
			// Add selection conditions
			return ' HAVING '.$this->compileConditions($this->query['having']);
		}

		return '';
	}

	/**
	 * Compiles the ORDER BY statement
	 *
	 * @return  string  compiled ORDER BY statement
	 */
	protected function compilePartOrderBy()
	{
		if ( ! empty($this->query['orderBy']))
		{
			$sort = array();

			foreach ($this->query['orderBy'] as $group)
			{
				extract($group);

				if ( ! empty($direction))
				{
					// Make the direction uppercase
					$direction = ' '.strtoupper($direction);
				}

				$sort[] = $this->quoteIdentifier($column).$direction;
			}

			return ' ORDER BY '.implode(', ', $sort);
		}

		return '';
	}

	/**
	 * Compiles the JOIN statements
	 *
	 * @return  string  compiled JOIN statement
	 */
	protected function compilePartJoin()
	{
		if (empty($this->query['join']))
		{
			return '';
		}

		$return = array();

		foreach ($this->query['join'] as $join)
		{
			$join = $join->asArray();

			if ($join['type'])
			{
				$sql = strtoupper($join['type']).' JOIN';
			}
			else
			{
				$sql = 'JOIN';
			}

			// Quote the table name that is being joined
			$sql .= ' '.$this->quoteIdentifier($join['table']).' ON ';

			$on_sql = '';
			foreach ($join['on'] as $condition)
			{
				// Split the condition
				list($c1, $op, $c2, $andOr) = $condition;

				if ($op)
				{
					// Make the operator uppercase and spaced
					$op = ' '.strtoupper($op);
				}

				// Quote each of the identifiers used for the condition
				$on_sql .= (empty($on_sql) ? '' : ' '.$andOr.' ').$this->quoteIdentifier($c1).$op.' '.$this->quoteIdentifier($c2);
			}

			// Concat the conditions "... AND ..."
			$sql .= '('.$on_sql.')';

			$return[] = $sql;
		}

		return ' '.implode(' ', $return);
	}

	/**
	 * Compiles the GROUP BY statement
	 *
	 * @return  string  compiler GROUP BY statement
	 */
	protected function compilePartGroupBy()
	{
		if ( ! empty($this->query['groupBy']))
		{
			// Add sorting
			return ' GROUP BY '.implode(', ', array_map(array($this, 'quoteIdentifier'), $this->query['groupBy']));
		}

		return '';
	}

	/**
	 * Compiles the LIMIT and OFFSET statement.
	 *
	 * @return  string  compiled limit and offset statement
	 */
	protected function compilePartLimitOffset()
	{
		$part = '';

		if ($this->query['limit'] !== null)
		{
			$part .= ' LIMIT '.$this->query['limit'];
		}

		if ($this->query['offset'] !== null)
		{
			$part .= ' OFFSET '.$this->query['offset'];
		}

		return $part;
	}

	/**
	 * Quotes an identifier
	 *
	 * @param   mixed   $value  value to quote
	 * @return  string  quoted identifier
	 */
	public function quoteIdentifier($value)
	{
		if ($value === '*')
		{
			return $value;
		}

		if (is_object($value))
		{
			if ($value instanceof Base)
			{
				// Create a sub-query
				return '('.$value->compile($this->connection).')';
			}
			elseif ($value instanceof \Cabinet\DBAL\Expression)
			{
				// Use a raw expression
				return $value->handle($this);
			}
			elseif ($value instanceof \Cabinet\DBAL\Fn)
			{
				return $this->compilePartFn($value);
			}
			else
			{
				// Convert the object to a string
				return $this->quoteIdentifier((string) $value);
			}
		}

		if (is_array($value))
		{
			// Separate the column and alias
			list ($_value, $alias) = $value;
			return $this->quoteIdentifier($_value).' AS '.$this->quoteIdentifier($alias);
		}

		if (strpos($value, '"') !== false)
		{
			// Quote the column in FUNC("ident") identifiers
			return preg_replace('/"(.+?)"/e', '$this->quoteIdentifier("$1")', $value);
		}

		if (strpos($value, '.') !== false)
		{
			// Split the identifier into the individual parts
			$parts = explode('.', $value);

			// Quote each of the parts
			return implode('.', array_map(array($this, __FUNCTION__), $parts));
		}

		return static::$tableQuote.$value.static::$tableQuote;
	}

	/**
	 * Quote a value for an SQL query.
	 *
	 * Objects passed to this function will be converted to strings.
	 * Expression objects will use the value of the expression.
	 * Query objects will be compiled and converted to a sub-query.
	 * Fn objects will be send of for compiling.
	 * All other objects will be converted using the `__toString` method.
	 *
	 * @param   mixed   any value to quote
	 * @return  string
	 * @uses    static::escape
	 */
	public function quote($value)
	{
		if ($value === null)
		{
			return 'NULL';
		}
		elseif ($value === true)
		{
			return "'1'";
		}
		elseif ($value === false)
		{
			return "'0'";
		}
		elseif (is_object($value))
		{
			if ($value instanceof Base)
			{
				// create a sub-query
				return '('.$value->compile($this->connection).')';
			}
			if ($value instanceof \Cabinet\DBAL\Fn)
			{
				// compile the function
				return $this->compilePartFn($value);
			}
			elseif ($value instanceof \Cabinet\DBAL\Expression)
			{
				// get the output from the expression
				return $value->handle($this);
			}
			else
			{
				// Convert the object to a string
				return $this->quote((string) $value, $commands['params']);
			}
		}
		elseif (is_array($value))
		{
			return '('.implode(', ', array_map(array($this, 'quote'), $value)).')';
		}
		elseif (is_int($value))
		{
			return (int) $value;
		}
		elseif (is_float($value))
		{
			// Convert to non-locale aware float to prevent possible commas
			return sprintf('%F', $value);
		}

		return $this->escape($value);
	}

	/**
	 * Escapes a value
	 *
	 * @param   string  $value  value to escape
	 * @return  string  escaped string
	 */
	public function escape($value)
	{
		return $this->connection->quote($value);
	}
}
