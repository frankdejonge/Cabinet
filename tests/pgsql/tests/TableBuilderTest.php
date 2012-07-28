<?php

use Cabinet\DBAL\Db;

class TableBuilderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var  $connection
	 */
	private $connection;

	public function setUp()
	{
		$this->connection = Db::connection(array(
			'driver' => 'pgsql',
			'username' => 'postgres',
			'password' => isset($_SERVER['DB']) ? '' : 'password',
			'database' => 'test_database',
		));
	}

	/**
	 * Test Schema Drop Table
	 *
	 * @test
	 */
	public function testDropTable()
	{
		$expected = "DROP TABLE \"my_table\"";

		$query = $this->connection
			->schema()
			->table('my_table')
			->drop()
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Schema Drop Table
	 *
	 * @test
	 */
	public function testDropTableIfExists()
	{
		$expected = "DROP TABLE IF EXISTS \"my_table\"";

		$query = $this->connection
			->schema()
			->table('my_table')
			->ifExists()
			->drop()
			->compile();

		$this->assertEquals($expected, $query);
	}
}
