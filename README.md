## Weby\Sloth

This PHP's library provides simple data manipulaton tools and may be used
for simple data analysis, transforming and reporting. For example, it provides
such operations for input data as "group by", "pivot" and additional aggregate
functions that may be applied to this operations.

## Installation

```bash
composer require weby/sloth:v0.1
```

## Usage

See [examples](https://github.com/maximiliamus/weby-sloth/tree/master/examples) folder for more info.

### Input data

```php
// examples/data.php:
$data = [
    ['foo' => 'one', 'bar' => 'A', 'baz' => 1],
    ['foo' => 'one', 'bar' => 'B', 'baz' => 2],
    ['foo' => 'one', 'bar' => 'C', 'baz' => 3],
    ['foo' => 'two', 'bar' => 'A', 'baz' => 4],
    ['foo' => 'two', 'bar' => 'B', 'baz' => 5],
    ['foo' => 'two', 'bar' => 'C', 'baz' => 6],
];
```

### "Goup" operation

```php
// examples/group.php:
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
// one      3       6        2
// two      3       15       5
```

### "Pivot" operation

```php
// examples/pivot.php:
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
```

## Tests

Running the tests is simple:

```bash
vendor/bin/phpunit
```

## Documentation

The documentation is located in this repo's [wiki](https://github.com/maximiliamus/weby-sloth/wiki/Documentation).

## License

Weby\Sloth is distributed under the MIT license.
