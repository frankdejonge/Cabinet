<?php

use Cabinet\DBAL\Db;

class TransactionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var  $connection
	 */
	private $connection;

	public function setUp()
	{
		$this->connection or $this->connection = Db::connection(array(
			'driver' => 'pgsql',
			'username' => 'root',
			'password' => isset($_SERVER['DB']) ? '' : 'root',
			'database' => 'test_database',
		));

		$this->connection->schema()
			->table('test_table')
			->create()
			->ifNotExists()
			->engine('InnoDB')
			->fields(array(
				'id' => function($field){
					$field->incremental()
						->primary();
				},
				'name' => function($field){
					$field->type('varchar')
						->constraint(255)
						->null();
				},
			))
			->execute();
	}

	public function tearDown()
	{
		$this->connection->schema()
			->table('test_table')
			->ifExists()
			->drop()
			->execute();
	}

	/**
	 * Tests transactions commit.
	 *
	 * @test
	 */
	public function testTransactionCommit()
	{
		$expected = array(
			array(
				'id' => 1,
				'name' => 'Bill',
			),
		);

		$this->connection->startTransaction();
		$this->connection->insert('test_table')
			->values(array(
				'name' => 'Bill',
			))
			->execute();
		$this->connection->commitTransaction();

		$result = $this->connection->select()
			->from('test_table')
			->asAssoc()
			->execute();

		$this->assertEquals($expected, $result);
	}

	/**
	 * Tests transaction rollbacks.
	 *
	 * @test
	 */
	public function testTransactionRollback()
	{
		$expected = array();

		$this->connection->startTransaction();
		$this->connection->insert('test_table')
			->values(array(
				'name' => 'Bill',
			))
			->execute();
		$this->connection->rollbackTransaction();

		$result = $this->connection->select()
			->from('test_table')
			->asAssoc()
			->execute();

		$this->assertEquals($expected, $result);
	}

	/**
	 * Tests transactions.
	 *
	 * @test
	 */
	public function testTransactionSavepoint()
	{
		$expected = array(
			array(
				'id' => 1,
				'name' => 'Bill',
			)
		);

		$this->connection->query('TRUNCATE TABLE test_table', Db::PLAIN)->execute();

		$this->connection->startTransaction();
		$this->connection->insert('test_table')
			->values(array(
				'name' => 'Bill',
			))
			->execute();
		$this->connection->setSavepoint('my_savepoint');
		$this->connection->insert('test_table')
			->values(array(
				'name' => 'Jim',
			))
			->execute();
		$this->connection->rollbackSavepoint('my_savepoint');
		$this->connection->commitTransaction();

		$result = $this->connection->select()
			->from('test_table')
			->asAssoc()
			->execute();

		$this->assertEquals($expected, $result);
	}
}
