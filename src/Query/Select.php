<?php

namespace Sharkodlak\FluentDb\Query;

class Select extends TableQuery implements \Iterator {
	private $currentOffset = 0;
	private $currentRow;

	public function __construct(\Sharkodlak\FluentDb\Table $table) {
		parent::__construct($table);
		$this->parts = [
			'command' => 'SELECT',
			'columns' => null,
			'fromClause' => 'FROM',
			'table' => ':table'
		];
		$this->setupColumns();
	}

	public function dropResult() {
		parent::dropResult();
		$this->setupColumns();
	}

	final public function setupColumns() {
		$usedColumns = $this->table->getUsedColumns();
		$this->parts['columns'] = empty($usedColumns) ? '*' : new PartsComma($usedColumns);
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
