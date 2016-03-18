<?php

namespace Sharkodlak\FluentDb;

class Reference {
	private $row;
	private $table;

	public function __construct(Row $row, Table $table) {
		$this->row = $row;
		$this->table = $table;
	}

	public function via($rowColumnName = null, $tableColumnName = null) {

	}

	public function getRowColumnName($name) {
		if ($name === null || $name === ':id') {
			return $this->row->getPrimaryKey();
		}
		return $name;
	}

	public function getTableColumnName($name) {
		if ($name === null || $name === ':id') {
			return $this->table->getPrimaryKey();
		}
		return $name;
	}
}
