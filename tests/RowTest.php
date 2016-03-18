<?php

namespace Sharkodlak\FluentDb;

class RowTest extends \PHPUnit_Framework_TestCase {
	static private $filmRow = [
		'film_id' => 1,
		'title' => 'Academy Dinosaur',
		'description' => 'A Epic Drama of a Feminist And a Mad Scientist who must Battle a Teacher in The Canadian Rockies',
		'release_year' => 2006,
		'language_id' => 1,
		'length' => 86,
	];
	static private $languageRow = [
		'language_id' => 1,
		'name' => 'English',
		'last_update' => '2006-02-15 10:02:19',
	];

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

	public function testCallTable() {
		$films = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$factory = $this->getMockBuilder(Factory\Factory::class)
			->getMock();
		$reference = $this->getMockBuilder(Reference::class)
			->disableOriginalConstructor()
			->getMock();
		$factory->expects($this->once())
			->method('getReferenceByTableName')
			->will($this->returnValue($reference));
		$films->expects($this->once())
			->method('getFactory')
			->will($this->returnValue($factory));
		$films->expects($this->once())
			->method('reportColumnUsage')
			->with($this->equalTo('language_id'));
		$film = new Row($films, self::$filmRow, true);
		$filmLanguage = $film->language;
		$this->assertInstanceOf(Reference::class, $filmLanguage);
		$this->markTestIncomplete();
		$this->assertEquals(self::$languageRow['language_id'], $filmLanguage['language_id']);
		$this->assertEquals(self::$languageRow['name'], $filmLanguage['name']);
		$this->assertEquals(self::$languageRow['last_update'], $filmLanguage['last_update']);
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

	public function testGetDb() {
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$db = $this->getMockBuilder(Db::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('getDb')
			->will($this->returnValue($db));
		$row = new Row($table, self::$languageRow);
		$this->assertSame($db, $row->getDb());
	}

	public function testGetPrimaryKey() {
		$expected = 'table_id';
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('getPrimaryKey')
			->will($this->returnValue($expected));
		$row = new Row($table, self::$languageRow);
		$this->assertSame($expected, $row->getPrimaryKey());
	}

	public function test__Get() {
		$factory = $this->getMockBuilder(Factory\Factory::class)
			->getMock();
		$reference = $this->getMockBuilder(Reference::class)
			->disableOriginalConstructor()
			->getMock();
		$factory->expects($this->once())
			->method('getReferenceByTableName')
			->with($this->isInstanceOf(Row::class), $this->equalTo('anotherTable'))
			->will($this->returnValue($reference));
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('getFactory')
			->will($this->returnValue($factory));
		$row = new Row($table, self::$languageRow);
		$this->assertSame($reference, $row->anotherTable);
	}
}
