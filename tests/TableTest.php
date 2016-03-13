<?php

namespace Sharkodlak\FluentDb;

class TableTest extends \PHPUnit_Framework_TestCase {
	static private $language = [
		1 => [
			'language_id' => 1,
			'name' => 'English',
			'last_update' => '2006-02-15 10:02:19',
		],
		[
			'language_id' => 2,
			'name' => 'Italian',
			'last_update' => '2006-02-15 10:02:19',
		],
		[
			'language_id' => 3,
			'name' => 'Japanese',
			'last_update' => '2006-02-15 10:02:19',
		],
		[
			'language_id' => 4,
			'name' => 'Mandarin',
			'last_update' => '2006-02-15 10:02:19',
		],
		[
			'language_id' => 5,
			'name' => 'French',
			'last_update' => '2006-02-15 10:02:19',
		],
		[
			'language_id' => 6,
			'name' => 'German',
			'last_update' => '2006-02-15 10:02:19',
		],
	];

	public function testArrayAccess() { // Select only row matching given id
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testInvoke() { // Table is called as a function, throw exception
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
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
		$db->expects($this->once())
			->method('getConventionTableName')
			->will($this->returnValue('prefix_languages_suffix'));
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

	public function testIterator() {
		$pdoStatement = $this->getMockBuilder('PDOStatement')
			->disableOriginalConstructor()
			->getMock();
		$pdoStatement->expects($this->once())
			->method('execute')
			->will($this->returnValue(true));
		$languageFetches = array_pad(self::$language, count(self::$language) + 1, false);
		$pdoStatement->expects($this->exactly(count($languageFetches)))
			->method('fetch')
			->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $languageFetches));
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
		$db->expects($this->exactly(2))
			->method('getConventionTableName')
			->will($this->returnArgument(0));
		$db->expects($this->once())
			->method('getConventionPrimaryKey')
			->will($this->returnValue('language_id'));
		$db->expects($this->once())
			->method('query')
			->will($this->returnValue($pdoStatement));
		$table = new Table($db, 'language');
		foreach ($table as $id => $row) {
			$expected = self::$language[$id];
			$this->assertEquals($expected, $row->toArray());
		}
	}
}
