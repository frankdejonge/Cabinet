<?php

use Cabinet\Database\Db;

require 'vendor/autoload.php';

$conn = Db::connection(array(
	'driver' => 'mysql',
	'username' => 'root',
	'password' => 'password',
	'database' => 'louter',
));

$query = Db::query('SELECT * from `:table`', Db::SELECT, array(
	'table' => 'blocks',
))->asObject(true);

$compiled = $query->execute($conn);

//print_r($compiled);

$subquery = Db::select('container_id')
	->from('blocks')
	->where('id', 'in', array(1, 2, 3));

$query = Db::select()->from('containers')
	->where('id', 'in', $subquery)
	->bind('id' , '%1%')
	->asObject(true);

$compiled = $query->compile($conn);
$compiled = $conn->compile($query);

//var_dump($compiled);

$update = Db::update('containers')
	->where('id', 1)
	->set(array(
		'name' => 'New Name',
		'hash' => 'new-name',
	))->compile($conn);

//var_dump($update);

$select = Db::select('containers')
	->where('id', 1)
	->compile($conn);

//var_dump($select);


$result = $conn->select('*')
	->from('blocks')
	->execute();

//var_dump($result);

$pg = Db::connection(array(
	'driver' => 'pgsql',
	'username' => 'postgres',
	'password' => 'password',
	'database' => 'mydb',
	'port' => 5432,
));

//$pg->delete('my_table')->execute();

$pg->insert('my_table')
	->values(array(
		'name' => 'John: '.time(),
	))
	->execute();


var_dump($pg->select()
	->where('id', '>', 0)
	->from('my_table')
	->limit(2)
	->offset(2)
	->asObject()
	->execute());

var_dump($pg->lastQuery());

