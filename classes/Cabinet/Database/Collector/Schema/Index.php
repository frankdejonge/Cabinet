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

class Index extends \Cabinet\Database\Collector
{
	/**
	 * @var  string  $index  type of index
	 */
	public $index;

	/**
	 * @var  array  $on  index fields
	 */
	public $on;

	/**
	 * @var  string  $name  index name
	 */
	public $name;

	/**
	 * Constructor, sets type and fields.
	 *
	 * @param   string   $name  index name
	 */
	public function __construct($name = null)
	{
		$name and $this->name = $name;
	}

	/**
	 * Set the index name.
	 *
	 * @param   string  $name  index name
	 * @return  object  $this
	 */
	public function name($name)
	{
		$this->name = $name;

		return $this;
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