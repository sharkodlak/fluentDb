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
		/*
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
		$reference = new Reference($row, $table);
		*/
		$file = '/etc/fluentdb/.dbconnect';
		$pdo = new \PDO('uri:file://' . $file);
		$cache = new \Stash\Pool();
		$convention = new Structure\DefaultConvention('%s_id');
		$factory = new Factory\Simple();
		$db = new Db($pdo, $cache, $convention, $factory);
		$films = $db->film;
		$films->rewind();
		$film = $films->current();
		$reference = $film->language;
		$this->assertInstanceOf(Row::class, $reference->via());
	}

	public function testBackwards() {
		$file = '/etc/fluentdb/.dbconnect';
		$pdo = new \PDO('uri:file://' . $file);
		$cache = new \Stash\Pool();
		$convention = new Structure\DefaultConvention('%s_id');
		$factory = new Factory\Simple();
		$db = new Db($pdo, $cache, $convention, $factory);
		$films = $db->film->where(':id = %d', -1);
		$films->rewind();
		$film = $films->current();
		$reference = $film->film_category;
		$rows = $reference->backwards();
		$expecteds = [
			[
				'film_id' => -1,
				'category_id' => -1,
			],
			[
				'film_id' => -1,
				'category_id' => 7,
			],
		];
		$this->assertEquals(2, count($rows));
		$i = 0;
		foreach ($rows as $row) {
			$expected = $expecteds[$i++];
			$this->assertInstanceOf(Row::class, $row);
			$this->assertEquals($expected['film_id'], $row['film_id']);
			$this->assertEquals($expected['category_id'], $row['category_id']);
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
