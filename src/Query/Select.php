<?php

namespace Sharkodlak\FluentDb\Query;

class Select extends TableQuery {
	public function __construct(\Sharkodlak\FluentDb\Table $table) {
		parent::__construct($table);
		$this->parts = [
			'command' => 'SELECT',
			'columns' => '*',
			'fromClause' => 'FROM',
			'table' => ':table'
	   ];
	}
}
