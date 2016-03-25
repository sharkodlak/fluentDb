<?php

namespace Sharkodlak\FluentDb\Structure;

class DefaultConvention implements Convention {
	private $cacheKeyTableColumns = '%s.columns';
	private $primary;
	private $foreign;
	private $table;

	public function __construct($primary = 'id', $foreign = '%s_id', $table = '%s') {
		$this->primary = $primary;
		$this->foreign = $foreign;
		$this->table = $table;
	}

	public function getPrimaryKey($tableName) {
		return sprintf($this->primary, $tableName);
	}

	public function getForeignKey($destinationTableName, $sourceTableName = null) {
		return sprintf($this->foreign, $destinationTableName);
	}

	public function getTableName($tableName) {
		return sprintf($this->table, $tableName);
	}

	public function getCacheKeyTableColumns($tableName) {
		return sprintf($this->cacheKeyTableColumns, $tableName);
	}
}
