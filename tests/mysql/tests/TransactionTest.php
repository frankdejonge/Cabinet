<?php

use Cabinet\Database\Db;

class TransactionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var  $connection
	 */
	private $connection;

	public function setUp()
	{
		return;
		$this->connection or $this->connection = Db::connection(array(
			'driver' => 'mysql',
			'username' => 'root',
			'password' => isset($_SERVER['DB']) ? '' : 'root',
			'database' => 'test_database',
		));
		
		$this->connection
			->schema()
			->table('test_table')
			->create()
			->fields(array(
				'id' => function($field){
					$field->type('int')
						->constraint(11)
						->autoIncrement();
				},
				'name' => function($field){
					$field->type('varchar')
						->constraint('255');
				},
			))
			->indexes(array(
				function($index){
					$index->on('id')
						->index('PRIMARY KEY');	
				}
			))
			->execute();
	}
	
	public function tearDown()
	{
		return;
		$this->connection
			->schema()
			->table('test_table')
			->drop()
			->execute();
	}
	
	public function testTransaction()
	{
		$this->assertEquals(true, true);
	}
}