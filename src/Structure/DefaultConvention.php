<?php

namespace Sharkodlak\FluentDb\Structure;

class DefaultConvention implements Convention {
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

	public function getForeignKey($tableName) {
		return sprintf($this->foreign, $tableName);
	}

	public function getTableName($tableName) {
		return sprintf($this->table, $tableName);
	}
}
