<?php

namespace Sharkodlak\FluentDb\Query\Parts;

class PartsOr extends Parts {
	public function getGlue() {
		return ' OR ';
	}
}
