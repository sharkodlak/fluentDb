<?php

namespace Sharkodlak\FluentDb\Query;

class BuilderTest extends \PHPUnit_Framework_TestCase {
	static private $parts = ['SELECT', 'FROM', 'WHERE', 'GROUP BY', 'HAVING', 'ORDER BY', 'OFFSET', 'LIMIT'];
	private $builder;

	public function setUp() {
		$this->builder = new Builder(...self::$parts);
	}

	public function testSelect() {
		$this->assertInstanceOf(Builder::class, $this->builder->select('*'));
		$expected = 'SELECT *';
		$this->assertEquals($expected, (string) $this->builder);
		$columns = [':id', 'name', 'anotherField'];
		$this->builder['SELECT'] = $columns;
		$expected = sprintf('SELECT %s', implode(', ', $columns));
		$this->assertEquals($expected, (string) $this->builder);
		$this->builder->select('COUNT(*)', 'numberOfRows');
		$expected .= ', COUNT(*) AS numberOfRows';
		$this->assertEquals($expected, (string) $this->builder);
		$this->builder['SELECT'] = null;
		$expected = '';
		$this->assertEquals($expected, (string) $this->builder);

	}

	public function testFrom() {
		$this->markTestIncomplete('Not implemented yet.');
		$this->assertInstanceOf(Builder::class, $this->builder->select('*'));
		$expected = 'SELECT *';
		$this->assertEquals($expected, (string) $this->builder);
		$columns = [':id', 'name', 'anotherField'];
		$this->builder['SELECT'] = $columns;
		$expected = sprintf('SELECT %s', implode(', ', $columns));
		$this->assertEquals($expected, (string) $this->builder);
	}
}
