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

class Sqlsrv extends Pdo
{
	/**
	 * Sets the connection encoding.
	 *
	 * @param  string  $charset  encoding
	 */
	protected function setCharset($charset)
	{
		$this->connection->setAttribute(\PDO::SQLSRV_ATTR_ENCODING, constant("\PDO::SQLSRV_ENCODING_".strtoupper($charset)));
	}
}
