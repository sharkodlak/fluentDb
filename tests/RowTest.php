<?php

namespace Sharkodlak\FluentDb;

class RowTest extends \PHPUnit_Framework_TestCase {
	static private $languageRow = [
		'language_id' => 1,
		'name' => 'English',
		'last_update' => '2006-02-15 10:02:19',
	];

	public function testCallTable() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testOffsetExists() {
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('reportColumnUsage')
			->with($this->equalTo('language_id'));
		$row = new Row($table, self::$languageRow, true);
		$this->assertTrue(isset($row['language_id']));
		$this->assertFalse(isset($row['unknown_column']));
	}

	public function testOffsetGet() {
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->atLeastOnce())
			->method('reportColumnUsage')
			->withConsecutive(
				[$this->equalTo('language_id')],
				[$this->equalTo('name')],
				[$this->equalTo('last_update')]
			);
		$row = new Row($table, self::$languageRow, true);
		$this->assertEquals(self::$languageRow['language_id'], $row['language_id']);
		$this->assertEquals(self::$languageRow['name'], $row['name']);
		$this->assertEquals(self::$languageRow['last_update'], $row['last_update']);
		$this->expectException(\PHPUnit_Framework_Error_Notice::class);
		$this->assertNull($row['unknown_column']);
	}

	public function testLoadMissingColumn() {
		$languageRowFiltered = array_intersect_key(self::$languageRow, array_flip(['language_id', 'name']));
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->exactly(3))
			->method('reportColumnUsage')
			->withConsecutive(
				[$this->equalTo('language_id')],
				[$this->equalTo('name')],
				[$this->equalTo('last_update')]
			);
		$row = new Row($table, $languageRowFiltered, true);
		$this->markTestIncomplete('Not implemented yet.');
		$this->assertEquals(self::$languageRow['language_id'], $row['language_id']);
		$this->assertEquals(self::$languageRow['name'], $row['name']);
		// Next column shall be loaded from DB just in time
		$this->assertEquals(self::$languageRow['last_update'], $row['last_update']);
	}

	public function testToArray() {
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('reportColumnsUsage')
			->with($this->equalTo(array_keys(self::$languageRow)));
		$row = new Row($table, self::$languageRow, true);
		$this->assertEquals(self::$languageRow, $row->toArray());
	}

	public function testGetIterator() {
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$row = new Row($table, self::$languageRow);
		$data = iterator_to_array($row->getIterator());
		$this->assertEquals(self::$languageRow, $data);
	}
}
