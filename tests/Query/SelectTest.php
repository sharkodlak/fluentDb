<?php

namespace Sharkodlak\FluentDb\Query;

class SelectTest extends \PHPUnit_Framework_TestCase {
	public function testConstructor() {
		$table = $this->getMockBuilder(\Sharkodlak\FluentDb\Table::class)
			->disableOriginalConstructor()
			->getMock();
		$instance = new Select($table);
		$this->assertInstanceOf(TableQuery::class, $instance);
	}

	public function testGetParts() {
		$table = $this->getMockBuilder(\Sharkodlak\FluentDb\Table::class)
			->disableOriginalConstructor()
			->getMock();
		$query = new Select($table);
		$expected = ['SELECT', '*', 'FROM', '%s'];
		$actual = $query->getParts();
		$this->assertEquals($expected, array_values($actual));
	}
}
