<?php
require_once dirname(__FILE__) . '/../vendor/autoload.php';

use \Weby\Sloth\Sloth;

include_once 'data.php';

Sloth::from($data)
	->group('foo', 'baz')
	->count()
	->sum()
	->print();

// Outputs:
// one......3.......6
// two......3.......15
