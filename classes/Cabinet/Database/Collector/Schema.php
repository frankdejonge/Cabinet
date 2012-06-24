<?php

namespace Cabinet\Database\Collector;


class Schema
{
	/**
	 * Get a new schema builder object
	 *
	 * @return  object  new database schema builder object
	 */
	public function database($connection, $database)
	{
		return new Schema\Database($connection, $database);
	}

	/**
	 * Get a new table builder object.
	 *
	 * @return  object new table schema builder object
	 */
	public function table($connection, $table)
	{
		return new Schema\Table($connection, $table;
	}
}