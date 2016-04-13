<?php

namespace Sharkodlak\FluentDb\Query;

class BuilderTest extends \PHPUnit_Framework_TestCase {
	static private $parts = ['SELECT', 'FROM', 'WHERE', 'GROUP BY', 'HAVING', 'ORDER BY', 'OFFSET', 'LIMIT'];
	static private $columns = [':id', 'name', 'anotherField'];
	private $builder;

	public function setUp() {
		$this->builder = new Builder(...self::$parts);
	}

	public function testSelect() {
		$this->assertInstanceOf(Builder::class, $this->builder->select('*'));
		$expected = 'SELECT *';
		$this->assertEquals($expected, (string) $this->builder);
		return $this->builder;
	}

	/** @depends testSelect
	 */
	public function testSelectArrayAccess($builder) {
		$builder['SELECT'] = self::$columns;
		$expected = $this->getSelectQuery(self::$columns);
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testSelectArrayAccess
	 */
	public function testSelectAddNamedExpression($builder) {
		$builder->select('COUNT(*)', 'numberOfRows');
		$expected = $this->getSelectQuery(self::$columns) . ', COUNT(*) AS numberOfRows';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	private function getSelectQuery(array $columns) {
		return 'SELECT ' . implode(', ', $columns);
	}

	/** @depends testSelectAddNamedExpression
	 */
	public function testSelectReset($builder) {
		$builder['SELECT'] = null;
		$expected = '';
		$this->assertEquals($expected, (string) $builder);
	}

	public function testFrom() {
		$this->assertInstanceOf(Builder::class, $this->builder->from('someTable'));
		$expected = 'FROM someTable';
		$this->assertEquals($expected, (string) $this->builder);
		return $this->builder;
	}

	/** @depends testFrom
	 */
	public function testFromArrayAccess($builder) {
		$builder['FROM'] = 'anotherTable';
		$expected = 'FROM anotherTable';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testFromArrayAccess
	 */
	public function testFromOverride($builder) {
		$builder->from('overridenTable');
		$expected = 'FROM overridenTable';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}
}
