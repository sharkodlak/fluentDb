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

	public function testGetConventions() {
		$expected = [
			'primaryKey' => 'langugage_id',
			'tableName' => 'languages',
		];
		$pdo = $this->getMockBuilder('PDO')
			->disableOriginalConstructor()
			->getMock();
		$cache = $this->getMockBuilder(\Psr\Cache\CacheItemPoolInterface::class)
			->getMock();
		$convention = $this->getMockBuilder(Structure\Convention::class)
			->getMock();
		$convention->expects($this->once())
			->method('getPrimaryKey')
			->will($this->returnValue($expected['primaryKey']));
		$convention->expects($this->once())
			->method('getTableName')
			->will($this->returnValue($expected['tableName']));
		$db = new Db($pdo, $cache, $convention);
		$this->assertSame($expected['primaryKey'], $db->getConventionPrimaryKey('language'));
		$this->assertSame($expected['tableName'], $db->getConventionTableName('language'));
	}

	public function testTableColumns() {
		$expected = [
			null,
			['language_id', 'name'],
		];
		$pdo = $this->getMockBuilder('PDO')
			->disableOriginalConstructor()
			->getMock();
		$cache = $this->getMockBuilder(\Psr\Cache\CacheItemPoolInterface::class)
			->getMock();
		$cacheItem = $this->getMockBuilder(\Psr\Cache\CacheItemInterface::class)
			->getMock();
		$cache->expects($this->exactly(3))
			->method('getItem')
			->will($this->returnValue($cacheItem));
		$cache->expects($this->once())
			->method('saveDeferred');
		$cacheItem->expects($this->exactly(count($expected)))
			->method('get')
			->will(call_user_func_array([$this, 'onConsecutiveCalls'], $expected));
		$cacheItem->expects($this->once())
			->method('set')
			->with($this->equalTo($expected[1]));
		$convention = $this->getMockBuilder(Structure\Convention::class)
			->getMock();
		$db = new Db($pdo, $cache, $convention);
		$this->assertSame($expected[0], $db->getTableColumns('language'));
		$db->saveTableColumns('language', $expected[1]);
		$this->assertSame($expected[1], $db->getTableColumns('language'));
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
