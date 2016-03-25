<?php

namespace Sharkodlak\FluentDb\Structure;

interface Convention {
	public function getPrimaryKey($tableName);
	public function getForeignKey($destinationTableName, $sourceTableName = null);
	public function getTableName($tableName);
	public function getCacheKeyTableColumns($tableName);
}
