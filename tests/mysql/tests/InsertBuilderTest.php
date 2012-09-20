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

	/**
	 * Test Builder SELECT
	 *
	 * @test
	 */
	public function testBuildInsertWithValues()
	{
		$expected = "INSERT INTO `my_table` (`id`, `name`) VALUES (1, 'Frank')";

		$query = $this->connection
			->insert('my_table')
			->values(array(
				'id' => 1,
				'name' => 'Frank',
			))
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT
	 *
	 * @test
	 */
	public function testBuildInsertWithFunction()
	{
		$expected = "INSERT INTO `my_table` (`id`, `time`) VALUES (1, NOW())";

		$query = $this->connection
			->insert('my_table')
			->values(array(
				'id' => 1,
				'time' => $this->connection->fn('now'),
			))
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT
	 *
	 * @test
	 */
	public function testBuildInsertWithExpression()
	{
		$expected = "INSERT INTO `my_table` (`id`, `expression`) VALUES (1, 'value')";

		$query = $this->connection
			->insert('my_table')
			->values(array(
				'id' => 1,
				'expression' => $this->connection->expr("'value'"),
			))
			->compile();

		$this->assertEquals($expected, $query);
	}
}
