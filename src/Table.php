<?php

namespace Sharkodlak\FluentDb;

class Table implements \Iterator {
	private $db;
	private $isColumnUsageReportingEnabled;
	private $name;
	private $primaryKey;
	private $query;
	private $usedColumns;

	public function __construct(Db $db, $name) {
		$this->db = $db;
		$this->name = $name;
		$this->primaryKey = $db->getConventionPrimaryKey($name);
		$this->usedColumns = $db->getTableColumns($name) ?: [];
		$this->isColumnUsageReportingEnabled = empty($this->usedColumns);
		$this->query = new Query\Select($this);
	}

	public function __destruct() {
		if ($this->isColumnUsageReportingEnabled && !empty($this->usedColumns)) {
			$this->db->saveTableColumns($this->name, $this->usedColumns);
		}
	}

	public function current() {
		return new Row($this, $this->query->current(), $this->isColumnUsageReportingEnabled);
	}

	public function key() {
		$this->reportColumnUsage($this->primaryKey);
		return $this->query->current()[$this->primaryKey];
	}

	public function next() {
		$this->query->next();
	}

	public function rewind() {
		$this->query->rewind();
	}

	public function valid() {
		return $this->query->valid();
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
			':table' => $this->getConventionTableName(),
		];
	}

	public function getConventionTableName() {
		return $this->db->getConventionTableName($this->name);
	}

	public function getQuery() {
		return $this->query;
	}

	public function getUsedColumns() {
		return $this->usedColumns;
	}
}
