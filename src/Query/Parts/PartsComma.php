<?php

namespace Sharkodlak\FluentDb\Query\Parts;

class PartsComma extends Parts {
	public function getGlue() {
		return ', ';
	}
}
