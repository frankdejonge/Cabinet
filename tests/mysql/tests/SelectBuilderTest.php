<?php

use Cabinet\Database\Db;

class SelectBuilderTest extends PHPUnit_Framework_TestCase
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
	 * Test Builder SELECT
	 *
	 * @test
	 */
	public function testBuildSelect()
	{
		$expected = "SELECT * FROM `my_table`";

		$query = $this->connection
			->select()->from('my_table')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT WHERE
	 *
	 * @test
	 */
	public function testBuildSelectWhere()
	{
		$expected = "SELECT * FROM `my_table` WHERE `field` = 'value'";

		$query = $this->connection
			->select()->from('my_table')
			->where('field', 'value')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT WHERE NULL
	 *
	 * @test
	 */
	public function testBuildSelectWhereNull()
	{
		$expected = "SELECT * FROM `my_table` WHERE `field` IS NULL";

		$query = $this->connection
			->select()->from('my_table')
			->where('field', null)
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT WHERE NOT NULL
	 *
	 * @test
	 */
	public function testBuildSelectWhereNotNull()
	{
		$expected = "SELECT * FROM `my_table` WHERE `field` IS NOT NULL";

		$query = $this->connection
			->select()->from('my_table')
			->where('field', '!=', null)
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT WHERE OR
	 *
	 * @test
	 */
	public function testBuildSelectWhereOr()
	{
		$expected = "SELECT * FROM `my_table` WHERE `field` = 'value' OR `other` != 'other value'";

		$query = $this->connection
			->select()->from('my_table')
			->where('field', 'value')
			->orWhere('other', '!=', 'other value')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT WHERE AND
	 *
	 * @test
	 */
	public function testBuildSelectWhereAnd()
	{
		$expected = "SELECT * FROM `my_table` WHERE `field` = 'value' AND `other` != 'other value'";

		$query = $this->connection
			->select()->from('my_table')
			->where('field', 'value')
			->andWhere('other', '!=', 'other value')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT WHERE AND GROUP
	 *
	 * @test
	 */
	public function testBuildSelectWhereAndGroup()
	{
		$expected = "SELECT * FROM `my_table` WHERE `field` = 'value' AND (`other` != 'other value' OR `field` = 'something')";

		$query = $this->connection
			->select()->from('my_table')
			->where('field', 'value')
			->andWhereOpen()
			->Where('other', '!=', 'other value')
			->orWhere('field', '=', 'something')
			->andWhereClose()
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT WHERE IN
	 *
	 * @test
	 */
	public function testBuildSelectWhereIn()
	{
		$expected = "SELECT * FROM `my_table` WHERE `field` IN (1, 2, 3)";

		$query = $this->connection
			->select()->from('my_table')
			->where('field', array(1, 2, 3))
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT WHERE NOT IN
	 *
	 * @test
	 */
	public function testBuildSelectWhereNotIn()
	{
		$expected = "SELECT * FROM `my_table` WHERE `field` NOT IN (1, 2, 3)";

		$query = $this->connection
			->select()->from('my_table')
			->where('field', 'not in', array(1, 2, 3))
			->compile();

		$this->assertEquals($expected, $query);
	}
	
	/**
	 * Test Builder SELECT WHERE IN
	 *
	 * @test
	 */
	public function testBuildSelectWhereFn()
	{
		$expected = "SELECT * FROM `my_table` WHERE CHAR_LENGTH(`field`) > 2 AND CHAR_LENGTH(`field`) < 20";

		$query = $this->connection
			->select()->from('my_table')
			->where(Db::fn('char_length', 'field'), '>', 2)
			->where('CHAR_LENGTH("field")', '<', 20)
			->compile();

		$this->assertEquals($expected, $query);
	}
	
	/**
	 * Test Builder SELECT JOIN
	 *
	 * @test
	 */
	public function testBuildSelectJoin()
	{
		$expected = "SELECT * FROM `my_table` JOIN `other_table` ON (`my_table`.`field` = `other_table`.`field`)";

		$query = $this->connection
			->select()->from('my_table')
			->join('other_table')
			->on('my_table.field', '=', 'other_table.field')
			->compile();

		$this->assertEquals($expected, $query);
	}
}