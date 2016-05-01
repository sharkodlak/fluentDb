<?php

namespace Sharkodlak\FluentDb\Query;

class BuilderTest extends \PHPUnit_Framework_TestCase {
	static private $parts = ['SELECT', 'FROM', 'WHERE', 'GROUP BY', 'HAVING', 'UNION', 'INTERSECT', 'EXCEPT', 'ORDER BY', 'LIMIT', 'OFFSET'];
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

	public function testWhere() {
		$this->assertInstanceOf(Builder::class, $this->builder->where('false'));
		$expected = 'WHERE false';
		$this->assertEquals($expected, (string) $this->builder);
		return $this->builder;
	}

	/** @depends testWhere
	 */
	public function testWhereArrayAccess($builder) {
		$builder['WHERE'] = 'true';
		$expected = 'WHERE true';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testWhereArrayAccess
	 */
	public function testWhereAdd($builder) {
		$builder->where('42 > 7');
		$expected = 'WHERE true AND 42 > 7';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testWhereAdd
	 */
	public function testWhereAddOr($builder) {
		$builder->or(':id %% 2');
		$expected = 'WHERE true AND 42 > 7 OR :id % 2';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testWhereAddOr
	 */
	public function testWhereReset($builder) {
		$builder['WHERE'] = null;
		$expected = '';
		$this->assertEquals($expected, (string) $builder);
	}

	public function testGroupBy() {
		$this->assertInstanceOf(Builder::class, $this->builder->groupBy(1));
		$expected = 'GROUP BY 1';
		$this->assertEquals($expected, (string) $this->builder);
		return $this->builder;
	}

	/** @depends testGroupBy
	 */
	public function testGroupByArrayAccess($builder) {
		$builder['GROUP BY'] = 'language_id';
		$expected = 'GROUP BY language_id';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testGroupByArrayAccess
	 */
	public function testGroupByAdd($builder) {
		$builder->groupBy('year');
		$expected = 'GROUP BY language_id, year';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testGroupByAdd
	 */
	public function testGroupByReset($builder) {
		$builder['GROUP BY'] = null;
		$expected = '';
		$this->assertEquals($expected, (string) $builder);
	}

	public function testHaving() {
		$this->assertInstanceOf(Builder::class, $this->builder->having('false'));
		$expected = 'HAVING false';
		$this->assertEquals($expected, (string) $this->builder);
		return $this->builder;
	}

	/** @depends testHaving
	 */
	public function testHavingArrayAccess($builder) {
		$builder['HAVING'] = 'true';
		$expected = 'HAVING true';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testHavingArrayAccess
	 */
	public function testHavingAdd($builder) {
		$builder->having('42 > 7');
		$expected = 'HAVING true AND 42 > 7';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testHavingAdd
	 */
	public function testHavingAddOr($builder) {
		$builder->havingOr(':id %% 2');
		$expected = 'HAVING true AND 42 > 7 OR :id % 2';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testHavingAddOr
	 */
	public function testHavingReset($builder) {
		$builder['HAVING'] = null;
		$expected = '';
		$this->assertEquals($expected, (string) $builder);
	}

	public function testOrderBy() {
		$this->assertInstanceOf(Builder::class, $this->builder->orderBy(1));
		$expected = 'ORDER BY 1';
		$this->assertEquals($expected, (string) $this->builder);
		return $this->builder;
	}

	/** @depends testOrderBy
	 */
	public function testOrderByArrayAccess($builder) {
		$builder['ORDER BY'] = 'language_id';
		$expected = 'ORDER BY language_id';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testOrderByArrayAccess
	 */
	public function testOrderByAdd($builder) {
		$builder->orderBy('year');
		$expected = 'ORDER BY language_id, year';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testOrderByAdd
	 */
	public function testOrderByReset($builder) {
		$builder['ORDER BY'] = null;
		$expected = '';
		$this->assertEquals($expected, (string) $builder);
	}

	public function testLimit() {
		$this->assertInstanceOf(Builder::class, $this->builder->limit(123));
		$expected = 'LIMIT 123';
		$this->assertEquals($expected, (string) $this->builder);
		return $this->builder;
	}

	/** @depends testLimit
	 */
	public function testLimitArrayAccess($builder) {
		$builder['LIMIT'] = 456;
		$expected = 'LIMIT 456';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testLimitArrayAccess
	 */
	public function testLimitOverride($builder) {
		$builder->limit(235);
		$expected = 'LIMIT 235';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testLimitOverride
	 */
	public function testLimitReset($builder) {
		$builder['LIMIT'] = null;
		$expected = '';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testLimitReset
	 */
	public function testLimitWrongNumber($builder) {
		$this->expectException(\Sharkodlak\Exception\IllegalArgumentException::class);
		$builder->limit(-7);
	}

	public function testOffset() {
		$this->assertInstanceOf(Builder::class, $this->builder->offset(123));
		$expected = 'OFFSET 123';
		$this->assertEquals($expected, (string) $this->builder);
		return $this->builder;
	}

	/** @depends testOffset
	 */
	public function testOffsetArrayAccess($builder) {
		$builder['OFFSET'] = 456;
		$expected = 'OFFSET 456';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testOffsetArrayAccess
	 */
	public function testOffsetOverride($builder) {
		$builder->offset(235);
		$expected = 'OFFSET 235';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testOffsetOverride
	 */
	public function testOffsetReset($builder) {
		$builder['OFFSET'] = null;
		$expected = '';
		$this->assertEquals($expected, (string) $builder);
		return $builder;
	}

	/** @depends testOffsetReset
	 */
	public function testOffsetWrongNumber($builder) {
		$this->expectException(\Sharkodlak\Exception\IllegalArgumentException::class);
		$builder->offset(-7);
	}
}
