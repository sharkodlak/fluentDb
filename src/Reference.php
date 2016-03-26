<?php

namespace Sharkodlak\FluentDb;

class Reference implements \ArrayAccess {
	private $row;
	private $rowVia;
	private $table;

	public function __construct(Row $row, Table $table) {
		$this->row = $row;
		$this->table = $table;
	}

	public function offsetExists($offset) {
		return array_key_exists($offset, $this->getRowVia());
	}

	public function offsetGet($offset) {
		return $this->getRowVia()[$offset];
	}

	public function offsetSet($offset, $value) {
		$this->getRowVia()[$offset] = $value;
	}

	public function offsetUnset($offset) {
		unset($this->getRowVia()[$offset]);
	}

	private function getRowVia() {
		if ($this->rowVia === null) {
			$this->rowVia = $this->via();
		}
		return $this->rowVia;
	}

	public function backwards($tableColumnName = null, $rowColumnName = null) {
		$tableColumnName = $this->getTableForeignColumnName($tableColumnName);
		$rowColumnName = $this->getRowPrimaryColumnName($rowColumnName);
		$rowQuery = clone $this->row->getQuery();
		$rowQueryBuilder = $rowQuery->getBuilder();
		$rowQueryBuilder['SELECT'] = ':id';
		// SELECT * FROM table WHERE :table_id IN (SELECT :id FROM row_table)
		$this->table->where('%s IN (%s)', $tableColumnName, $rowQuery);
		$rowColumnValue = $this->row[$rowColumnName];
		$rows = $this->table->getRows();
		$filter = function($row) use($rowColumnName, $rowColumnValue) {
			return isset($row[$rowColumnName]) && $row[$rowColumnName] === $rowColumnValue;
		};
		return array_filter($rows, $filter);
	}

	public function getTableForeignColumnName($name) {
		if ($name === null) {
			$tableName = $this->row->getTableName();
			return $this->table->getForeignKey($tableName);
		}
		return $name;
	}

	public function getRowPrimaryColumnName($name) {
		if ($name === null || $name === ':id') {
			return $this->row->getPrimaryKey();
		}
		return $name;
	}

	public function via($rowColumnName = null, $tableColumnName = null) {
		$rowColumnName = $this->getRowForeignColumnName($rowColumnName);
		$tableColumnName = $this->getTablePrimaryColumnName($tableColumnName);
		$rowQuery = clone $this->row->getQuery();
		$rowQueryBuilder = $rowQuery->getBuilder();
		$rowQueryBuilder['SELECT'] = ':id';
		// SELECT * FROM table WHERE :id IN (SELECT :id FROM row_table)
		$this->table->where(':id IN (%s)', $rowQuery);
		$rowColumnValue = $this->row[$rowColumnName];
		return $this->table[$rowColumnValue];
	}

	public function getRowForeignColumnName($name) {
		if ($name === null) {
			$tableName = $this->table->getName();
			return $this->row->getForeignKey($tableName);
		}
		return $name;
	}

	public function getTablePrimaryColumnName($name) {
		if ($name === null || $name === ':id') {
			return $this->table->getPrimaryKey();
		}
		return $name;
	}
}
