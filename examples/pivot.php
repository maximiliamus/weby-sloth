<?php
require_once dirname(__FILE__) . '/../vendor/autoload.php';

use \Weby\Sloth\Sloth;

include_once 'data.php';

Sloth::from($data)
	->pivot('foo', 'bar', 'baz')
	->print();

// Outputs:
// one     1       2       3
// two     4       5       6
