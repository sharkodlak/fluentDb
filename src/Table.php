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

	public function getDb() {
		return $this->db;
	}

	public function getName() {
		return $this->name;
	}

	public function getConventionTableName() {
		return $this->db->getConventionTableName($this->name);
	}

	public function getQuery() {
		return $this->query;
	}

	public function getPlaceholderTranslations() {
		return [
			':table' => $this->getConventionTableName(),
		];
	}

	public function query($query) {
		$translations = $this->getPlaceholderTranslations();
		$queryTranslated = strtr($query, $translations);
		return $this->db->query($queryTranslated);
	}
}
