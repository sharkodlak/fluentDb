<?php

namespace Sharkodlak\FluentDb;

class Row {
	private $data;
	private $table;

	public function __construct(array $data, Table $table) {
		$this->data = $data;
		$this->table = $table;
	}
}
