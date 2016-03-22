<?php

namespace Sharkodlak\FluentDb;

class Table implements \ArrayAccess, \Iterator {
	private $db;
	private $isColumnUsageReportingEnabled;
	private $name;
	private $primaryKey;
	private $query;
	private $rows;
	private $usedColumns;

	public function __construct(Db $db, $name) {
		$this->db = $db;
		$this->name = $name;
		$this->primaryKey = $db->getConventionPrimaryKey($name);
		$this->usedColumns = $db->getTableColumns($name) ?: [];
		$this->isColumnUsageReportingEnabled = empty($this->usedColumns);
	}

	public function __destruct() {
		if ($this->isColumnUsageReportingEnabled && !empty($this->usedColumns)) {
			$this->db->saveTableColumns($this->name, $this->usedColumns);
		}
	}

	public function offsetExists($offset) {
		return array_key_exists($offset, $this->getRows());
	}

	public function offsetGet($offset) {
		return $this->getRows()[$offset];
	}

	public function getRows() {
		if ($this->rows === null) {
			$this->rows = [];
			foreach ($this as $primaryKey => $row) {
				$this->rows[$primaryKey] = $row;
			}
		}
		return $this->rows;
	}

	public function offsetSet($offset, $value) {
		$this->rows[$offset] = $value;
	}

	public function offsetUnset($offset) {
		unset($this->rows[$offset]);
	}

	public function current() {
		$factory = $this->getFactory();
		return $factory->getRow($this, $this->getQuery()->current(), $this->isColumnUsageReportingEnabled);
	}

	public function getFactory() {
		return $this->db->getFactory();
	}

	public function key() {
		$this->reportColumnUsage($this->primaryKey);
		return $this->getQuery()->current()[$this->primaryKey];
	}

	public function next() {
		$this->getQuery()->next();
	}

	public function rewind() {
		$this->getQuery()->rewind();
	}

	public function valid() {
		return $this->getQuery()->valid();
	}

	public function where(...$args) {
		$this->getQuery()->where(...$args);
		return $this;
	}

	public function getQuery() {
		if ($this->query === null) {
			$this->query = $this->db->getFactory()->getSelectQuery($this);
		}
		return $this->query;
	}

	public function reportColumnUsage($usedColumn) {
		return $this->reportColumnsUsage([$usedColumn]);
	}

	public function reportColumnsUsage(array $usedColumns) {
		$newUsedColumns = array_diff($usedColumns, $this->usedColumns);
		if (!empty($newUsedColumns)) {
			$this->isColumnUsageReportingEnabled = true;
		}
		$this->usedColumns = array_merge($this->usedColumns, $newUsedColumns);
		return $this;
	}

	public function getDb() {
		return $this->db;
	}

	public function getName() {
		return $this->name;
	}

	public function query($query) {
		$translations = $this->getPlaceholderTranslations();
		$queryTranslated = strtr($query, $translations);
		return $this->db->query($queryTranslated);
	}

	public function getPlaceholderTranslations() {
		return [
			':id' => $this->getPrimaryKey(),
			':table' => $this->getConventionTableName(),
		];
	}

	public function getConventionTableName() {
		return $this->db->getConventionTableName($this->name);
	}

	public function getForeignKey($tableName) {
		return $this->db->getConventionForeignKey($tableName);
	}

	public function getPrimaryKey() {
		return $this->primaryKey;
	}

	public function getUsedColumns() {
		return $this->usedColumns;
	}
}
