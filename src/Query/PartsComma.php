<?php

namespace Sharkodlak\FluentDb\Query;

class PartsComma extends Parts {
	public function getGlue() {
		return ', ';
	}
}
