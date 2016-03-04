<?php

namespace Sharkodlak\FluentDb;

class Db {
	private $cache;
	private $convention;
	private $pdo;

	public function __construct(\PDO $pdo, \Psr\Cache\CacheItemPoolInterface $cache, Structure\Convention $convention) {
		$this->pdo = $pdo;
		$this->cache = $cache;
		$this->convention = $convention;
	}

	public function __get($name) {
		return $this->table($name);
	}

	/** Get object representing table.
	 *
	 * This method acts as factory for creating new Table with reference to this DB object.
	 *
	 * @param string  $name Simplified table name.
	 *
	 * @returns Table  Table representation.
	 */
	public function table($name) {
		return new Table($this, $name);
	}

	public function getConventionTableName($tableName) {
		return $this->convention->getTableName($tableName);
	}

	public function query($query) {
		return $this->pdo->query($query);
	}
}
