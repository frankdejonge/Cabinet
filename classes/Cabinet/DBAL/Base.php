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

namespace Cabinet\DBAL;

abstract class Base
{
    /**
     * @var  string  $asOjbect  true for stCLass or string classname
     */
    protected $asObject = null;

    /**
     * @var boolean $propsLate true for assigning properties after object creation
     */
    protected $propsLate = false;

    /**
     * @var  array  $bindings  query bindings
     */
    protected $bindings = array();

    /**
     * @var  string  $type  query type
     */
    protected $type;

    /**
     * @var  object  $connection  connection object
     */
    protected $_connection;

    /**
     * Bind a value to the query.
     *
     * @param mixed $key   binding key or associative array of bindings
     * @param mixed $value binding value
     */
    public function bind($key, $value = null)
    {
        is_array($key) or $key = array($key => $value);

        foreach ($key as $k => $v) {
            $this->bindings[$k] = $v;
        }

        return $this;
    }

    /**
     * Get the query value.
     *
     * @param  object $connection database connection object
     * @return object $this
     */
    public function setConnection(Connection $connection)
    {
        if (! $connection instanceof Connection) {
            throw new Exception('Supplied invalid connection object');
        }

        $this->_connection = $connection;

        return $this;
    }

    /**
     * Get the connection object.
     *
     * @return object                 connection object
     * @throws Cabinet\DBAL\Exception when no connection object is set.
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Get the query value.
     *
     * @return mixed query contents
     */
    abstract public function getContents();

    /**
     * Returns the query's bindings.
     *
     * @return array query bindings
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Returns the query type.
     *
     * @return array query bindings
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the return type for SELECT statements
     *
     * @param         $object null for connection default, false for array, true for stdClass or string classname
     * @return object $this;
     */
    public function asObject($object = true)
    {
        $this->asObject = $object;

        return $this;
    }

    /**
     * When return type is classname u can assign properties late
     *
     * @param  boolean $late false, true to assign properties late
     * @return object  $this;
     */
    public function propsLate($late = false)
    {
        $this->propsLate = $late;
    }

    /**
     * Assign propslate
     *
     * @return boolean
     */
    public function getPropsLate()
    {
        return $this->propsLate;
    }

    /**
     * Sets the return type to array
     *
     * @return object $this;
     */
    public function asAssoc()
    {
        $this->asObject = false;

        return $this;
    }

    /**
     * Returns wether to get as array or object
     *
     * @return mixed null for array, true for stdClass or string for classname
     */
    public function getAsObject()
    {
        return $this->asObject;
    }

    /**
     * Executes the query on a given connection.
     *
     * @param  object $connection Cabinet\DBAL\Connection
     * @return mixed  Query result.
     */
    public function execute($connection = null)
    {
        $connection or $connection = $this->getConnection();

        if (! $connection) {
            throw new Exception('Cannot execute a query without a valid connection');
        }

        return $connection->execute($this);
    }

    /**
     * Compiles the query on a given connection.
     *
     * @param  object $connection Cabinet\DBAL\Connection
     * @return mixed  compiled query
     */
    public function compile($connection = null)
    {
        $connection or $connection = $this->getConnection();

        if (! $connection) {
            throw new Exception('Cannot compile a query without a valid connection');
        }

        return $connection->compile($this, $this->getType());
    }
}
