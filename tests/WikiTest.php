<?php
namespace Weby\Sloth;

class WikiTest extends \PHPUnit\Framework\TestCase
{
	public function providerArrayData()
	{
		return
		[
			[
				[
					['one', 'A',  1],
					['one', null, null],
					['one', 'B',  2],
					['one', 'C',  3],
					['two', 'A',  4],
					['two', null, null],
					['two', 'B',  5],
					['two', 'C',  6],
					['two', 'D',  7],
				]
			]
		];
	}
	
	public function providerAssocData()
	{
		return
		[
			[
				[
					['foo' => 'one', 'bar' => 'A',  'baz' => 1],
					['foo' => 'one', 'bar' => null, 'baz' => null],
					['foo' => 'one', 'bar' => 'B',  'baz' => 2],
					['foo' => 'one', 'bar' => 'C',  'baz' => 3],
					['foo' => 'two', 'bar' => 'A',  'baz' => 4],
					['foo' => 'two', 'bar' => null, 'baz' => null],
					['foo' => 'two', 'bar' => 'B',  'baz' => 5],
					['foo' => 'two', 'bar' => 'C',  'baz' => 6],
					['foo' => 'two', 'bar' => 'D',  'baz' => 7],
				]
			]
		];
	}
	
	public function providerObjectData()
	{
		return
		[
			[
				[
					(object) ['foo' => 'one', 'bar' => 'A',  'baz' => 1],
					(object) ['foo' => 'one', 'bar' => null, 'baz' => null],
					(object) ['foo' => 'one', 'bar' => 'B',  'baz' => 2],
					(object) ['foo' => 'one', 'bar' => 'C',  'baz' => 3],
					(object) ['foo' => 'two', 'bar' => 'A',  'baz' => 4],
					(object) ['foo' => 'two', 'bar' => null, 'baz' => null],
					(object) ['foo' => 'two', 'bar' => 'B',  'baz' => 5],
					(object) ['foo' => 'two', 'bar' => 'C',  'baz' => 6],
					(object) ['foo' => 'two', 'bar' => 'D',  'baz' => 7],
				]
			]
		];
	}
	
	private $testResults = [
		'count' => 2,
		'input' => [
			0 => ['one', 'A', 3],
			1 => ['two', 'A', 4]
		],
		'groupNames' => ['one', 'two'],
		'pivotFirst' => [
			0 => [1, 2, 3, null],
			1 => [4, 5, 6, 7]
		],
		'pivotCount' => [
			0 => [1, 1, 1, null],
			1 => [1, 1, 1, 1]
		],
		'accum' => [
			[1, 2, 3],
			[4, 5, 6, 7]
		],
		'avg' => [
			2,
			5.5
		],
		'concat' => [
			'123',
			'4567'
		],
		'countRows' => [4, 5],
		'countValues' => [3, 4],
	];
	
	/**
	 * @dataProvider providerArrayData
	 */
	public function testGroup_InputArray($data)
	{
		$result = Sloth::from($data)
			->group(0, [1, 2])
			->first(1)
			->count(2)
			->fetch();
		
		$this->assertEquals(true, count($result) == $this->testResults['count']);
		$this->assertEquals(true, $result[0][0] == $this->testResults['input'][0][0]);
		$this->assertEquals(true, $result[0][1] == $this->testResults['input'][0][1]);
		$this->assertEquals(true, $result[0][2] == $this->testResults['input'][0][2]);
		$this->assertEquals(true, $result[1][0] == $this->testResults['input'][1][0]);
		$this->assertEquals(true, $result[1][1] == $this->testResults['input'][1][1]);
		$this->assertEquals(true, $result[1][2] == $this->testResults['input'][1][2]);
	}
	
	/**
	 * @dataProvider providerAssocData
	 */
	public function testGroup_InputAssoc($data)
	{
		$result = Sloth::from($data)
			->group('foo', ['bar', 'baz'])
			->first('bar')
			->count('baz')
			->fetch();
		
		$this->assertEquals(true, count($result) == $this->testResults['count']);
		$this->assertEquals(true, $result[0]['foo'] == $this->testResults['input'][0][0]);
		$this->assertEquals(true, $result[0]['bar'] == $this->testResults['input'][0][1]);
		$this->assertEquals(true, $result[0]['baz'] == $this->testResults['input'][0][2]);
		$this->assertEquals(true, $result[1]['foo'] == $this->testResults['input'][1][0]);
		$this->assertEquals(true, $result[1]['bar'] == $this->testResults['input'][1][1]);
		$this->assertEquals(true, $result[1]['baz'] == $this->testResults['input'][1][2]);
	}
	
	/**
	 * @dataProvider providerObjectData
	 */
	public function testGroup_InputObject($data)
	{
		$result = Sloth::from($data)
			->group('foo', ['bar', 'baz'])
			->first('bar')
			->count('baz')
			->fetch();
		
		$this->assertEquals(true, count($result) == $this->testResults['count']);
		$this->assertEquals(true, $result[0]->foo == $this->testResults['input'][0][0]);
		$this->assertEquals(true, $result[0]->bar == $this->testResults['input'][0][1]);
		$this->assertEquals(true, $result[0]->baz == $this->testResults['input'][0][2]);
		$this->assertEquals(true, $result[1]->foo == $this->testResults['input'][1][0]);
		$this->assertEquals(true, $result[1]->bar == $this->testResults['input'][1][1]);
		$this->assertEquals(true, $result[1]->baz == $this->testResults['input'][1][2]);
	}
	
	/**
	 * @dataProvider providerAssocData
	 */
	public function testGroup_ListGroups($data)
	{
		$result = Sloth::from($data)
			->group('foo')
			->fetch();
		
		$this->assertEquals(true, $result[0]['foo'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $result[1]['foo'] == $this->testResults['groupNames'][1]);
	}
	
	/**
	 * @dataProvider providerAssocData
	 */
	public function testGroup_CountRows($data)
	{
		$result = Sloth::from($data)
			->group('foo')
			->count('*')
			->fetch();
		
		$this->assertEquals(true, $result[0]['*'] == $this->testResults['countRows'][0]);
		$this->assertEquals(true, $result[1]['*'] == $this->testResults['countRows'][1]);
	}
	
	/**
	 * @dataProvider providerAssocData
	 */
	public function testGroup_CountValues($data)
	{
		$result = Sloth::from($data)
			->group('foo', 'baz')
			->count()
			->fetch();
		
		$this->assertEquals(true, $result[0]['baz'] == $this->testResults['countValues'][0]);
		$this->assertEquals(true, $result[1]['baz'] == $this->testResults['countValues'][1]);
	}
	
	/**
	 * @dataProvider providerAssocData
	 */
	public function testGroup_Accum($data)
	{
		$result = Sloth::from($data)
			->group('foo', 'baz')
			->accum()
			->fetch();
		
		$this->assertEquals(true, $result[0]['baz'] == $this->testResults['accum'][0]);
		$this->assertEquals(true, $result[1]['baz'] == $this->testResults['accum'][1]);
	}
	
	/**
	 * @dataProvider providerAssocData
	 */
	public function testGroup_Avg($data)
	{
		$result = Sloth::from($data)
			->group('foo', 'baz')
			->avg()
			->fetch();
		
		$this->assertEquals(true, $result[0]['baz'] == $this->testResults['avg'][0]);
		$this->assertEquals(true, $result[1]['baz'] == $this->testResults['avg'][1]);
	}
	
	/**
	 * @dataProvider providerAssocData
	 */
	public function testGroup_Concat($data)
	{
		$result = Sloth::from($data)
			->group('foo', 'baz')
			->concat()
			->fetch();
		
		$this->assertEquals(true, $result[0]['baz'] == $this->testResults['concat'][0]);
		$this->assertEquals(true, $result[1]['baz'] == $this->testResults['concat'][1]);
	}
	
	/**
	 * @dataProvider providerAssocData
	 */
	public function testPivot_First($data)
	{
		$result = Sloth::from($data)
			->pivot('foo', 'bar', 'baz')
			->fetch();
		
		$this->assertEquals(true, $result[0]['A'] == $this->testResults['pivotFirst'][0][0]);
		$this->assertEquals(true, $result[0]['B'] == $this->testResults['pivotFirst'][0][1]);
		$this->assertEquals(true, $result[0]['C'] == $this->testResults['pivotFirst'][0][2]);
		$this->assertEquals(true, $result[0]['D'] == $this->testResults['pivotFirst'][0][3]);
		
		$this->assertEquals(true, $result[1]['A'] == $this->testResults['pivotFirst'][1][0]);
		$this->assertEquals(true, $result[1]['B'] == $this->testResults['pivotFirst'][1][1]);
		$this->assertEquals(true, $result[1]['C'] == $this->testResults['pivotFirst'][1][2]);
		$this->assertEquals(true, $result[1]['D'] == $this->testResults['pivotFirst'][1][3]);
	}
	
	/**
	 * @dataProvider providerAssocData
	 */
	public function testPivot_Count($data)
	{
		$result = Sloth::from($data)
			->pivot('foo', 'bar', 'baz')
			->count()
			->fetch();
		
		$this->assertEquals(true, $result[0]['A'] == $this->testResults['pivotCount'][0][0]);
		$this->assertEquals(true, $result[0]['B'] == $this->testResults['pivotCount'][0][1]);
		$this->assertEquals(true, $result[0]['C'] == $this->testResults['pivotCount'][0][2]);
		$this->assertEquals(true, $result[0]['D'] == $this->testResults['pivotCount'][0][3]);
		
		$this->assertEquals(true, $result[1]['A'] == $this->testResults['pivotCount'][1][0]);
		$this->assertEquals(true, $result[1]['B'] == $this->testResults['pivotCount'][1][1]);
		$this->assertEquals(true, $result[1]['C'] == $this->testResults['pivotCount'][1][2]);
		$this->assertEquals(true, $result[1]['D'] == $this->testResults['pivotCount'][1][3]);
	}
}