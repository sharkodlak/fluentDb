<?php

namespace Sharkodlak\FluentDb;

class ReferenceTest extends \PHPUnit_Framework_TestCase {
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
		$films = $db->film;
		$films->rewind();
		$film = $films->current();
		$reference = $film->film_category;
		$rows = $reference->backwards();
		$expected = [
			'film_id' => 133,
			'category_id' => 12,
		];
		$this->assertEquals(1, count($rows));
		foreach ($rows as $row) {
			$this->assertInstanceOf(Row::class, $row);
			$this->assertEquals($expected['film_id'], $row['film_id']);
			$this->assertEquals($expected['category_id'], $row['category_id']);
		}
	}
}
