<?php

namespace Sharkodlak\FluentDb\Query\Parts;

class PartsAnd extends Parts {
	public function getGlue() {
		return ' AND ';
	}
}
