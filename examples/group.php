<?php
require_once '../vendor/autoload.php';

use Weby\Sloth\Sloth;

include_once 'data.php';

Sloth::from($data)
	->group('foo', 'baz')
	->count()
	->sum()
	->avg()
	->print();

// Outputs:
// foo      count   sum      avg
// one......3.......6........2
// two......3.......15.......5
