<?php

namespace Sharkodlak\FluentDb\Structure;

class DefaultConventionTest extends \PHPUnit_Framework_TestCase {
	private $conventions = [];

	public function setUp() {
		$this->conventions['default'] = new DefaultConvention;
		$this->conventions['redefined'] = new DefaultConvention('id_%s', 'id_%s', 'prefix_%ss');
		$this->conventions['special'] = new SpecialConvention('%s_id');
	}

	/**
	 * @dataProvider getPrimaryKeyProvider
	 */
	public function testGetPrimaryKey($conventionKey, $table, $expected) {
		$convention = $this->conventions[$conventionKey];
		$this->assertEquals($expected, $convention->getPrimaryKey($table));
	}

	public function getPrimaryKeyProvider() {
		return [
			'default' => ['default', 'film', 'id'],
			'redefined' => ['redefined', 'film', 'id_film'],
			'special' => ['special', 'film', 'film_id'],
		];
	}

	/**
	 * @dataProvider getForeignKeyProvider
	 */
	public function testGetForeignKey($conventionKey, $table, $expected) {
		$convention = $this->conventions[$conventionKey];
		$this->assertEquals($expected, $convention->getForeignKey($table));
	}

	public function getForeignKeyProvider() {
		return [
			'default' => ['default', 'language', 'language_id'],
			'redefined' => ['redefined', 'language', 'id_language'],
			'special' => ['special', 'language', 'language_id'],
		];
	}

	/**
	 * @dataProvider getTableNameProvider
	 */
	public function testGetTableName($conventionKey, $table, $expected) {
		$convention = $this->conventions[$conventionKey];
		$this->assertEquals($expected, $convention->getTableName($table));
	}

	public function getTableNameProvider() {
		return [
			'default' => ['default', 'film', 'film'],
			'redefined' => ['redefined', 'film', 'prefix_films'],
			'special' => ['special', 'film', 'film'],
		];
	}
}
