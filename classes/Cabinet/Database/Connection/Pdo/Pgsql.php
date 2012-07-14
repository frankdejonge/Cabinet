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

namespace Cabinet\Database\Connection\Pdo;

use Cabinet\Database\Connection\Pdo;

class Pgsql extends Pdo
{
	public function listTables()
	{
		$query = Db::query('SELECT table_name FROM information_schema.tables ', Db::SELECT);
		
		$result = $query->execute($this);
		
		return $result;
	}
}