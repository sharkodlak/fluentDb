<?php

namespace Sharkodlak\FluentDb\Query;

class Select extends TableQuery {
	protected $parts = [
		'command' => 'SELECT',
		'columns' => '*',
		'fromClause' => 'FROM',
		'table' => '%s'
	];

	protected function getParts() {
		return $this->parts;
	}
}
