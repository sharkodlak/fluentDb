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

	abstract public function getGlue();
}
