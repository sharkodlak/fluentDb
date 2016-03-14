<?php

namespace Sharkodlak\FluentDb\Query;

abstract class Parts extends \ArrayObject {
	abstract public function getGlue();

	public function __toString() {
		return implode($this->getGlue(), $this->getArrayCopy());
	}
}
