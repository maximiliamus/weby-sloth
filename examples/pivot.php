<?php
require_once '../vendor/autoload.php';

use Weby\Sloth\Sloth;

include_once 'data.php';

Sloth::from($data)
	->pivot('foo', 'bar', 'baz')
	->print();

// Outputs:
// foo     A       B       C
// one     1       2       3
// two     4       5       6
