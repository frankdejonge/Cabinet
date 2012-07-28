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

class Pgsql extends Pdo
{
	/**
	 * @var  string  $tableQuote  table quote
	 */
	protected static $tableQuote = '"';

	/**
	 * Get an array of table names from the connection.
	 *
	 * @return  array  tables names
	 */
	public function listTables()
	{
		$result = Db::query('SELECT table_name FROM information_schema.tables WHERE table_schema = \'public\'', Db::SELECT)
			->asAssoc()
			->execute($this);

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
		$result = Db::query('SELECT datname FROM pg_database', Db::SELECT)
			->asAssoc()
			->execute($this);

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
		$query = Db::query('SELECT * FROM information_schema.columns WHERE table_name = '.$this->quote($table), Db::SELECT)
			->asAssoc();

		$result = $query->execute($this);

		return array_map(function($r){
			return reset($r);
		}, $result);
	}

	/**
	 * Start a transaction.
	 *
	 * @return  object  $this;
	 */
	public function startTransaction()
	{
		$this->connection->query('BEGIN');

		return $this;
	}

	/**
	 * Commit a transaction.
	 *
	 * @return  object  $this;
	 */
	public function commitTransaction()
	{
		$this->connection->query('COMMIT');

		return $this;
	}

	/**
	 * Roll back a transaction.
	 *
	 * @return  object  $this;
	 */
	public function rollbackTransaction()
	{
		$this->connection->query('ROLLBACK');

		return $this;
	}
}
