<?php

namespace Sharkodlak\FluentDb\Query;

class ColumnsTest extends \PHPUnit_Framework_TestCase {
	public function testToString() {
		$columns = new Columns;
		$expected = '';
		$this->assertEquals($expected, (string) $columns);
	}
}
