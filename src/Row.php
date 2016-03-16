<?php

namespace Sharkodlak\FluentDb;

class Row implements \ArrayAccess, \IteratorAggregate {
	private $data;
	private $isColumnUsageReportingEnabled;
	private $table;

	public function __construct(Table $table, array $data, $isColumnUsageReportingEnabled = false) {
		$this->table = $table;
		$this->data = $data;
		$this->isColumnUsageReportingEnabled = $isColumnUsageReportingEnabled;
	}

	public function offsetExists($offset) {
		$exists = array_key_exists($offset, $this->data);
		if ($exists && $this->isColumnUsageReportingEnabled) {
			$this->table->reportColumnUsage($offset);
		}
		return $exists;
	}

	public function offsetGet($offset) {
		if ($this->isColumnUsageReportingEnabled) {
			$this->table->reportColumnUsage($offset);
		}
		return $this->data[$offset];
	}

	public function offsetSet($offset, $value) {
		$this->data[$offset] = $value;
	}

	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}

	public function getIterator() {
		return new \ArrayIterator($this->toArray());
	}

	public function toArray() {
		if ($this->isColumnUsageReportingEnabled) {
			$usedColumns = array_keys($this->data);
			$this->table->reportColumnsUsage($usedColumns);
		}
		return $this->data;
	}
}
