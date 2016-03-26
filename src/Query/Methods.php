<?php

namespace Sharkodlak\FluentDb\Query;

trait Methods {
	abstract protected function getQueryBuilder();

	public function orderBy(...$args) {
		$this->getQueryBuilder()->orderBy(...$args);
		return $this;
	}

	public function where(...$args) {
		$this->getQueryBuilder()->where(...$args);
		return $this;
	}
}
