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

namespace Cabinet\Database\Collector\Schema;

class Index extends \Cabinet\Query\Collector
{
	/**
	 * @var  string  $index  type of index
	 */
	public $index;

	/**
	 * @var  
	 */
	public $on;

	/**
	 * Constructor, sets type and fields.
	 *
	 * @param   string   $index  index type
	 * @param   
	 *
	 */
	public function __construct($index = null, $on = null)
	{
		$index and $this->index = $index;
		
		if ( ! empty($on))
		{
			is_array($on) or $on = array($on);

			$this->on = $on;
		}
	}

	/**
	 * Set the index type.
	 *
	 * @param   string  $type  index type
	 * @return  object  $this
	 */
	public function type($type)
	{
		$this->index = $type;

		return $this;
	}

	/**
	 * Set the index fields
	 *
	 * @param   string  $field  column name
	 * ......
	 * @return  object  $this
	 */
	public function on($field)
	{
		$this->on = func_get_args();

		return $this;
	}
}