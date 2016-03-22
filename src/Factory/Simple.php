<?php

namespace Sharkodlak\FluentDb\Factory;

class Simple implements Factory {
	public function getQueryBuilder(...$parts) {
		$partKeys = [];
		foreach ($parts as $part) {
			$partKeys[$part] = null;
		}
		return new \Sharkodlak\FluentDb\Query\Builder($partKeys);
	}

	public function getSelectQuery(\Sharkodlak\FluentDb\Table $table) {
		return new \Sharkodlak\FluentDb\Query\Select($table);
	}

	public function getTable(\Sharkodlak\FluentDb\Db $db, $name) {
		return new \Sharkodlak\FluentDb\Table($db, $name);
	}

	public function getRow(\Sharkodlak\FluentDb\Table $table, array $data, $isColumnUsageReportingEnabled = false) {
		return new \Sharkodlak\FluentDb\Row($table, $data, $isColumnUsageReportingEnabled);
	}

	public function getReferenceByTableName(\Sharkodlak\FluentDb\Row $row, $tableName) {
		$db = $row->getDb();
		$table = $this->getTable($db, $tableName);
		return $this->getReference($row, $table);
	}

	public function getReference(\Sharkodlak\FluentDb\Row $row, \Sharkodlak\FluentDb\Table $table) {
		return new \Sharkodlak\FluentDb\Reference($row, $table);
	}
}
