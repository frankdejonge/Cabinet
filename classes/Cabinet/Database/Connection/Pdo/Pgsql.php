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
use Cabinet\Database\Db;

class Pgsql extends Pdo
{
	public function listTables()
	{
		$result = Db::query('SELECT table_name FROM information_schema.tables WHERE table_schema = \'public\'', Db::SELECT)
			->execute($this);
		
		return array_map(function($r){
			return reset($r);
		}, $result);
	}

	public function listDatabases()
	{
		$result = Db::query('SELECT datname FROM pg_database', Db::SELECT)
			->execute($this);
		
		return array_map(function($r){
			return reset($r);
		}, $result);
	}
	
	public function listFields($table)
	{
		$query = Db::query("SELECT * FROM information_schema.columns WHERE table_name ='$table'", Db::SELECT);
		
		$result = $query->execute($this);
		
		return array_map(function($r){
			return reset($r);
		}, $result);
	}
}