<?php

namespace Sharkodlak\FluentDb\Query;

class Select extends TableQuery implements \Iterator {
	private $currentOffset = 0;
	private $currentRow;

	public function __construct(\Sharkodlak\FluentDb\Table $table) {
		parent::__construct($table);
		$usedColumns = $table->getUsedColumns();
		$columns = empty($usedColumns) ? '*' : new PartsComma($usedColumns);
		$this->parts = [
			'command' => 'SELECT',
			'columns' => $columns,
			'fromClause' => 'FROM',
			'table' => ':table'
	   ];
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
