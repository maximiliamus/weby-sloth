<?php
require_once dirname(__FILE__) . '/../vendor/autoload.php';

use \Weby\Sloth\Sloth;

include_once 'data.php';

Sloth::from($data)
	->group('foo', 'baz')
	->count()
	->sum()
	->avg()
	->print();

// Outputs:
// one......3.......6........2
// two......3.......15.......5
