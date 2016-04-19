<?php

namespace Sharkodlak\FluentDb\Query\Parts;

abstract class Parts  {
	private $data;

	public function __construct(array $data) {
		$this->data = $data;
	}

	public function __debugInfo() {
		return [
			'parts' => $this->__toString(),
		];
	}

	public function __toString() {
		return implode($this->getGlue(), $this->data);
	}

	public function merge($data) {
		$merged = array_merge($this->data, $data);
		return new static($merged);
	}

	public function mergeLast($data) {
		$last = end($this->data);
		$lastKey = key($this->data);
		$collection = $this->data;
		$collection[$lastKey] = $last->merge($data);
		return new static($collection);
	}

	abstract public function getGlue();
}
