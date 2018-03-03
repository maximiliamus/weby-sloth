<?php
namespace Weby\Sloth;

class ExamplesTest extends \PHPUnit\Framework\TestCase
{
	public function testGroupExample()
	{
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
			->select();
		
		/* Uncomment to perform:
		foreach ($result as $row) {
			foreach ($row as $col) {
				echo $col, "\t";
			}
			echo "\n";
		}
		//*/
		
		// Outputs:
		// one     3
		// two     3
		
		$this->assertTrue(true);
	}
	
	public function testPivotExample()
	{
		$data = array(
			array('foo' => 'one', 'bar' => 'A', 'baz' => 1),
			array('foo' => 'one', 'bar' => 'B', 'baz' => 2),
			array('foo' => 'one', 'bar' => 'C', 'baz' => 3),
			array('foo' => 'two', 'bar' => 'A', 'baz' => 4),
			array('foo' => 'two', 'bar' => 'B', 'baz' => 5),
			array('foo' => 'two', 'bar' => 'C', 'baz' => 6),
		);
		
		$result = Sloth::from($data)
			->pivot('foo', 'baz', 'bar')
			->select();
		
		/* Uncomment to perform:
		foreach ($result as $row) {
			foreach ($row as $col) {
				echo $col, "\t";
			}
			echo "\n";
		}
		//*/
		
		// Outputs:
		// one     1       2       3
		// two     4       5       6
		
		$this->assertTrue(true);
	}
}