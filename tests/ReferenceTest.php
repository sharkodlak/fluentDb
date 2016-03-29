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

	public function testVia() {
		$row = $this->getMockBuilder(Row::class)
			->disableOriginalConstructor()
			->getMock();
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
			->method('offsetGet')
			->will($this->returnValue(new Row($table, self::$languages[6])));
		$reference = new Reference($row, $table);
		$via = $reference->via();
		$this->assertInstanceOf(Row::class, $via);
		$this->assertEquals(self::$languages[6], $via->toArray());
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

	public function testArrayAccess() {
		$row = $this->getMockBuilder(Row::class)
			->disableOriginalConstructor()
			->getMock();
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
			->method('offsetGet')
			->will($this->onConsecutiveCalls(self::$languages[6], self::$languages[1]));
		$filmLanguages = new Reference($row, $table);
		$expectedLanguageIds = [6, 1];
		$i = 0;
		$expected = self::$languages[$expectedLanguageIds[$i++]];
		$this->assertEquals($expected['name'], $filmLanguages['name']);
	}
}
