<?php

namespace Sharkodlak\FluentDb\Query;

abstract class TableQuery implements Query {
	use MethodsTrait;
	protected $result;
	protected $table;

	public function __construct(\Sharkodlak\FluentDb\Table $table) {
		$this->table = $table;
	}

	protected function getQueryBuilder() {
		return $this->getBuilder();
	}

	abstract public function getBuilder();

	public function dropResult() {
		$this->result = null;
		return $this;
	}

	public function executeOnce() {
		if ($this->isExecuted()) {
			throw new \Sharkodlak\Exception\IllegalStateException('Query is already executed.');
		}
		return $this->execute();
	}

	public function getResult() {
		if (!$this->isExecuted()) {
			$this->execute();
		}
		return $this->result;
	}

	public function isExecuted() {
		return $this->result != null;
	}

	private function execute() {
		$query = (string) $this;
		$statement = $this->table->query($query);
		if ($statement === false || !$statement->execute()) {
			throw new \Exception('Statement execution error.');
		}
		$this->result = $statement;
		return $this->result;
	}

	public function __toString() {
		$query = (string) $this->getBuilder();
		$translations = $this->table->getPlaceholderTranslations();
		return strtr($query, $translations);
	}
}
