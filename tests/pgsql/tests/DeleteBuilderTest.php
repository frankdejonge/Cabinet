<?php

use Cabinet\DBAL\Db;

class DeleteBuilderTest extends PHPUnit_Framework_TestCase
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
	 * Test Builder DELETE
	 *
	 * @test
	 */
	public function testBuildDelete()
	{
		$expected = "DELETE FROM \"my_table\"";

		$query = $this->connection
			->delete()->from('my_table')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder DELETE WHERE
	 *
	 * @test
	 */
	public function testBuildDeleteWhere()
	{
		$expected = "DELETE FROM \"my_table\" WHERE \"field\" = 'value'";

		$query = $this->connection
			->delete()->from('my_table')
			->where('field', 'value')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder DELETE WHERE NULL
	 *
	 * @test
	 */
	public function testBuildDeleteWhereNull()
	{
		$expected = "DELETE FROM \"my_table\" WHERE \"field\" IS NULL";

		$query = $this->connection
			->delete()->from('my_table')
			->where('field', null)
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder DELETE WHERE NOT NULL
	 *
	 * @test
	 */
	public function testBuildDeleteWhereNotNull()
	{
		$expected = "DELETE FROM \"my_table\" WHERE \"field\" IS NOT NULL";

		$query = $this->connection
			->delete()->from('my_table')
			->where('field', '!=', null)
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder DELETE WHERE OR
	 *
	 * @test
	 */
	public function testBuildDeleteWhereOr()
	{
		$expected = "DELETE FROM \"my_table\" WHERE \"field\" = 'value' OR \"other\" != 'other value'";

		$query = $this->connection
			->delete()->from('my_table')
			->where('field', 'value')
			->orWhere('other', '!=', 'other value')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder DELETE WHERE AND
	 *
	 * @test
	 */
	public function testBuildDeleteWhereAnd()
	{
		$expected = "DELETE FROM \"my_table\" WHERE \"field\" = 'value' AND \"other\" != 'other value'";

		$query = $this->connection
			->delete()->from('my_table')
			->where('field', 'value')
			->andWhere('other', '!=', 'other value')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder DELETE WHERE AND GROUP
	 *
	 * @test
	 */
	public function testBuildDeleteWhereAndGroup()
	{
		$expected = "DELETE FROM \"my_table\" WHERE \"field\" = 'value' AND (\"other\" != 'other value' OR \"field\" = 'something')";

		$query = $this->connection
			->delete()->from('my_table')
			->where('field', 'value')
			->andWhereOpen()
			->Where('other', '!=', 'other value')
			->orWhere('field', '=', 'something')
			->andWhereClose()
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder DELETE WHERE IN
	 *
	 * @test
	 */
	public function testBuildDeleteWhereIn()
	{
		$expected = "DELETE FROM \"my_table\" WHERE \"field\" IN (1, 2, 3)";

		$query = $this->connection
			->delete()->from('my_table')
			->where('field', array(1, 2, 3))
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder DELETE WHERE NOT IN
	 *
	 * @test
	 */
	public function testBuildDeleteWhereNotIn()
	{
		$expected = "DELETE FROM \"my_table\" WHERE \"field\" NOT IN (1, 2, 3)";

		$query = $this->connection
			->delete()->from('my_table')
			->where('field', 'not in', array(1, 2, 3))
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder DELETE WHERE IN
	 *
	 * @test
	 */
	public function testBuildDeleteWhereFn()
	{
		$expected = "DELETE FROM \"my_table\" WHERE CHAR_LENGTH(\"field\") > 2 AND CHAR_LENGTH(\"field\") < 20";

		$query = $this->connection
			->delete()->from('my_table')
			->where(Db::fn('char_length', 'field'), '>', 2)
			->where('CHAR_LENGTH("field")', '<', 20)
			->compile();

		$this->assertEquals($expected, $query);
	}
}
