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

namespace Cabinet\DBAL\Connection\Pdo;

use Cabinet\DBAL\Connection\Pdo;
use Cabinet\DBAL\Db;

class Mysql extends Pdo
{
	/**
	 * Get an array of table names from the connection.
	 *
	 * @return  array  tables names
	 */
	public function listTables()
	{
		$query = Db::query('SHOW TABLES', Db::SELECT)->asAssoc();

		$result = $query->execute($this);

		return array_map(function($r){
			return reset($r);
		}, $result);
	}

	/**
	 * Get an array of database names from the connection.
	 *
	 * @return  array  database names
	 */
	public function listDatabases()
	{
		$query = Db::query('SHOW DATABASES', Db::SELECT)->asAssoc();

		$result = $query->execute($this);

		return array_map(function($r){
			return reset($r);
		}, $result);
	}

	/**
	 * Get an array of table fields from a table.
	 *
	 * @return  array  field arrays
	 */
	public function listFields($table)
	{
		$query = Db::query('SHOW FULL COLUMNS FROM '.$this->quoteIdentifier($table), Db::SELECT)->asAssoc();

		$result  = $query->execute($this);

		$return = array();

		foreach ($result as $r)
		{
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

		return $return;
	}

	/**
	 * Start a transaction.
	 *
	 * @return  object  $this;
	 */
	public function startTransaction()
	{
		$this->connection->query('SET AUTOCOMMIT=0');
		$this->connection->query('START TRANSACTION');

		return $this;
	}

	public function commitTransaction()
	{
		$this->connection->query('COMMIT');
		$this->connection->query('SET AUTOCOMMIT=1');

		return $this;
	}

	public function rollbackTransaction()
	{
		$this->connection->query('ROLLBACK');
		$this->connection->query('SET AUTOCOMMIT=1');

		return $this;
	}
}
