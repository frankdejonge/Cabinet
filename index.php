<?php

use Cabinet\DBAL\Db;
use Cabinet\DBAL\Exception;

class MyObject
{

}

require './vendor/autoload.php';


$conn = Db::connection(array(
	'driver' => 'mysql',
	'username' => 'root',
	'password' => 'root',
	'database' => 'louter',
));

/*
die($conn->select()->from(':table')
			->bind('table', 'my_table')
			->compile());
*/

$query = $conn->schema()
	->table('my_table')
	->create()
	->ifNotExists()
	->indexes(array(function($index){
		$index->on('id')
			->type('primary key');
	}))
	->fields(array(
		'id' => function($field){
			$field->type('int')
				->constraint(11)
				->incremental()
				;
		},
		'fieldname' => function($field){
			$field->type('varchar')
				->constraint(255)
				->null();
		},
		'textarea' => function($field)
		{
			$field->type('text')
				->comments('this is a comment')
				//->defaultValue('donkeyballs are go!')
				->charset('utf8_general_ci');
		}
	))
	->execute();

//print_r($conn->listFields('my_table'));
//print_r($conn->listDatabases());

$conn->query('TRUNCATE TABLE `my_table`', Db::PLAIN)->execute();

$conn->insert('my_table')
	->values(array(
		'fieldname' => 'name',
		'textarea' => 'some content',
	))
	->execute();

print_r($conn->select(Db::fn('count', '*'))->from('my_table')->execute());

$conn->startTransaction();

$conn->insert('my_table')
	->values(array(
		'fieldname' => 'name',
		'textarea' => 'some content',
	))
	->execute();

$conn->commitTransaction();

print_r($conn->select()->from('my_table')->execute());


$conn->schema()
	->table('my_table')
	->drop()
	->ifExists()
	->execute();

/*
$query = $conn->schema()
	->table('my_table')
	->create()
	->indexes(array(function($index){
		$index->on('id')
			->type('primary key');
	}))
	->fields(array(
		'id' => function($field){
			$field->type('int')
				->constraint(11)
				->autoIncrement()
				;
		},
		'fieldname' => function($field){
			$field->type('varchar')
				->constraint(255)
				->null();
		},
		'textarea' => function($field)
		{
			$field->type('text')
				->comments('this is a comment')
				//->defaultValue('donkeyballs are go!')
				->charset('utf8_general_ci');
		}
	))
	->execute();

print_r($conn->lastQuery());


//die($query);

/*
echo($conn->select()->from('table')->join('other_table')
->on('table.field', '=', 'other_table.field')
->orOn('table.field', '=', 'other_table.other_field')
->compile());

$conn2 = Db::connection(array(
	'driver' => 'mysql',
	'username' => 'root',
	'password' => 'root',
	'database' => 'test_database',
));



$conn->schema()
	->createDatabase('unknown_database')
	->charset('utf8_general_ci')
	->execute();

print_r($conn->lastQuery());

$conn->schema()
	->database('unknown_database')
	->drop()
	->execute();

$query = Db::query('SELECT * from `:table`', Db::SELECT, array(
	'table' => 'blocks',
))->asObject(true);

$compiled = $query->execute($conn);

$result = $conn->select(array(Db::fn('max', 'id'), 'max_id'))
	->from('containers')
	->asObject('MyObject')
	->execute();

print_r($result);

echo 1;

$subquery = Db::select('container_id')
	->from('blocks')
	->where('id', 'in', array(1, 2, 3));

$query = Db::select()->from('containers')
	->where('id', 'in', $subquery)
	->bind('id' , '%1%')
	->asObject(true);

// a query can compile with a connection
$compiled = $query->compile($conn);

// and a connection can compile a query
$compiled = $conn->compile($query);

// works the same for executing

//var_dump($compiled);

//var_dump($update);



$result = $conn->select('*')
	->from('blocks')
	->execute();

var_dump($result);

*/

$pg = Db::connection(array(
	'driver' => 'pgsql',
	'username' => 'postgres',
	'password' => 'password',
	'database' => 'mydb',
	'port' => 5432,
));


$pg->startTransaction();
$pg->commitTransaction();

print_r($pg->listTables());
print_r($pg->listDatabases());
print_r($pg->listFields('my_table'));

/*
$pg->delete('my_table')
	//->where('id', '<', 100)
	->execute();

$pg->insert('my_table')
	->values(array(
		'name' => 'John: '.time(),
	))
	->execute();


var_dump($pg->select()
	->where('id', '>', 0)
	->orderBy('id', 'desc')
	->from('my_table')
	->limit(2)
	->offset(2)
	->asObject()
	->execute());

var_dump($pg->lastQuery());
*/

$sqlite = Db::connection(array(
	'driver' => 'sqlite',
	'dsn' => 'sqlite:/Applications/MAMP/db/sqlite/mysqlite',
));

var_dump('------------------');

print_r($sqlite->listTables());
print_r($sqlite->listFields('my_table'));

//var_dump($sqlite->select()->from('my_table')->asObject()->execute());

//print_r($conn->profilerQueries());
