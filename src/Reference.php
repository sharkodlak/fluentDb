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
		$rowQueryBuilder = $rowQuery->getBuilder();
		$rowQueryBuilder['SELECT'] = ':id';
		// SELECT * FROM table WHERE :id IN (SELECT :id FROM row_table)
		$this->table->where(':id IN (%s)', $rowQuery);
		$rowColumnValue = $this->row[$rowColumnName];
		return $this->table[$rowColumnValue];
	}

	public function getRowColumnName($name) {
		if ($name === null) {
			$tableName = $this->table->getName();
			return $this->row->getForeignKey($tableName);
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
