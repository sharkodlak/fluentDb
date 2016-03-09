<?php

namespace Sharkodlak\FluentDb;

class Row implements \ArrayAccess, \IteratorAggregate {
	private $data;
	private $table;

	public function __construct(Table $table, array $data) {
		$this->table = $table;
		$this->data = $data;
	}

	public function offsetExists($offset) {
		return array_key_exists($offset, $this->data);
	}

	public function offsetGet($offset) {
		return $this->data[$offset];
	}

	public function offsetSet($offset, $value) {
		$this->data[$offset] = $value;
	}

	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}

	public function getIterator() {
		return new \ArrayIterator($this->data);
	}

	public function toArray() {
		return $this->data;
	}
}
