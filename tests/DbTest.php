<?php

namespace Sharkodlak\FluentDb;

class DbTest extends \PHPUnit_Framework_TestCase {
	public function test__Get() {
		$pdo = $this->getMockBuilder('PDO')
			->disableOriginalConstructor()
			->getMock();
		$cache = $this->getMockBuilder(\Psr\Cache\CacheItemPoolInterface::class)
			->getMock();
		$cacheItem = $this->getMockBuilder(\Psr\Cache\CacheItemInterface::class)
			->getMock();
		$cache->expects($this->any())
			->method('getItem')
			->will($this->returnValue($cacheItem));
		$convention = $this->getMockBuilder(Structure\Convention::class)
			->getMock();
		$convention->expects($this->once())
			->method('getCacheKeyTableColumns')
			->will($this->returnArgument(0));
		$db = new Db($pdo, $cache, $convention);
		$this->assertInstanceOf(Table::class, $db->film);
	}

	public function testQuery() {
		$query = 'SELECT * FROM film';
		$pdo = $this->getMockBuilder('PDO')
			->disableOriginalConstructor()
			->getMock();
		$pdo->expects($this->once())
			->method('query')
			->with($this->equalTo($query));
		$cache = $this->getMockBuilder(\Psr\Cache\CacheItemPoolInterface::class)
			->getMock();
		$convention = $this->getMockBuilder(Structure\DefaultConvention::class)
			->disableOriginalConstructor()
			->getMock();
		$db = new Db($pdo, $cache, $convention);
		$db->query($query);
	}
}
