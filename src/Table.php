<?php

namespace Sharkodlak\FluentDb;

class Table {
	private $db;
	private $name;
	private $query;

	public function __construct(Db $db, $name) {
		$this->db = $db;
		$this->name = $name;
		$this->query = new Query\Select($this);
	}

	public function getQuery() {
		return $this->query;
	}
}
