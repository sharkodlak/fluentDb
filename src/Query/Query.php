<?php

namespace Sharkodlak\FluentDb\Query;

interface Query {
	public function __toString();
	public function dropResult();
	public function executeOnce();
	public function getResult();
	public function isExecuted();
}
