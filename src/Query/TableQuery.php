<?php

namespace Sharkodlak\FluentDb\Query;

abstract class TableQuery implements Query {
	private $table;
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
		$statement = $this->table->query($query);
		if (!$statement->execute()) {
			throw new \Exception('Statement execution error.');
		}
		$this->result = $statement;
		return $this->result;
	}

	public function __toString() {
		return implode(' ', $this->getParts());
	}

	abstract protected function getParts();
}
