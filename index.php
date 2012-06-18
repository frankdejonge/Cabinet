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
	'table' => 'containers',
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

var_dump($compiled);

$update = Db::update('containers')
	->where('id', 1)
	->set(array(
		'name' => 'New Name',
		'hash' => 'new-name',
	))->compile($conn);

var_dump($update);

$update = Db::delete('containers')
	->where('id', 1)
	->orWhere(function($query){
		$query->where('something', 'between', array(1, 4))
			->where('something_else', 'this');
	})->compile($conn);

var_dump($update);


$query = $conn->select();

var_dump($query);
