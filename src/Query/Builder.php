<?php

namespace Sharkodlak\FluentDb\Query;

class Builder implements \ArrayAccess {
	private $parts = [];

	public function __construct(...$parts) {
		foreach ($parts as $part) {
			$this->parts[$part] = $this->envelopeKnownPart($part);
		}
	}

	public function from($table) {
		return $this->offsetSet('FROM', $table);
	}

	public function offsetSet($offset, $value) {
		$mergeWithPrevious = false;
		$this->parts[$offset] = $this->envelopeKnownPart($offset, $value, $mergeWithPrevious);
		return $this;
	}

	public function __call($name, $args) {
		$upperCaseName = strtoupper($name);
		$argString = sprintf(...$args);
		$this->parts[$upperCaseName] = $this->envelopeKnownPart($upperCaseName, $argString);
		return $this;
	}

	public function orderBy($column) {
		$part = 'ORDER BY';
		$this->parts[$part] = $this->envelopeKnownPart($part, $column);
		return $this;
	}

	public function select($expression, $name = null) {
		$part = 'SELECT';
		if ($name !== null) {
			$expression = sprintf('%s AS %s', $expression, $name);
		}
		$this->parts[$part] = $this->envelopeKnownPart($part, $expression);
		return $this;
	}

	private function envelopeKnownPart($part, $value = null, $mergeWithPrevious = true) {
		switch ($part) {
			case 'SELECT':
			case 'ORDER BY':
				$value = (array) $value;
				return $mergeWithPrevious && array_key_exists($part, $this->parts)
					? $this->parts[$part]->merge($value)
					: new Parts\PartsComma($value);
			default:
				return $value;
		}
	}

	public function offsetExists($offset) {
		return array_key_exists($offset, $this->parts);
	}

	public function offsetGet($offset) {
		return $this->parts[$offset];
	}

	public function offsetUnset($offset) {
		$this->parts[$offset] = null;
	}

	public function __toString() {
		$parts = [];
		foreach ($this->parts as $part => $value) {
			if (isset($value)) {
				$value = (string) $value;
				if ($value !== '') {
					$parts[] = $part . ' ' . $value;
				}
			}
		}
		return implode("\n", $parts);
	}
}
