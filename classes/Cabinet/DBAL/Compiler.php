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


abstract class Compiler
{
	/**
	 * @var  object  $connection  Cabinet connection object.
	 */
	protected $connection = null;

	/**
	 * @var  array  $query  query commands
	 */
	protected $query = array();

	/**
	 * Compiler constructor.
	 *
	 * @param  object  $connection   Cabinet connection object.
	 */
	public function __construct(&$connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Compiles the query.
	 *
	 * @param   object  $query  query object
	 */
	public function compile($query, $type = null, $bindings = array())
	{
		// ensure an instance of Quer\Base
		if ( ! ($query instanceof Query\Base))
		{
			$query = new Query($query, $type, $bindings);
		}

		// get the query contents
		$contents = $query->getContents();

		// merge the bindings
		$queryBindings = $query->getBindings();
		$bindings = $queryBindings + $bindings;

		// process the bindings
		$contents = $this->processBindings($contents, $bindings);

		// returns when it is a raw string
		if (is_string($contents))
		{
			return $contents;
		}

		/**
		 * Since we can compile subqueries, store the old query
		 * and set the new one.
		 */
		$oldQuery = $this->query;
		$this->query = $contents;

		// Compile the query according to it's type.
		$result = $this->{'compile'.$type}();

		// Set back the old query
		$this->query = $oldQuery;

		return is_string($result) ? trim($result) : $result;
	}

	/**
	 * Processes all the query bindings recursively.
	 *
	 * @param  mixes  $contents  query contents
	 * @bindings  array  $bindings  an array of query bindings.
	 */
	protected function processBindings($contents, $bindings, $first = true)
	{
		if ($first and empty($bindings))
		{
			return $contents;
		}

		if (is_array($contents))
		{
			foreach($contents as $i => &$v)
			{
				$contents[$i] = $this->processBindings($v, $bindings, false);
			}
		}
		elseif (is_string($contents))
		{
			foreach($bindings as $from => $to)
			{
				substr($from, 0, 1) !== ':' and $from = ':'.$from;
				$contents = preg_replace('/'.$from.'/', $to, $contents);
			}
		}

		return $contents;
	}
	
	protected function quote($value)
	{
		return $this->connection->quote($value);
	}
	
	protected function quoteIdentifier($value)
	{
		return $this->connection->quoteIdentifier($value);
	}
}