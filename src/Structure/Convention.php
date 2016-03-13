<?php

namespace Sharkodlak\FluentDb\Structure;

interface Convention {
	public function getPrimaryKey($tableName);
	public function getForeignKey($tableName);
	public function getTableName($tableName);
	public function getCacheKeyTableColumns($tableName);
}
