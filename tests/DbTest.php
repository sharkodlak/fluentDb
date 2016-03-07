<?php

namespace Sharkodlak\FluentDb;

class DbTest extends \PHPUnit_Framework_TestCase {
	public function test__Get() {
		$pdo = $this->getMockBuilder('PDO')
			->disableOriginalConstructor()
			->getMock();
		$cache = $this->getMockBuilder(\Psr\Cache\CacheItemPoolInterface::class)
			->getMock();
		$convention = $this->getMockBuilder(Structure\DefaultConvention::class)
			->disableOriginalConstructor()
			->getMock();
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
