<?php

namespace Sharkodlak\FluentDb\Query;

class Columns {
	private $columns = [];

	public function __toString() {
		$columns = [];
		foreach ($this->columns as $columnName => $expression) {
			if ($expression == null) {
				$columns[$columnName] = $columnName;
			} else {
				$columns[$columnName] = $expression . ' AS ' . $columnName;
			}
		}
		return implode(', ', $columns);
	}

	public function add($columnName, $expression = null) {
		$this->columns[$columnName] = $expression;
	}

	public function set(array $columns) {
		$this->columns = $columns;
	}
}
