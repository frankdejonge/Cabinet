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

use Cabinet\Database\Db;
use Cabinet\Database\Collector;

class Database extends Collector
{
	/**
	 * @var  bool  $database  database name
	 */
	public $database;

	/**
	 * @var  bool  $ifExists  wether to use IF EXISTS
	 */
	public $ifExists = false;

	/**
	 * @var  bool  $ifNotExists  wether to use IF NOT EXISTS
	 */
	public $ifNotExists = false;

	/**
	 * @var  bool  $charset  database charset
	 */
	public $charset;

	/**
	 * @var  bool  $charsetIsDefault  wether the charset/collate are default
	 */
	public $charsetIsDefault = false;

	/**
	 * Constructor, sets the database name 
	 *
	 * @param  string  $database  database
	 */
	public function __construct($database)
	{
		$this->database = $database;
	}

	/**
	 * Sets the IF EXISTS CLAUSE
	 *
	 * @param   boolean  $useExists  wether to use IF EXISTS
	 */
	public function ifExists($useExists = true)
	{
		$this->ifExists = $useExists;

		return $this;
	}

	/**
	 * Sets the IF NOT EXISTS CLAUSE
	 *
	 * @param   boolean  $useExists  wether to use IF EXISTS
	 */
	public function ifNotExists($useNotExists = true)
	{
		$this->ifNotExists = $useNotExists;

		return $this;
	}

	/**
	 * Sets the charset.
	 *
	 * @param   string   $charset    database charset
	 * @param   boolean  $isDefault  wether the charset/collate are default
	 * @return  object   $this
	 */
	public function charset($charset, $isDefault = null)
	{
		$this->charset = $charset;

		is_bool($isDefault) and $this->charsetIsDefault = $isDefault; 

		return $this;
	}

	/**
	 * Sets the query type for drop database
	 *
	 * @return  object  $this
	 */
	public function drop()
	{
		$this->type = Db::DATABASE_DROP;

		return $this;
	}

	/**
	 * Sets the query type for drop database
	 *
	 * @return  object  $this
	 */
	public function create()
	{
		$this->type = Db::DATABASE_CREATE;

		return $this;
	}

	/**
	 * Sets the query type for drop database
	 *
	 * @return  object  $this
	 */
	public function alter()
	{
		$this->type = Db::DATABASE_ALTER;

		return $this;
	}
}