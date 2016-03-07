<?php

namespace Sharkodlak\FluentDb;

class Table implements \Iterator {
	private $currentRow;
	private $db;
	private $name;
	private $primaryKey;
	private $query;

	public function __construct(Db $db, $name) {
		$this->db = $db;
		$this->name = $name;
		$this->primaryKey = $db->getConventionPrimaryKey($name);
		$this->query = new Query\Select($this);
	}

	public function current() {
		return new Row($this, $this->currentRow);
	}

	public function key() {
		return $this->currentRow[$this->primaryKey];
	}

	public function next() {
		$result = $this->query->getResult();
		$this->currentRow = $result->fetch(\PDO::FETCH_ASSOC);
	}

	public function rewind() {
		$result = $this->query->getResult();
		$this->currentRow = $result->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_FIRST);
	}

	public function valid() {
		return $this->currentRow !== false;
	}

	public function getDb() {
		return $this->db;
	}

	public function getName() {
		return $this->name;
	}

	public function query($query) {
		$translations = $this->getPlaceholderTranslations();
		$queryTranslated = strtr($query, $translations);
		return $this->db->query($queryTranslated);
	}

	public function getPlaceholderTranslations() {
		return [
			':table' => $this->getConventionTableName(),
		];
	}

	public function getConventionTableName() {
		return $this->db->getConventionTableName($this->name);
	}

	public function getQuery() {
		return $this->query;
	}
}
