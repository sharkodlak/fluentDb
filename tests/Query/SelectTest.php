<?php

namespace Sharkodlak\FluentDb\Query;

class SelectTest extends \PHPUnit_Framework_TestCase {
	public function testConstructor() {
		$table = $this->getMockBuilder(\Sharkodlak\FluentDb\Table::class)
			->disableOriginalConstructor()
			->getMock();
		$translations = [':table' => 'test_table'];
		$table->expects($this->once())
			->method('getPlaceholderTranslations')
			->will($this->returnValue($translations));
		$query = new Select($table);
		$this->assertInstanceOf(TableQuery::class, $query);
		$expected = 'SELECT * FROM test_table';
		$this->assertEquals($expected, (string) $query);
	}
}
