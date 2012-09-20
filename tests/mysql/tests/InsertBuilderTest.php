<?php

use Cabinet\DBAL\Db;

class InsertBuilderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var  $connection
	 */
	private $connection;

	/**
	 * Setup, connects to the database
	 */
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
	 * Test Builder SELECT
	 *
	 * @test
	 */
	public function testBuildInsert()
	{
		$expected = "INSERT INTO `my_table` () VALUES ()";

		$query = $this->connection
			->insert('my_table')
			
			->compile();

		$this->assertEquals($expected, $query);
	}
}
