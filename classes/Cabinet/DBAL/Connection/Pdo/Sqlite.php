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

class Sqlite extends Pdo
{
	/**
	 * Sets the connection encoding.
	 *
	 * @param  string  $charset  encoding
	 */
	protected function setCharset($charset)
	{
		// skip setting the character set
	}

	/**
	 * Formats the dsn if none supplied and returns it.
	 *
	 * @param   array   $config  connection config
	 * @return  string  formatted connection dsn
	 */
	protected function formatDsn($config)
	{
		// check for dsn, if set, return that
		if (isset($config['dsn']) and ! empty($config['dsn']))
		{
			return $config['dsn'];
		}

		// build the dsn
		return $config['driver'].':'.$config['database'];
	}

	public function listTables()
	{
		return array_map(function($i){
			return reset($i);
		}, $this->query("SELECT name FROM sqlite_master WHERE type = 'table'".
			" AND name != 'sqlite_sequence' AND name != 'geometry_columns'"
			." AND name != 'spatial_ref_sys' "
			. "UNION ALL SELECT name FROM sqlite_temp_master "
			. "WHERE type = 'table' ORDER BY name", Db::SELECT)
			->asAssoc()
			->execute());
	}

	public function listFields($table)
	{
		return array_map(function($i){
			$field = array(
				'name' => $i['name'],
			);

			$type = $i['type'];

			if ($pos = strpos($type, '('))
			{
				$field['type'] = substr($type, 0, $pos);
				$field['constraint'] = substr($type, $pos + 1, -1);
			}
			else
			{
				$field['constraint'] = null;
			}

			$field['null'] = ! (bool) $i['notnull'];
			$field['default'] = $i['dflt_value'];
			$field['primary'] = (bool) $i['pk'];

			return $field;
		}, $this->query('Pragma table_info('.$this->quoteIdentifier($table).')', Db::SELECT)
			->asAssoc()
			->execute());
	}

	public function startTransaction()
	{
		$this->connection->query('BEGIN');

		return $this;
	}

	public function commitTransaction()
	{
		$this->connection->query('COMMIT');

		return $this;
	}

	public function rollbackTransaction()
	{
		$this->connection->query('ROLLBACK');

		return $this;
	}
}
