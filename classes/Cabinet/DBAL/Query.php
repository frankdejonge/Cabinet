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

class Query extends Base
{
	/**
	 * @var  mixed  raw query (string for sql, array for NoSQL)
	 */
	protected $query;

	/**
	 * Constructor, sets the query, type and bindings
	 *
	 * @param  mixed   raw query
	 * @param  string  query type
	 * @param  array   query bindings
	 */
	public function __construct($query, $type, $bindings = array())
	{
		$this->query = $query;
		$this->type = $type;
		$this->bindings = $bindings;
	}

	/**
	 * Get the query value.
	 *
	 * @return  mixed  query contents
	 */
	public function getContents()
	{
		return $this->query;
	}
}
