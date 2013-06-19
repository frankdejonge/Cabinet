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
			'driver' => 'mysql',
			'username' => 'root',
			'password' => isset($_SERVER['DB']) ? '' : 'root',
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
		$expected = "DROP TABLE `my_table`";

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
		$expected = "DROP TABLE IF EXISTS `my_table`";

		$query = $this->connection
			->schema()
			->table('my_table')
			->ifExists()
			->drop()
			->compile();

		$this->assertEquals($expected, $query);
	}

    public function testCreateTableWithForeignKeys()
    {
        $expected = 'CREATE TABLE IF NOT EXISTS `users` ( `id` INT(11) NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL, `group_id` INT(11) NOT NULL, PRIMARY KEY `id` (`id`), CONTSTRAINT `FK_users_groups` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE )  ENGINE = InnoDB CHARACTER SET utf8';

        $query = $this->connection
            ->schema()
            ->table('users')
            ->create()
            ->ifNotExists()
            ->engine('InnoDB')
            ->charset('utf8')
            ->indexes(array(function($index){
                $index->on('id')
                    ->type('primary key');
            }))
            ->fields(array(
                'id' => function($field){
                    $field->type('int')
                        ->constraint(11)
                        ->incremental();
                },
                'name' => function($field){
                    $field->type('varchar')
                        ->constraint(255);
                },
                'group_id' => function($field){
                    $field->type('int')
                        ->constraint(11)
                        ->nullable(false);
                }
            ))
            ->foreignKeys('group_id', function($key){
                $key
                    ->constraint('FK_users_groups')
                    ->references('groups', 'id')
                    ->onUpdate('NO ACTION')
                    ->onDelete('CASCADE');
            })
            ->compile();

       $this->assertEquals($expected, $query);
    }
}
