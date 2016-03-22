<?php

namespace Sharkodlak\FluentDb\Query;

class Builder implements \ArrayAccess {
	private $parts;

	public function __construct(array $parts) {
		$this->parts = $parts;
	}

	public function offsetExists($offset) {
		return array_key_exists($offset, $this->parts);
	}

	public function offsetGet($offset) {
		return $this->parts[$offset];
	}

	public function __call($name, $args) {
		$upperCaseName = strtoupper($name);
		$argString = sprintf(...$args);
		return $this->offsetSet($upperCaseName, $argString);
	}

	public function from($table) {
		return $this->offsetSet('FROM', $table);
	}

	public function offsetSet($offset, $value) {
		switch ($offset) {
			case 'SELECT':
				$value = new Parts\PartsComma((array) $value);
				break;
		}
		$this->parts[$offset] = $value;
		return $this;
	}

	public function offsetUnset($offset) {
		$this->parts[$offset] = null;
	}

	public function __toString() {
		$parts = [];
		foreach ($this->parts as $part => $value) {
			if (isset($value)) {
				$parts[] = $part . ' ' . (string) $value;
			}
		}
		return implode("\n", $parts);
	}
}
