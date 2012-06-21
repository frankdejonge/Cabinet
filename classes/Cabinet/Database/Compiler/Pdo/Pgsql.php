<?php

namespace Cabinet\Database\Compiler\Pdo;

use Cabinet\Database\Compiler\Pdo;

class Pgsql extends Pdo
{
	protected static $tableQuote = '"';
}