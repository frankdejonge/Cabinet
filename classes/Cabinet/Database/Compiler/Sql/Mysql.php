<?php

namespace Cabinet\Database\Compiler\Sql;

use Cabinet\Database\Compiler\Sql;

class Mysql extends Sql
{
	protected function compilePartFn($value)
	{
		$fn = strtoupper($value->getFn());
		$quoteFn = ($value->quoteAs() === 'identifier') ? 'quoteIdentifier' : 'quote';

		return $fn.'('.join(', ', array_map(array($this, $quoteFn), $value->getParams())).')';
	}
}