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

use Cabinet\DBAL\Db;

class Delete extends Where
{
	protected $type = Db::DELETE;

	public function __construct($table = null)
	{
		$table and $this->table = $table;
	}

	/**
	 * Sets the table to update
	 *
	 * @param   string  $table  table to update
	 * @return  object  $this
	 */
	public function from($table)
	{
		$this->table = $table;

		return $this;
	}
}
