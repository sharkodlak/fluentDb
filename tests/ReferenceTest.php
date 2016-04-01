<?php

namespace Sharkodlak\FluentDb;

class ReferenceTest extends \PHPUnit_Framework_TestCase {
	static private $films = [
		-1 => [
			'film_id' => -1,
			'title' => 'Die Welle',
			'description' => "A high school teacher's experiment to demonstrate to his students what life is like under a dictatorship spins horribly out of control when he forms a social unit with a life of its own.",
			'release_year' => 2008,
			'language_id' => 6,
			'length' => 110,
		],
		1 => [
			'film_id' => 1,
			'title' => 'Academy Dinosaur',
			'description' => 'A Epic Drama of a Feminist And a Mad Scientist who must Battle a Teacher in The Canadian Rockies',
			'release_year' => 2006,
			'language_id' => 1,
			'length' => 86,
		],
	];
	static private $filmCategories = [
		[
			'film_id' => -1,
			'category_id' => -1,
		],
		[
			'film_id' => -1,
			'category_id' => 7,
		],
	];
	static private $languages = [
		1 => [
			'language_id' => 1,
			'name' => 'English',
			'last_update' => '2006-02-15 10:02:19',
		],
		6 => [
			'language_id' => 6,
			'name' => 'German',
			'last_update' => '2006-02-15 10:02:19',
		],
	];

	public function testGetRowForeignColumnName() {
		$expected = 'film_id';
		$tableName = 'film';
		$row = $this->getMockBuilder(Row::class)
			->disableOriginalConstructor()
			->getMock();
		$row->expects($this->once())
			->method('getForeignKey')
			->with($this->equalTo($tableName))
			->will($this->returnValue($expected));
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('getName')
			->will($this->returnValue($tableName));
		$reference = new Reference($row, $table);
		$this->assertEquals($expected, $reference->getRowForeignColumnName(null));
		$expected = 'parent_id';
		$this->assertEquals($expected, $reference->getRowForeignColumnName($expected));
	}

	public function testGetTablePrimaryColumnName() {
		$expected = 'film_id';
		$row = $this->getMockBuilder(Row::class)
			->disableOriginalConstructor()
			->getMock();
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->exactly(2))
			->method('getPrimaryKey')
			->will($this->returnValue($expected));
		$reference = new Reference($row, $table);
		$this->assertEquals($expected, $reference->getTablePrimaryColumnName(null));
		$this->assertEquals($expected, $reference->getTablePrimaryColumnName(':id'));
		$expected = 'renamed_id';
		$this->assertEquals($expected, $reference->getTablePrimaryColumnName($expected));
	}

	public function testVia() {
		$rowTableName = 'film';
		$rowPrimary = 'film_id';
		$tablePrimary = 'language_id';
		$row = $this->getMockBuilder(Row::class)
			->disableOriginalConstructor()
			->getMock();
		$row->expects($this->once())
			->method('getForeignKey')
			->will($this->returnValue($tablePrimary));
		$query = $this->getMockBuilder(Query\Select::class)
			->disableOriginalConstructor()
			->getMock();
		$row->expects($this->once())
			->method('getQuery')
			->will($this->returnValue($query));
		$row->expects($this->once())
			->method('offsetGet')
			->with($this->equalTo($tablePrimary))
			->will($this->returnValue(1));
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('getPrimaryKey')
			->will($this->returnValue($tablePrimary));
		$table->expects($this->once())
			->method('getRows')
			->will($this->returnValue([new Row($table, self::$languages[1]), new Row($table, self::$languages[6])]));
		$reference = new Reference($row, $table);
		$via = $reference->via();
		$this->assertInstanceOf(Row::class, $via);
		$this->assertEquals(self::$languages[1], $via->toArray());
	}

	public function testArrayAccess() {
		$tablePrimary = 'language_id';
		$row = $this->getMockBuilder(Row::class)
			->disableOriginalConstructor()
			->getMock();
		$query = $this->getMockBuilder(Query\Select::class)
			->disableOriginalConstructor()
			->getMock();
		$row->expects($this->once())
			->method('getQuery')
			->will($this->returnValue($query));
		$row->expects($this->once())
			->method('offsetGet')
			->will($this->returnValue(6));
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('getPrimaryKey')
			->will($this->returnValue($tablePrimary));
		$table->expects($this->once())
			->method('getRows')
			->will($this->returnValue([new Row($table, self::$languages[1]), new Row($table, self::$languages[6])]));
		$filmLanguages = new Reference($row, $table);
		$expected = self::$languages[6];
		$this->assertEquals($expected['name'], $filmLanguages['name']);
	}

	public function testGetTableForeignColumnName() {
		$expected = 'film_id';
		$tableName = 'film';
		$row = $this->getMockBuilder(Row::class)
			->disableOriginalConstructor()
			->getMock();
		$row->expects($this->once())
			->method('getTableName')
			->will($this->returnValue($tableName));
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('getForeignKey')
			->with($this->equalTo($tableName))
			->will($this->returnValue($expected));
		$reference = new Reference($row, $table);
		$this->assertEquals($expected, $reference->getTableForeignColumnName(null));
		$expected = 'parent_id';
		$this->assertEquals($expected, $reference->getTableForeignColumnName($expected));
	}

	public function testGetRowPrimaryColumnName() {
		$expected = 'film_id';
		$row = $this->getMockBuilder(Row::class)
			->disableOriginalConstructor()
			->getMock();
		$row->expects($this->exactly(2))
			->method('getPrimaryKey')
			->will($this->returnValue($expected));
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$reference = new Reference($row, $table);
		$this->assertEquals($expected, $reference->getRowPrimaryColumnName(null));
		$this->assertEquals($expected, $reference->getRowPrimaryColumnName(':id'));
		$expected = 'renamed_id';
		$this->assertEquals($expected, $reference->getRowPrimaryColumnName($expected));
	}

	public function testBackwards() {
		$row = $this->getMockBuilder(Row::class)
			->disableOriginalConstructor()
			->getMock();
		$row->expects($this->once())
			->method('getPrimaryKey')
			->will($this->returnValue('film_id'));
		$row->expects($this->once())
			->method('offsetGet')
			->will($this->returnValue(-1));
		$query = $this->getMockBuilder(Query\Select::class)
			->disableOriginalConstructor()
			->getMock();
		$row->expects($this->once())
			->method('getQuery')
			->will($this->returnValue($query));
		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('getRows')
			->will($this->returnValue([new Row($table, self::$filmCategories[0]), new Row($table, self::$filmCategories[1])]));
		$reference = new Reference($row, $table);
		$rows = $reference->backwards();
		$this->assertEquals(2, count($rows));
		$i = 0;
		foreach ($rows as $row) {
			$expected = self::$filmCategories[$i++];
			$this->assertInstanceOf(Row::class, $row);
			$this->assertEquals($expected, $row->toArray());
		}
	}
}
