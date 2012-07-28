<?php

use Cabinet\DBAL\Db;

class UpdateBuilderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var  $connection
	 */
	private $connection;

	public function setUp()
	{
		$this->connection = Db::connection(array(
			'driver' => 'pgsql',
			'username' => 'root',
			'password' => isset($_SERVER['DB']) ? '' : 'root',
			'database' => 'test_database',
		));
	}

	/**
	 * Test Builder UPDATE
	 *
	 * @test
	 */
	public function testBuildSimple()
	{
		$expected = "UPDATE \"my_table\" SET \"field\" = 'value'";

		$query = $this->connection
			->update('my_table')
			->set(array(
				'field' => 'value',
			))
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder UPDATE
	 *
	 * @test
	 */
	public function testBuildMultiple()
	{
		$expected = "UPDATE \"my_table\" SET \"field\" = 'value', \"another_field\" = '1'";

		$query = $this->connection
			->update('my_table')
			->set(array(
				'field' => 'value',
				'another_field' => true,
			))
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder UPDATE WHERE
	 *
	 * @test
	 */
	public function testBuildWhere()
	{
		$expected = "UPDATE \"my_table\" SET \"field\" = 'value' WHERE \"field\" = 'other value'";

		$query = $this->connection
			->update('my_table')
			->set(array(
				'field' => 'value',
			))
			->where('field', 'other value')
			->compile();

		$this->assertEquals($expected, $query);
	}
}
