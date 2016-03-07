<?php

namespace Sharkodlak\FluentDb;

class TableTest extends \PHPUnit_Framework_TestCase {
	public function testArrayAccess() { // Select only row matching given id
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testInvoke() { // Table is called as a function, it has same meaning as execute
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testExecute() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testJoin() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testJoinReverse() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testLeftJoin() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testLeftJoinReverse() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testLimit() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testOffset() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testOrder() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testWhere() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testWholeTable() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testMatchingRows() {
		$this->markTestIncomplete('Not implemented yet.');
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
		$query = 'SELECT * FROM :table';
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
