<?php

namespace Sharkodlak\FluentDb\Query;

class ColumnsTest extends \PHPUnit_Framework_TestCase {
	public function testEmptyString() {
		$columns = new Columns;
		$expected = '';
		$this->assertEquals($expected, (string) $columns);
	}

	public function testSet() {
		$columns = new Columns;
		$columns->set(['first', 'second', 'third' => 'MD5(data)']);
		$expected = 'first, second, MD5(data) AS third';
		$this->assertEquals($expected, (string) $columns);
	}
}
