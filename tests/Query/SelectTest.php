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
		$factory = $this->getMockBuilder(\Sharkodlak\FluentDb\Factory\Factory::class)
			->getMock();
		$queryBuilder = $this->getMockBuilder(\Sharkodlak\FluentDb\Query\Builder::class)
			->disableOriginalConstructor()
			->getMock();
		$queryBuilder->expects($this->once())
			->method('from')
			->will($this->returnSelf());
		$queryBuilder->expects($this->once())
			->method('__toString')
			->will($this->returnValue('SELECT * FROM :table'));
		$factory->expects($this->once())
			->method('getQueryBuilder')
			->will($this->returnValue($queryBuilder));
		$table->expects($this->once())
			->method('getFactory')
			->will($this->returnValue($factory));
		$query = new Select($table);
		$this->assertInstanceOf(TableQuery::class, $query);
		$expected = 'SELECT * FROM test_table';
		$this->assertEquals($expected, (string) $query);
	}
}
