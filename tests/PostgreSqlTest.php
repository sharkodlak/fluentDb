<?php

namespace Sharkodlak\FluentDb;

class PostgreSqlTest extends \PHPUnit_Framework_TestCase {
	private static $films = [
		1 => [
			'film_id' => 1,
			'title' => 'Academy Dinosaur',
			'description' => 'A Epic Drama of a Feminist And a Mad Scientist who must Battle a Teacher in The Canadian Rockies',
			'release_year' => 2006,
			'language_id' => 1,
			'length' => 86,
		],
		[
			'film_id' => 2,
			'title' => 'Ace Goldfinger',
			'description' => 'A Astounding Epistle of a Database Administrator And a Explorer who must Find a Car in Ancient China',
			'release_year' => 2006,
			'language_id' => 1,
			'length' => 48,
		],
		[
			'film_id' => 3,
			'title' => 'Adaptation Holes',
			'description' => 'A Astounding Reflection of a Lumberjack And a Car who must Sink a Lumberjack in A Baloon Factory',
			'release_year' => 2006,
			'language_id' => 1,
			'length' => 50,
		],
	];
	private static $languages = [
		1 => [
			'language_id' => 1,
			'name' => 'English             ',
			'last_update' => '2006-02-15 10:02:19',
		],
		6 => [
			'language_id' => 6,
			'name' => 'German              ',
			'last_update' => '2006-02-15 10:02:19',
		],
	];

	public function setUp() {
		$file = '/etc/fluentdb/.dbconnect';
		if (!file_exists($file)) {
			$this->markTestSkipped('Missing DB connection configuration file.');
		} else {
			$pdo = new \PDO('uri:file://' . $file);
			$cache = new \Stash\Pool();
			$convention = new Structure\DefaultConvention('%s_id');
			$factory = new Factory\Simple();
			$this->db = new Db($pdo, $cache, $convention, $factory);
		}
	}

	public function test__get() {
		$films = $this->db->film;
		$this->assertInstanceOf(Table::class, $films);
		$this->assertSame('film', $films->getName());
	}

	public function testTableQueryResult() {
		$result = $this->db->language->getQuery()->getResult();
		$this->assertInstanceOf('PDOStatement', $result);
		$expected = ['English', 'Italian', 'Japanese', 'Mandarin', 'French', 'German'];
		$expectedKey = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$this->assertEquals($expected[$expectedKey++], trim($row['name']));
		}
	}

	public function testAutoSelectColumns() {
		$actors = $this->db->actor;
		$expected = [
			"SELECT *\nFROM actor",
			"SELECT first_name, last_name\nFROM actor",
		];
		for ($i = 0; $i < 2; ++$i) {
			foreach ($actors as $actor) {
				$actorName = $actor['first_name'] . ' ' . $actor['last_name'];
			}
			$query = $actors->getQuery();
			$this->assertEquals($expected[$i], (string) $query);
			$this->assertTrue($query->isExecuted());
			$query->dropResult();
		}
	}

	public function testReuseAnother() {
		$this->markTestIncomplete('Not implemented yet.');
		$actors = $this->db->actor;
		foreach ($actors as $actor) {
			$actorName = $actor['first_name'] . ' ' . $actor['last_name'];
		}
		$expected = 'SELECT * FROM actor';
		$query = $actors->getQuery();
		$this->assertEquals($expected, (string) $query);
		$this->assertTrue($query->isExecuted());
		// Another query
		$actors = $this->db->actor;
		foreach ($actors as $id => $actor) {
			$actorSurname = $actor['last_name'];
		}
		$expected = 'SELECT * FROM actor';
		$query = $actors->getQuery();
		$this->assertEquals($expected, (string) $query);
		$this->assertFalse($query->isExecuted(), 'Query has not to be executed, because result from another query can be used.');
		return $db;
	}

	public function testReferenceVia() {
		$films = $this->db->film->orderBy(':id');
		$expectedLanguageIds = [-1 => 6, 1 => 1, 1, 1, 1, 1];
		foreach ($films as $filmId => $film) {
			if ($filmId > 5) {
				break;
			}
			$expected = self::$languages[$expectedLanguageIds[$filmId]];
			$this->assertEquals($expected, $film->language->via()->toArray());
		}
	}

	public function testReferenceBackwards() {
		$films = $this->db->film->where(':id = %d', -1);
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


	public function testGetQuery() {
		$this->markTestIncomplete('Not implemented yet.');
		$expected = 'SELECT film.*, language.name, category.name
			FROM film
				JOIN language USING (language_id)
				LEFT JOIN film_category USING (film_id)
				LEFT JOIN category USING (category_id)
			WHERE film.id IN (SELECT id FORM film OFFSET 10 LIMIT 3)
			ORDER BY film.film_id DESC, category.name';
		$films = $this->db->film->where('id IN (SELECT id FORM film OFFSET 10 LIMIT 3)')->order('id DESC');
		$films->join('language')->select('name');
		$films->leftJoin('film_category')->reverse()->leftJoin('category')->select('name')->order('name');
		$this->assertEquals($expected, $films->getQuery());
	}

	public function testTable() {
		$this->markTestIncomplete('Not implemented yet.');
		$expected = [];
		$actual = [];
		// SELECT * FROM film ORDER BY film_id DESC OFFSET 10 LIMIT 3
		// Calls fetchAll to obtain all ID's for use by derived tables. If all derived tables calls wholeTable() iteration will use fetchAssoc instead.
		foreach ($this->db->film->offset(10)->limit(3)->order('id DESC') as $filmId => $film) {
			$actual[$filmId] = $film->toArray();
			// SELECT language_id, name FROM langage WHERE language_id IN (1, 3) -- ids are known from film table
			$actual[$filmId]['language'] = $film->language['name'];
			// SELECT name FROM film_category JOIN category USING (category_id)
			foreach ($film->leftJoin('film_category')->category->order('name')->wholeTable() as $categoryId => $category) {
				$actual[$filmId]['categories'][$categoryId] = $category['name'];
			}
		}
		$this->assertEquals(array_keys($expected), array_keys($actual));
		$this->assertEquals($expected[12], $actual[12]);
		$this->assertEquals($expected[11], $actual[11]);
		$this->assertEquals($expected[10], $actual[10]);
	}

	public function testMultipleRuns() {
		$this->markTestIncomplete('Not implemented yet.');
		for ($i = 0; $i < 3; ++$i) {
			// 1st run: SELECT * FROM film
			// 2nd run: SELECT film_id, title FROM film
			// 3rd run: SELECT film_id, title, description FROM film
			foreach ($this->db->film->order('id DESC')->offset($i * 10)->limit(3) as $filmId => $film) {
				$film['title'];
				if ($i >= 1) {
					// SELECT film_id, description FROM file WHERE film_id IN (...)
					$film['description'];
				}
			}
			// Can reuse same query result because it contains same columns and filters
			$films = $this->db->film->select('title')->order('id DESC')->offset($i * 10)->limit(3);
			foreach ($films as $filmId => $film) {
				$film['title'];
			}
		}
	}

	public function testCacheInvalidation() {
		$this->markTestIncomplete('Not implemented yet.');
		for ($i = 0; $i < 3; ++$i) {
			// 1st run: SELECT * FROM film
			// 2nd run: SELECT film_id, title FROM film
			foreach ($this->db->film->order('id DESC')->offset(10)->limit(3) as $filmId => $film) {
				$film['title'];
			}
			$filmUpdate = ['id' => -1];
			// Because table is modified invalidate cache
			$this->db->film->update($filmUpdate)->where('id = 1');
		}
	}

	public function testCacheStillValid() {
		$this->markTestIncomplete('Not implemented yet.');
		for ($i = 0; $i < 3; ++$i) {
			// 1st run: SELECT * FROM film
			// 2nd run: Reuse result from previous run
			foreach ($this->db->film->order('id')->offset(10)->limit(3) as $filmId => $film) {
				$film['title'];
			}
			$filmInsert = ['title' => 'new', 'language_id' => 1];
			// Because inserted id doesn't affect selected result, result will be still valid
			$this->db->film->insert($filmInsert);
		}
	}
}
