<?php

use Cabinet\DBAL\Db;

class SelectBuilderTest extends PHPUnit_Framework_TestCase
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
	public function testBuildSelect()
	{
		$expected = "SELECT * FROM `my_table`";

		$query = $this->connection
			->select()->from('my_table')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT with multiple fields
	 *
	 * @test
	 */
	public function testBuildSelectFields()
	{
		$expected = "SELECT `column`, `other` FROM `my_table`";

		$query = $this->connection
			->select('column', 'other')->from('my_table')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT with a field alias
	 *
	 * @test
	 */
	public function testBuildSelectAlias()
	{
		$expected = "SELECT `column` AS `alias`, `other` FROM `my_table`";

		$query = $this->connection
			->select(array('column', 'alias'), 'other')->from('my_table')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT with a function
	 *
	 * @test
	 */
	public function testBuildSelectFn()
	{
		$expected = "SELECT COUNT(*) FROM `my_table`";

		$query = $this->connection
			->select(Db::fn('count', '*'))->from('my_table')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT with an aliased function
	 *
	 * @test
	 */
	public function testBuildSelectFnAlias()
	{
		$expected = "SELECT COUNT(*) AS `alias` FROM `my_table`";

		$query = $this->connection
			->select(array(Db::fn('count', '*'), 'alias'))->from('my_table')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT with a field
	 *
	 * @test
	 */
	public function testBuildSelectExpr()
	{
		$expected = "SELECT expr FROM `my_table`";

		$query = $this->connection
			->select(Db::expr('expr'))->from('my_table')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT with a field
	 *
	 * @test
	 */
	public function testBuildSelectField()
	{
		$expected = "SELECT `column` FROM `my_table`";

		$query = $this->connection
			->select('column')->from('my_table')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT from multiple tables
	 *
	 * @test
	 */
	public function testBuildSelectMultipleTables()
	{
		$expected = "SELECT * FROM `my_table`, `other_table`";

		$query = $this->connection
			->select()->from('my_table', 'other_table')
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
	 * Test Builder SELECT WHERE AND GROUPS
	 *
	 * @test
	 */
	public function testBuildSelectWhereAndGroups()
	{
		$expected = "SELECT * FROM `my_table` WHERE `field` = 'value' AND (`other` != 'other value' OR `field` = 'something') AND (`age` IN (1, 2, 3) OR `age` NOT IN (2, 5, 7))";

		$query = $this->connection
			->select()->from('my_table')
			->where('field', 'value')
			->andWhereOpen()
			->Where('other', '!=', 'other value')
			->orWhere('field', '=', 'something')
			->andWhereClose()
			->andWhere(function($q){
				$q->where('age', 'in', array(1, 2, 3))
					->orWhere('age', 'not in', array(2, 5, 7));
			})
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

	/**
	 * Test Builder SELECT JOIN ON
	 *
	 * @test
	 */
	public function testBuildSelectJoinAnd()
	{
		$expected = "SELECT * FROM `my_table` JOIN `other_table` ON (`my_table`.`field` = `other_table`.`field` AND `my_table`.`other_field` = `other_table`.`other_field`)";

		$query = $this->connection
			->select()->from('my_table')
			->join('other_table')
			->on('my_table.field', '=', 'other_table.field')
			->andOn('my_table.other_field', 'other_table.other_field')
			->compile();

		$this->assertEquals($expected, $query);
	}

	/**
	 * Test Builder SELECT JOIN ON
	 *
	 * @test
	 */
	public function testBuildSelectJoinOr()
	{
		$expected = "SELECT * FROM `my_table` JOIN `other_table` ON (`my_table`.`field` = `other_table`.`field` OR `my_table`.`other_field` = `other_table`.`other_field`)";

		$query = $this->connection
			->select()->from('my_table')
			->join('other_table')
			->on('my_table.field', '=', 'other_table.field')
			->orOn('my_table.other_field', 'other_table.other_field')
			->compile();

		$this->assertEquals($expected, $query);
	}
}
