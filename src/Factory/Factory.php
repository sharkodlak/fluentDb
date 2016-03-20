<?php

namespace Sharkodlak\FluentDb\Factory;

interface Factory {
	public function getQueryBuilder(...$parts);
	public function getReferenceByTableName(\Sharkodlak\FluentDb\Row $row, $tableName);
	public function getReference(\Sharkodlak\FluentDb\Row $row, \Sharkodlak\FluentDb\Table $table);
	public function getRow(\Sharkodlak\FluentDb\Table $table, array $data, $isColumnUsageReportingEnabled = false);
	public function getTable(\Sharkodlak\FluentDb\Db $db, $name);
}
