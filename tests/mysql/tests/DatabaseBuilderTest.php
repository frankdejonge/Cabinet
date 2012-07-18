<?php

use Cabinet\DBAL\Db;

class DatabaseBuilderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var  $connection
	 */
	private $connection;

	public function setUp()
	{
		$this->connection = Db::connection(array(
			'driver' => 'mysql',
			'username' => 'root',
			'password' => isset($_SERVER['DB']) ? '' : 'root',
			'database' => 'test_database',
		));
	}

	/**
	 * Test Schema Drop Database
	 *
	 * @test
	 */
	public function testDropDatabase()
	{
		$expected = "DROP DATABASE `my_database`";

		$query = $this->connection
			->schema()
			->database('my_database')
			->drop()
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Schema Create Database
	 *
	 * @test
	 */
	public function testCreateDatabase()
	{
		$expected = "CREATE DATABASE `my_database`";

		$query = $this->connection
			->schema()
			->database('my_database')
			->create()
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Schema Create Database
	 *
	 * @test
	 */
	public function testCreateDatabaseIfNotExists()
	{
		$expected = "CREATE DATABASE IF NOT EXISTS `my_database`";

		$query = $this->connection
			->schema()
			->database('my_database')
			->create()
			->ifNotExists()
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Schema Create Database with Charset
	 *
	 * @test
	 */
	public function testCreateDatabaseCharset()
	{
		$expected = "CREATE DATABASE `my_database` CHARACTER SET utf8 COLLATE utf8_general_ci";

		$query = $this->connection
			->schema()
			->database('my_database')
			->create()
			->charset('utf8_general_ci')
			->compile();

		$this->assertEquals($expected, $query);
	}


	/**
	 * Test Schema Create Database with Charset as Default
	 *
	 * @test
	 */
	public function testCreateDatabaseCharsetDefault()
	{
		$expected = "CREATE DATABASE `my_database` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";

		$query = $this->connection
			->schema()
			->database('my_database')
			->create()
			->charset('utf8_general_ci', true)
			->compile();

		$this->assertEquals($expected, $query);
	}
}
