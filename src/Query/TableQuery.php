<?php

namespace Sharkodlak\FluentDb\Query;

class TableQuery implements Query {
	private $table;
	protected $parts;
	private $result;

	public function __construct(\Sharkodlak\FluentDb\Table $table) {
		$this->table = $table;
	}

	public function getResult() {
		if (!$this->isExecuted()) {
			$this->execute();
		}
		return $this->result;
	}

	public function executeOnce() {
		if ($this->isExecuted()) {
			throw new \Sharkodlak\Exception\IllegalStateException('Query is already executed.');
		}
		return $this->execute();
	}

	public function isExecuted() {
		return $this->result != null;
	}

	private function execute() {
		$query = (string) $this;
		$result = $this->table
			->getDb()
			->getDriver()
			->query($query);
		$this->result = new Result($result);
		return $this->result;
	}

	public function __toString() {
		$joinedParts = implode(' ', $this->parts);
		$tableName = $this->table
			->getDb()
			->getConvention()
			->getTableName($this->name);
		return sprintf($joinedParts, $tableName);
	}
}
