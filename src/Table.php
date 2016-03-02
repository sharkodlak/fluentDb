<?php

namespace Sharkodlak\FluentDb;

class Table {
	private $db;

	public function __construct(Db $db) {
		$this->db = $db;
	}
}
