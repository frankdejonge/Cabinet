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

class Mysql extends Pdo
{
	public function listTables()
	{
		$query = Db::query('SHOW TABLES ', Db::SELECT);

		$result = $query->execute($this);

		return array_map(function($r){
			return reset($r);
		}, $result);
	}
	
	public function listDatabases()
	{
		$query = Db::query('SHOW TABLES ', Db::SELECT);

		$result = $query->execute($this);

		return array_map(function($r){
			return reset($r);
		}, $result);
	}
	
	public function listFields($table)
	{
		$query = Db::query('SHOW FULL COLUMNS FROM '.$table, Db::SELECT);
		
		$result  = $query->execute($this);
		
		$return = array();
		
		foreach ($result as $r)
		{
			print_r($r);

			$type = $r['Type'];
			
			if (strpos($type, ' '))
			{
				list($type, $extra) = explode(' ', $type, 2);
			}
			
			if ($pos = strpos($type, '('))
			{
				$field['type'] = substr($type, 0, $pos);
				$field['constraint'] = substr($type, $pos + 1, -1);
			}
			else
			{
				$field['constraint'] = null;
			}
			
			$field['extra'] = isset($extra) ? $extra : null;
			
			$field['name'] = $r['Field'];
			$field['default'] = $r['Default'];
			$field['null'] = $r['Null'] !== 'No';
			$field['privileges'] = explode(',', $r['Privileges']);
			$field['key'] = $r['Key'];
			$field['comments'] = $r['Comment'];
			$field['collation'] = $r['Collation'];
			
			if($r['Extra'] === 'auto_increment')
			{
				$field['auto_increment'] = true;
			}
			else
			{
				$field['auto_increment'] = false;
			}
			
			$return[$field['name']] = $field;
		}
		print_r($return);
		die();
		
		return $return;
	}
}