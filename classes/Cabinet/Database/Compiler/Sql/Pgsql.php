<?php

namespace Cabinet\Database\Compiler\Sql;

use Cabinet\Database\Compiler\Sql;

class Pgsql extends Sql
{
	protected static $tableQuote = '"';
}