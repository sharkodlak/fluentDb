<?php

namespace Sharkodlak\FluentDb;

class Db {
	private $cache;
	private $pdo;

	public function __construct(\PDO $pdo, \Psr\Cache\CacheItemPoolInterface $cache) {
		$this->pdo = $pdo;
		$this->cache = $cache;
	}
}
