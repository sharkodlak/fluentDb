<?php

namespace Sharkodlak\FluentDb\Query;

interface Query {
	public function __toString();
	public function getResult();
	public function executeOnce();
	public function isExecuted();
}
