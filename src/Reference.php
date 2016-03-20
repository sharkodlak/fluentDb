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
		$rowColumnName = $this->getRowColumnName($rowColumnName);
		$tableColumnName = $this->getTableColumnName($tableColumnName);
		$rowQuery = clone $this->row->getQuery();
		$rowQuery->getBuilder()['SELECT'] = ':id';
		var_dump((string) $rowQuery);
		// SELECT * FROM table WHERE :id IN (SELECT :id FROM row_table)
		$query = $this->table->where(':id IN (%s)', $rowQuery);
		var_dump((string) $query);
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
