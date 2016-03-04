<?php

namespace Sharkodlak\FluentDb;

class TableTest extends \PHPUnit_Framework_TestCase {
	public function testArrayAccess() {
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testInvoke() { // Table is called as a function, it has same meaning as execute
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testExecute() {
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testJoin() {
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testJoinReverse() {
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testLeftJoin() {
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testLeftJoinReverse() {
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testLimit() {
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testOffset() {
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testOrder() {
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testWhere() {
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testWholeTable() {
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testMatchingRows() {
		$this->markTestSkipped('Not implemented yet.');
	}

	public function testGetDb() {
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
		$table = new Table($db, 'language');
		$this->assertSame($db, $table->getDb());
	}

	public function testGetName() {
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
		$tableName = 'language';
		$table = new Table($db, $tableName);
		$this->assertSame($tableName, $table->getName());
	}

	public function testGetConventionTableName() {
		$pdo = $this->getMockBuilder('PDO')
			->disableOriginalConstructor()
			->getMock();
		$cache = $this->getMockBuilder(\Psr\Cache\CacheItemPoolInterface::class)
			->getMock();
		$convention = $this->getMockBuilder(Structure\DefaultConvention::class)
			->setConstructorArgs(['id', '%s_id', 'prefix_%ss_suffix'])
			->setMethods(null)
			->getMock();
		$db = $this->getMockBuilder(Db::class)
			->setConstructorArgs([$pdo, $cache, $convention])
			->setMethods(null)
			->getMock();
		$table = new Table($db, 'language');
		$this->assertSame('prefix_languages_suffix', $table->getConventionTableName());
	}

	public function testGetQuery() {
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
		$table = new Table($db, 'language');
		$query = $table->getQuery();
		$this->assertInstanceOf(Query\Query::class, $query);
		$this->assertInstanceOf(Query\Select::class, $query);
	}

	public function testQuery() {
		$query = 'SELECT * FROM %s';
		$expected = 'SELECT * FROM language';
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
		$db->expects($this->once())
			->method('getConventionTableName')
			->will($this->returnArgument(0));
		$db->expects($this->once())
			->method('query')
			->with($this->equalTo($expected));
		$table = new Table($db, 'language');
		$table->query($query);
	}
}
