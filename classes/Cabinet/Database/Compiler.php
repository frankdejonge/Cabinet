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
		
		$old_query = $this->query;
		$this->query = $contents;
		
		$result = $this->{'compile'.$type}();
		
		$this->query = $old_query;

		return $result;
	}

	/**
	 * Processes all the query bindings recursively.
	 *
	 * @param  mixes  $contents  query contents
	 * @
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
}