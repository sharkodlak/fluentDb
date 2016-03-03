<?php

namespace Sharkodlak\FluentDb\Query;

class ColumnsTest extends \PHPUnit_Framework_TestCase {
	public function testEmptyString() {
		$columns = new Columns;
		$expected = '';
		$this->assertEquals($expected, (string) $columns);
	}

	public function testAdd() {
		$columns = new Columns;
		$actual = $columns->add('first');
		$this->assertSame($columns, $actual);
		$columns->add('second')
			->add('third', 'MD5(data)');
		$expected = 'first, second, MD5(data) AS third';
		$this->assertEquals($expected, (string) $columns);
	}

	public function testSet() {
		$columns = new Columns;
		$actual = $columns->set(['first', 'second', 'third' => 'MD5(data)']);
		$this->assertSame($columns, $actual);
		$expected = 'first, second, MD5(data) AS third';
		$this->assertEquals($expected, (string) $columns);
	}
}
