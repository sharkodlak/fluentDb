<?php

namespace Sharkodlak\FluentDb;

class Db {
	private $cache;
	private $convention;
	private $factory;
	private $pdo;

	public function __construct(\PDO $pdo, \Psr\Cache\CacheItemPoolInterface $cache, Structure\Convention $convention, Factory\Factory $factory) {
		$this->pdo = $pdo;
		$this->cache = $cache;
		$this->convention = $convention;
		$this->factory = $factory;
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
		return $this->factory->getTable($this, $name);
	}

	public function getConventionPrimaryKey($tableName) {
		return $this->convention->getPrimaryKey($tableName);
	}

	public function getConventionTableName($tableName) {
		return $this->convention->getTableName($tableName);
	}

	public function getFactory() {
		return $this->factory;
	}

	public function getTableColumns($tableName) {
		$item = $this->getTableColumnsItem($tableName);
		return $item->get();
	}

	public function saveTableColumns($tableName, array $columns) {
		$item = $this->getTableColumnsItem($tableName);
		$item->set($columns);
		return $this->cache->saveDeferred($item);
	}

	private function getTableColumnsItem($tableName) {
		$cacheKey = $this->convention->getCacheKeyTableColumns($tableName);
		return $this->cache->getItem($cacheKey);
	}

	public function query($query) {
		return $this->pdo->query($query);
	}
}
