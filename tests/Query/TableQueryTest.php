<?php

namespace Sharkodlak\FluentDb\Query;

class TableQueryTest extends \PHPUnit_Framework_TestCase {
	public function test__toString() {
		$table = $this->getMockBuilder(\Sharkodlak\FluentDb\Table::class)
			->disableOriginalConstructor()
			->getMock();
		$translations = [':table' => 'test_table'];
		$table->expects($this->once())
			->method('getPlaceholderTranslations')
			->will($this->returnValue($translations));
		$query = $this->getMockBuilder(TableQuery::class)
			->setConstructorArgs([$table])
			->setMethods(['getBuilder'])
			->getMockForAbstractClass();
		$builder = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->getMock();
		$builder->expects($this->once())
			->method('__toString')
			->will($this->returnValue("SELECT * FROM :table"));
		$query->expects($this->once())
			->method('getBuilder')
			->will($this->returnValue($builder));
		$expected = 'SELECT * FROM test_table';
		$this->assertEquals($expected, (string) $query);
	}

	public function testExecution() {
		$statement = $this->getMockBuilder('PDOStatement')
			->disableOriginalConstructor()
			->getMock();
		$statement->expects($this->once())
			->method('execute')
			->will($this->returnValue(true));
		$table = $this->getMockBuilder(\Sharkodlak\FluentDb\Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('query')
			->will($this->returnValue($statement));
		$translations = [':table' => 'test_table'];
		$table->expects($this->any())
			->method('getPlaceholderTranslations')
			->will($this->returnValue($translations));
		$query = $this->getMockBuilder(TableQuery::class)
			->setConstructorArgs([$table])
			->setMethods(['getBuilder'])
			->getMockForAbstractClass();
		$builder = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->getMock();
		$builder->expects($this->once())
			->method('__toString')
			->will($this->returnValue("SELECT * FROM :table"));
		$query->expects($this->once())
			->method('getBuilder')
			->will($this->returnValue($builder));
		$this->assertFalse($query->isExecuted());
		$this->assertInstanceOf('PDOStatement', $query->executeOnce());
		$this->assertTrue($query->isExecuted());
		$this->expectException(\Sharkodlak\Exception\IllegalStateException::class);
		$query->executeOnce();
	}

	public function testGetResult() {
		$statement = $this->getMockBuilder('PDOStatement')
			->disableOriginalConstructor()
			->getMock();
		$statement->expects($this->once())
			->method('execute')
			->will($this->returnValue(true));
		$table = $this->getMockBuilder(\Sharkodlak\FluentDb\Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('query')
			->will($this->returnValue($statement));
		$translations = [':table' => 'test_table'];
		$table->expects($this->any())
			->method('getPlaceholderTranslations')
			->will($this->returnValue($translations));
		$query = $this->getMockBuilder(TableQuery::class)
			->setConstructorArgs([$table])
			->setMethods(['getBuilder'])
			->getMockForAbstractClass();
		$builder = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->getMock();
		$builder->expects($this->once())
			->method('__toString')
			->will($this->returnValue("SELECT * FROM :table"));
		$query->expects($this->once())
			->method('getBuilder')
			->will($this->returnValue($builder));
		$this->assertFalse($query->isExecuted());
		$this->assertInstanceOf('PDOStatement', $query->getResult());
		$this->assertTrue($query->isExecuted());
		$this->assertInstanceOf('PDOStatement', $query->getResult());
		$this->assertTrue($query->isExecuted());
	}

	public function testExecutionError() {
		$statement = $this->getMockBuilder('PDOStatement')
			->disableOriginalConstructor()
			->getMock();
		$statement->expects($this->once())
			->method('execute')
			->will($this->returnValue(false));
		$table = $this->getMockBuilder(\Sharkodlak\FluentDb\Table::class)
			->disableOriginalConstructor()
			->getMock();
		$table->expects($this->once())
			->method('query')
			->will($this->returnValue($statement));
		$translations = [':table' => 'test_table'];
		$table->expects($this->any())
			->method('getPlaceholderTranslations')
			->will($this->returnValue($translations));
		$query = $this->getMockBuilder(TableQuery::class)
			->setConstructorArgs([$table])
			->getMockForAbstractClass();
		$this->assertFalse($query->isExecuted());
		$this->expectException(\Exception::class);
		$query->executeOnce();
	}
}
