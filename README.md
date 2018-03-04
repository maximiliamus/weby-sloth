## Weby\Sloth

This PHP's library provides simple data manipulaton tools and may be used
for simple data analysis, transforming and reporting. For example, it provides
such operations for input data as "group by", "pivot" and additional aggregate
functions that may be applied to this operations.

## Installation

TBD

## Usage

### "Goup" operation

```php
namespace Weby\Sloth;

$data = array(
    array('foo' => 'one', 'bar' => 'A', 'baz' => 1),
    array('foo' => 'one', 'bar' => 'B', 'baz' => 2),
    array('foo' => 'one', 'bar' => 'C', 'baz' => 3),
    array('foo' => 'two', 'bar' => 'A', 'baz' => 4),
    array('foo' => 'two', 'bar' => 'B', 'baz' => 5),
    array('foo' => 'two', 'bar' => 'C', 'baz' => 6),
);

$result = Sloth::from($data)
    ->group('foo', 'baz')
    ->count()
    ->sum()
    ->select();

foreach ($result as $row) {
    foreach ($row as $col) {
        echo $col, "\t";
    }
    echo "\n";
}

// Outputs:
// one     3       6
// two     3       15
```

### "Pivot" operation

```php
namespace Weby\Sloth;

$data = array(
    array('foo' => 'one', 'bar' => 'A', 'baz' => 1),
    array('foo' => 'one', 'bar' => 'B', 'baz' => 2),
    array('foo' => 'one', 'bar' => 'C', 'baz' => 3),
    array('foo' => 'two', 'bar' => 'A', 'baz' => 4),
    array('foo' => 'two', 'bar' => 'B', 'baz' => 5),
    array('foo' => 'two', 'bar' => 'C', 'baz' => 6),
);

$result = Sloth::from($data)
    ->pivot('foo', 'bar', 'baz')
    ->select();

foreach ($result as $row) {
    foreach ($row as $col) {
        echo $col, "\t";
    }
    echo "\n";
}

// Outputs:
// one     1       2       3
// two     4       5       6
```

## Tests

Running the tests is simple:

```bash
vendor/bin/phpunit
```

## License

Weby\Sloth is distributed under the MIT license.

