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

	public function getConventionPrimaryKey($tableName) {
		return $this->convention->getPrimaryKey($tableName);
	}

	public function getConventionTableName($tableName) {
		return $this->convention->getTableName($tableName);
	}

	public function getTableColumns($tableName) {
		$item = $this->getTableColumnsItem($tableName);
		return $item->get();
	}

	public function saveTableColumns($tableName, array $columns) {
		$item = $this->getTableColumnsItem($tableName);
		$item->set($columns);
		return $this->cache->save($item);
	}

	protected function getTableColumnsItem($tableName) {
		$cacheKey = $this->convention->getCacheKeyTableColumns($tableName);
		return $this->cache->getItem($cacheKey);
	}

	public function query($query) {
		return $this->pdo->query($query);
	}
}
