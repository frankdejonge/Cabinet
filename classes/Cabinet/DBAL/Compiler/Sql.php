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
use Cabinet\DBAL\Base;

abstract class Sql extends Compiler
{
	/**
	 * Compiles an insert query
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
	 * Compiles an update query
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
	 * Compiles a delete query
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
	 * Compiles an insert query
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
	 * Compiles a drop databse query
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
	 * Compiles a create database query
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
	 * Compiles a drop table query
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
	 * Compiles a rename table query
	 *
	 * @return  string  compiled rename table query
	 */
	public function compileTableRename()
	{
		return 'RENAME TABLE '.$this->quoteIdentifier($this->query['table']).' TO '.$this->quoteIdentifier($this->query['newName']);
	}

	/**
	 * Compile an alter table query for dropping fields.
	 *
	 * @return  string  compiled create table query
	 */
	public function compileTableDropFields()
	{
		return 'ALTER TABLE '.
			$this->quoteIdentifier($this->query['table']).
			' DROP '.
			join(', ', array_map(array($this, 'quoteIdentifier'), $this->query['fields']));
	}

	/**
	 * Compile an alter table query for altering fields.
	 *
	 * @return  string  compiled create table query
	 */
	public function compileTableAlterFields()
	{
		return 'ALTER TABLE '.
			$this->quoteIdentifier($this->query['table']).' '.
			$this->compilePartFields('alter');
	}

	/**
	 * Compile an alter table query for adding fields.
	 *
	 * @return  string  compiled create table query
	 */
	public function compileTableAddFields()
	{
		return 'ALTER TABLE '.
			$this->quoteIdentifier($this->query['table']).' '.
			$this->compilePartFields('add');
	}

	/**
	 * Compile a create table query.
	 *
	 * @return  string  compiled create table query
	 */
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
		$fieldsSql = array();
		$fields = $this->prepareFields($this->query['fields']);

		foreach ($fields as $data)
		{
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

			if ($data['primary'])
			{
				$fsql .= ' PRIMARY KEY';
			}

			if ($data['unsigned'])
			{
				$fsql .= ' UNSIGNED';
			}

			if ($data['defaultValue'])
			{
				$fsql .= ' DEFAULT '.$this->quote($data['defaultValue']);
			}

			if ($data['nullable'])
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

			$fieldsSql[] = $fsql;
		}

		return join(', ', $fieldsSql);
	}

	/**
	 * Compiles the table indexes.
	 *
	 * @return  string  compiled index sql
	 */
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

	protected function compilePartForeignKeys()
	{
		if (empty($this->query['foreignKeys']))
		{
			return '';
		}

		$sql = array();
		$part = array();

		foreach ($this->query['foreignKeys'] as $fk)
		{
			if ($fk['constraint'])
			{
				$part[] = 'CONTSTRAINT '.$this->quoteIdentifier($fk['constraint']);
			}
			
			$part[] = 'FOREIGN KEY ('.$this->quoteIdentifier($fk['key']).')';
			$part[] = 'REFERENCES '.$this->quoteIdentifier($fk['reference']['table']).' ('.
				$this->quoteIdentifier($fk['reference']['columns']).')';

			if ($fk['onUpdate'])
			{
				$part[] = 'ON UPDATE '.strtoupper($fk['onUpdate']);
			}

			if ($fk['onDelete'])
			{
				$part[] = 'ON DELETE '.strtoupper($fk['onDelete']);
			}

			$sql[] = join(' ', $part);
			$part = array();
		}

		return join(', ');
	}

	/**
	 * Prepares the fields for rendering.
	 *
	 * @param   array  $fields  array with field objects
	 * @return  array  array with prepped field objects
	 */
	protected function prepareFields($fields)
	{
		return array_map(function($field) {
			return $field->getContents();
		}, $fields);
	}

	/**
	 * Compiles the ENGINE statement
	 *
	 * @return  string  compiled ENGINE statement
	 */
	protected function compilePartEngine()
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
	public function compilePartFn($value)
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
	 * Compiles the insert into part.
	 *
	 * @return  string  compiled inter into part
	 */
	protected function compilePartInsert()
	{
		return 'INSERT INTO '.$this->query['table'];
	}

	/**
	 * Compiles the insert values.
	 *
	 * @return  string  compiled values part
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
	 * Compiles a select part.
	 *
	 * @return  string  compiled select part
	 */
	protected function compilePartSelect()
	{
		$columns = $this->query['columns'];
		empty($columns) and $columns = array('*');
		$columns = array_map(array($this, 'quoteIdentifier'), $columns);
		return 'SELECT'.($this->query['distinct'] === true ? ' DISTINCT ' : ' ').trim(join(', ', $columns));
	}

	/**
	 * Compiles a delete part.
	 *
	 * @return  string  compiled delete part
	 */
	protected function compilePartDelete()
	{
		return 'DELETE FROM '.$this->quoteIdentifier($this->query['table']);
	}

	/**
	 * Compiles an update part.
	 *
	 * @return  string  compiled update part
	 */
	protected function compilePartUpdate()
	{
		return 'UPDATE '.$this->quoteIdentifier($this->query['table']);
	}

	/**
	 * Compiles a from part.
	 *
	 * @return  string  compiled from part
	 */
	protected function compilePartFrom()
	{
		$tables = $this->query['table'];
		is_array($tables) or $tables = array($tables);
		return ' FROM '.join(', ', array_map(array($this, 'quoteIdentifier'), $tables));
	}

	/**
	 * Compiles the where part.
	 *
	 * @return  string  compiled where part
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
	 * Compiles the set part.
	 *
	 * @return  string  compiled set part
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
	 * Compiles the having part.
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
	 * Compiles the order by part.
	 *
	 * @return  string  compiled order by part
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
	 * Compiles the join part.
	 *
	 * @return  string  compiled join part
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
	 * Compiles the group by part.
	 *
	 * @return  string  compiler group by part
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
	 * Compiles the limit and offset statement.
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
	 * Escapes a value.
	 *
	 * @param   string  $value  value to escape
	 * @return  string  escaped string
	 */
	public function escape($value)
	{
		return $this->connection->quote($value);
	}
}
