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
}
