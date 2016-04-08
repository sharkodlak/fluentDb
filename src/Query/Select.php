<?php

namespace Sharkodlak\FluentDb\Query;

class Select extends TableQuery implements \Iterator {
	private $builder;
	private $command = 'SELECT';
	private $currentOffset = 0;
	private $currentRow;

	public function __construct(\Sharkodlak\FluentDb\Table $table) {
		parent::__construct($table);
		$this->builder = $table->getFactory()
			->getQueryBuilder($this->command, 'FROM', 'WHERE', 'ORDER BY', 'OFFSET', 'LIMIT');
		$this->builder->from(':table');
		$this->setupColumns();
	}

	public function getBuilder() {
		return $this->builder;
	}

	public function dropResult() {
		parent::dropResult();
		$this->setupColumns();
	}

	final public function setupColumns() {
		$usedColumns = $this->table->getUsedColumns();
		$this->builder[$this->command] = $usedColumns ?: '*';
		return $this;
	}

	public function current() {
		return $this->currentRow;
	}

	public function key() {
		return $this->currentOffset;
	}

	public function next() {
		++$this->currentOffset;
		$result = $this->getResult();
		$this->currentRow = $result->fetch(\PDO::FETCH_ASSOC);
	}

	public function rewind() {
		$this->currentOffset = 0;
		$result = $this->getResult();
		$this->currentRow = $result->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_FIRST);
	}

	public function valid() {
		return $this->currentRow !== false;
	}
}
