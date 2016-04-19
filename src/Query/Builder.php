<?php

namespace Sharkodlak\FluentDb\Query;

class Builder implements \ArrayAccess {
	private $parts = [];

	public function __construct(...$parts) {
		foreach ($parts as $part) {
			$this->parts[$part] = $this->envelopeKnownPart($part);
		}
	}

	public function orderBy($column) {
		$part = 'ORDER BY';
		return $this->setPart($part, $column);
	}

	public function select($expression, $name = null) {
		$part = 'SELECT';
		if ($name !== null) {
			$expression = sprintf('%s AS %s', $expression, $name);
		}
		return $this->setPart($part, $expression);
	}

	public function __call($name, $args) {
		$part = strtoupper($name);
		$value = sprintf(...$args);
		return $this->setPart($part, $value);
	}

	public function offsetSet($part, $value) {
		$mergeWithPrevious = false;
		return $this->setPart($part, $value, $mergeWithPrevious);
	}

	private function setPart($part, $value, $mergeWithPrevious = true) {
		$realPart = $part === 'OR'
			? 'WHERE'
			: $part;
		if (!array_key_exists($realPart, $this->parts)) {
			throw new \Sharkodlak\Exception\IllegalArgumentException('Unknown query part');
		}
		$this->parts[$realPart] = $this->envelopeKnownPart($part, $value, $mergeWithPrevious);
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
			case 'WHERE':
				$value = (array) $value;
				if ($mergeWithPrevious && array_key_exists($part, $this->parts)) {
					return $this->parts[$part]->mergeLast($value);
				}
				return new Parts\PartsOr([new Parts\PartsAnd($value)]);
			case 'OR':
				$value = (array) $value;
				return $this->parts['WHERE']->merge([new Parts\PartsAnd($value)]);
			default:
				return $value;
		}
	}

	public function offsetExists($part) {
		return array_key_exists($part, $this->parts);
	}

	public function offsetGet($part) {
		return $this->parts[$part];
	}

	public function offsetUnset($part) {
		$this->parts[$part] = null;
	}

	public function __toString() {
		$parts = [];
		foreach ($this->parts as $part => $value) {
			if (isset($value)) {
				var_dump($part, (string) $value, $value);
				$value = (string) $value;
				if ($value !== '') {
					$parts[] = $part . ' ' . $value;
				}
			}
		}
		return implode("\n", $parts);
	}
}
