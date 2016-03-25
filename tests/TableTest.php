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

	public function test__destructWithReportingDisabled() {
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
		$db->expects($this->never())
			->method('saveTableColumns');
		$table = new Table($db, 'language');
		$table->__destruct();
	}

	public function test__destruct() {
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
		$db->expects($this->once())
			->method('saveTableColumns')
			->with(
				$this->equalTo('language'),
				$this->equalTo(['language_id', 'name'])
			);
		$table = new Table($db, 'language');
		$table->reportColumnUsage('language_id');
		$table->reportColumnUsage('name');
		unset($table);
	}

	public function testArrayAccess() { // Select only row matching given id
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testInvoke() { // Table is called as a function, throw exception
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

	public function testGetPrimaryKey() {
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
		$db->expects($this->once())
			->method('getConventionPrimaryKey')
			->will($this->returnValue('language_id'));
		$table = new Table($db, 'language');
		$this->assertSame('language_id', $table->getPrimaryKey());
	}

	public function testGetQuery() {
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
		$factory = $this->getMockBuilder(Factory\Factory::class)
			->getMock();
		$query = $this->getMockBuilder(Query\Select::class)
			->disableOriginalConstructor()
			->getMock();
		$factory->expects($this->once())
			->method('getSelectQuery')
			->will($this->returnValue($query));
		$db->expects($this->once())
			->method('getFactory')
			->will($this->returnValue($factory));
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
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
			$factory = $this->getMockBuilder(Factory\Factory::class)
				->getMock();
				$query = $this->getMockBuilder(Query\Select::class)
					->disableOriginalConstructor()
					->getMock();
				$query->expects($this->exactly(1 + count(self::$language)))
					->method('valid')
					->will(call_user_func_array(array($this, 'onConsecutiveCalls'), self::$language));
				$firstRun = true;
				$query->expects($this->exactly(2 * count(self::$language)))
					->method('current')
					->will($this->returnCallback(function() use (&$firstRun) {
						$rowData = current(self::$language);
						if (!$firstRun) {
							next(self::$language);
						}
						$firstRun = !$firstRun;
						return $rowData;
					}));
			$factory->expects($this->once())
				->method('getSelectQuery')
				->will($this->returnValue($query));
				$row = $this->getMockBuilder(Row::class)
					->disableOriginalConstructor()
					->getMock();
				$row->expects($this->exactly(count(self::$language)))
					->method('toArray')
					->will(call_user_func_array(array($this, 'onConsecutiveCalls'), self::$language));
			$factory->expects($this->exactly(count(self::$language)))
				->method('getRow')
				->will($this->returnValue($row));
		$db->expects($this->exactly(1 + count(self::$language)))
			->method('getFactory')
			->will($this->returnValue($factory));
		$db->expects($this->once())
			->method('getConventionPrimaryKey')
			->will($this->returnValue('language_id'));
		$table = new Table($db, 'language');
		foreach ($table as $id => $row) {
			$expected = self::$language[$id];
			$this->assertEquals($expected, $row->toArray());
		}
	}

	public function testAutoSelectColumns() {
		$expected = [
			[],
			['language_id', 'name'],
		];
		$iterations = count($expected);
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
		$factory = $this->getMockBuilder(Factory\Factory::class)
			->getMock();
		$query = $this->getMockBuilder(Query\Select::class)
			->disableOriginalConstructor()
			->getMock();
		$factory->expects($this->exactly($iterations))
			->method('getSelectQuery')
			->will($this->returnValue($query));
		$db->expects($this->atLeastOnce())
			->method('getFactory')
			->will($this->returnValue($factory));
		$db->expects($this->any())
			->method('getConventionTableName')
			->will($this->returnArgument(0));
		$db->expects($this->exactly($iterations))
			->method('getTableColumns')
			->will($this->onConsecutiveCalls(null, $expected[1]));
		for ($i = 0; $i < $iterations; ++$i) {
			$table = new Table($db, 'language');
			$this->assertEquals($expected[$i], $table->getUsedColumns());
			foreach ($table as $id => $row) {
				$row['name'];
			}
			$query = $table->getQuery();
			$query->dropResult();
		}
	}
}
