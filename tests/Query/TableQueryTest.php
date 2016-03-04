<?php

namespace Sharkodlak\FluentDb\Query;

class TableQueryTest extends \PHPUnit_Framework_TestCase {
	public function test__toString() {
		$parts = ['SELECT', '*', 'FROM', '%s'];
		$query = $this->getMockBuilder('Sharkodlak\\FluentDb\\Query\\TableQuery')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		$query->expects($this->once())
			->method('getParts')
			->will($this->returnValue($parts));
		$expected = 'SELECT * FROM %s';
		$this->assertEquals($expected, (string) $query);
	}

	public function testExecution() {
		$statement = $this->getMockBuilder('PDOStatement')
			->disableOriginalConstructor()
			->getMock();
		$statement->expects($this->once())
			->method('execute')
			->will($this->returnValue(true));
		$table = $this->getMockBuilder('Sharkodlak\\FluentDb\\Table')
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->any())
			->method('query')
			->will($this->returnValue($statement));
		$parts = ['SELECT', '*', 'FROM', '%s'];
		$query = $this->getMockBuilder('Sharkodlak\\FluentDb\\Query\\TableQuery')
			->setConstructorArgs([$table])
			->getMockForAbstractClass();
		$query->expects($this->any())
			->method('getParts')
			->will($this->returnValue($parts));
		$this->assertFalse($query->isExecuted());
		$query->executeOnce();
		$this->assertTrue($query->isExecuted());
	}
}
