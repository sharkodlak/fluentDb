<?php

namespace Sharkodlak\FluentDb\Query;

class Builder implements \ArrayAccess {
	private $parts = [];

	public function __construct(...$parts) {
		foreach ($parts as $part) {
			$realPart = $part;
			if (in_array($part, ['UNION', 'INTERSECT', 'EXCEPT'])) {
				$realPart = 'COMBINE';
			}
			$this->parts[$realPart] = $this->envelopeKnownPart($part);
		}
	}

	public function groupBy($column) {
		$part = 'GROUP BY';
		return $this->setPart($part, $column);
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
		switch ($part) {
			case 'HAVINGOR':
				$realPart = 'HAVING';
				break;
			case 'OR':
				$realPart = 'WHERE';
				break;
			case 'EXCEPT':
			case 'INTERSECT':
			case 'UNION':
				$realPart = 'COMBINE';
				break;
			default:
				$realPart = $part;
				break;
		}
		if (!array_key_exists($realPart, $this->parts)) {
			$msg = "Unknown query part '$realPart'";
			throw new \Sharkodlak\Exception\IllegalArgumentException($msg);
		}
		$this->parts[$realPart] = $this->envelopeKnownPart($part, $value, $mergeWithPrevious);
		return $this;
	}

	private function envelopeKnownPart($part, $value = null, $mergeWithPrevious = true) {
		switch ($part) {
			case 'SELECT':
			case 'GROUP BY':
			case 'ORDER BY':
				$value = (array) $value;
				return $mergeWithPrevious && array_key_exists($part, $this->parts)
					? $this->parts[$part]->merge($value)
					: new Parts\PartsComma($value);
			case 'HAVING':
			case 'WHERE':
				$value = (array) $value;
				if ($mergeWithPrevious && array_key_exists($part, $this->parts)) {
					return $this->parts[$part]->mergeLast($value);
				}
				return new Parts\PartsOr([new Parts\PartsAnd($value)]);
			case 'HAVINGOR':
				$value = (array) $value;
				return $this->parts['HAVING']->merge([new Parts\PartsAnd($value)]);
			case 'OR':
				$value = (array) $value;
				return $this->parts['WHERE']->merge([new Parts\PartsAnd($value)]);
			case 'EXCEPT':
			case 'INTERSECT':
			case 'UNION':
				$realPart = 'COMBINE';
				$className = __NAMESPACE__ . '\\Parts\\' . ucfirst(strtolower($part));
				return $mergeWithPrevious && array_key_exists($realPart, $this->parts)
					? $this->parts[$realPart]->merge([new $className($value)])
					: new Parts\Combine($value);
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
				$value = (string) $value;
				if ($value !== '') {
					$parts[] = $part . ' ' . $value;
				}
			}
		}
		return implode("\n", $parts);
	}
}
