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

class Fn
{
	/**
	 * @var  array  $params  function params
	 */
	protected $params = array();

	/**
	 * @var  string  $fn  function name
	 */
	protected $fn;

	/**
	 * @var  string  $quoteAs  quote as value or as identifier
	 */
	protected $quoteAs = 'identifier';

	/**
	 * Constructor, stores function name and ensures $params is an array.
	 *
	 * @param  string  $fn      function name
	 * @param  mixed   $params  function params
	 */
	public function __construct($fn, $params = array())
	{
		is_array($params) or $params = array($params);

		$this->fn = $fn;
		$this->params = $params;
	}

	/**
	 * Sets the default quote type to value.
	 *
	 * @return  object  $this
	 */
	public function quoteAsValue()
	{
		$this->quoteAs = 'value';

		return $this;
	}

	/**
	 * Sets the default quote type to identifier.
	 *
	 * @return  object  $this
	 */
	public function quoteAsIdentifier()
	{
		$this->quoteAs = 'identifier';

		return $this;
	}

	/**
	 * Returns default the quoting type.
	 *
	 * @return  string  quoteation type
	 */
	public function quoteAs()
	{
		return $this->quoteAs;
	}

	/**
	 * Retrieve the function name.
	 *
	 * @return  string  function name
	 */
	public function getFn()
	{
		return $this->fn;
	}

	/**
	 * Retrieve the function params.
	 *
	 * @return  array  function params
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * Wrap the function in an alias.
	 *
	 * @param   string  alias identifier
	 * @return  array   alias array
	 */
	public function aliasTo($name)
	{
		return array($this, $name);
	}
}