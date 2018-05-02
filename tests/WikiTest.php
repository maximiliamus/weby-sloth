<?php
namespace Weby\Sloth;

class WikiTest extends \PHPUnit\Framework\TestCase
{
	private $testResults = [
		'count' => 2,
		'groupNames' => ['one', 'two'],
		'rowCount' => [4, 5],
		'valueCount' => [3, 4],
		'pivotFirst' => [
			0 => [1, 2, 3, null],
			1 => [4, 5, 6, 7]
		],
		'pivotCount' => [
			0 => [1, 1, 1, null],
			1 => [1, 1, 1, 1]
		]
	];
	
	public function providerData()
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
	
	/**
	 * @dataProvider providerData
	 */
	public function testGroup_ListGroups($data)
	{
		$result = Sloth::from($data)
			->group('foo')
			->fetch();
		
		$this->assertEquals(true, count($result) == $this->testResults['count']);
		$this->assertEquals(true, $result[0]['foo'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $result[1]['foo'] == $this->testResults['groupNames'][1]);
	}
	
	/**
	 * @dataProvider providerData
	 */
	public function testGroup_CountRows($data)
	{
		$result = Sloth::from($data)
			->group('foo')
			->count('*')
			->fetch();
		
		$this->assertEquals(true, $result[0]['*'] == $this->testResults['rowCount'][0]);
		$this->assertEquals(true, $result[1]['*'] == $this->testResults['rowCount'][1]);
	}
	
	/**
	 * @dataProvider providerData
	 */
	public function testGroup_CountValues($data)
	{
		$result = Sloth::from($data)
			->group('foo', 'baz')
			->count()
			->fetch();
		
		$this->assertEquals(true, $result[0]['baz'] == $this->testResults['valueCount'][0]);
		$this->assertEquals(true, $result[1]['baz'] == $this->testResults['valueCount'][1]);
	}
	
	/**
	 * @dataProvider providerData
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
	 * @dataProvider providerData
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