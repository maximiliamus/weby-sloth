<?php
namespace Weby\Sloth;

class GroupTest extends \PHPUnit\Framework\TestCase
{
	public function providerArrayOrdered()
	{
		return
		[
			[
				[
					[1, 'group1', 's1', 1, 0.1, true,  ['a1']],
					[3, 'group1', 's3', 1, 0.1, true,  ['a3']],
					[2, 'group2', 's2', 2, 0.2, true,  ['a2']],
					[5, 'group2', 's5', 2, 0.2, false, ['a5']],
					[4, 'group2', 's4', 2, 0.2, true,  ['a4']],
				],
			],
		];
	}
	
	public function providerAssocOrdered()
	{
		return
		[
			[
				[
					['id' => 1, 'group' => 'group1', 'string' => 's1', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => ['a1']],
					['id' => 3, 'group' => 'group1', 'string' => 's3', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => ['a3']],
					['id' => 2, 'group' => 'group2', 'string' => 's2', 'integer' => 2, 'double' => 0.2, 'boolean' => true,  'array' => ['a2']],
					['id' => 5, 'group' => 'group2', 'string' => 's5', 'integer' => 2, 'double' => 0.2, 'boolean' => false, 'array' => ['a5']],
					['id' => 4, 'group' => 'group2', 'string' => 's4', 'integer' => 2, 'double' => 0.2, 'boolean' => true,  'array' => ['a4']],
				],
			],
		];
	}
	
	public function providerObjectOrdered()
	{
		return
		[
			[
				[
					(object) ['id' => 1, 'group' => 'group1', 'string' => 's1', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => ['a1']],
					(object) ['id' => 3, 'group' => 'group1', 'string' => 's3', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => ['a3']],
					(object) ['id' => 2, 'group' => 'group2', 'string' => 's2', 'integer' => 2, 'double' => 0.2, 'boolean' => true,  'array' => ['a2']],
					(object) ['id' => 5, 'group' => 'group2', 'string' => 's5', 'integer' => 2, 'double' => 0.2, 'boolean' => false, 'array' => ['a5']],
					(object) ['id' => 4, 'group' => 'group2', 'string' => 's4', 'integer' => 2, 'double' => 0.2, 'boolean' => true,  'array' => ['a4']],
				],
			],
		];
	}
	
	private $testResults = [
		'groupCount' => 2,
		'groupNames' => ['group1', 'group2'],
		
		'count' => [2, 3],
		
		'sumInteger' => [2, 6],
		'sumDouble' => [0.2, 0.6],
		
		'avgInteger' => [1, 2],
		'avgDouble' => [0.1, 0.2],
		
		'maxInteger' => [1, 2],
		'maxDouble' => [0.1, 0.2],
		
		'minInteger' => [1, 2],
		'minDouble' => [0.1, 0.2],
		
		'medianInteger' => [1, 2],
		'medianDouble' => [0.1, 0.2],
		
		'modeInteger' => [1, 2],
		'modeDouble' => [0.1, 0.2],
		
		'accumString'  => [['s1', 's3'], ['s2', 's5', 's4']],
		'accumInteger' => [[1, 1], [2, 2, 2]],
		'accumDouble'  => [[0.1, 0.1], [0.2, 0.2, 0.2]],
		'accumBoolean' => [[true, true], [true, false, true]],
		'accumArray'   => [[['a1'], ['a3']], [['a2'], ['a5'], ['a4']]],
		
		'firstString'  => ['s1', 's2'],
		'firstInteger' => [1, 2],
		'firstDouble'  => [0.1, 0.2],
		'firstBoolean' => [true, true],
		'firstArray'   => [['a1'], ['a2']],
		
		'accumFlatString'  => ['s1s3', 's2s5s4'],
		'accumFlatInteger' => [2, 6],
		'accumFlatDouble'  => [0.2, 0.6],
		'accumFlatBoolean' => [true, false],
		'accumFlatArray'   => [['a1', 'a3'], ['a2', 'a5', 'a4']],
		
		'firstNonFlatInteger' => [[1], [2]],
		'firstNonFlatDouble'  => [[0.1], [0.2]],
		'firstNonFlatString'  => [['s1'], ['s2']],
		'firstNonFlatBoolean' => [[true], [true]],
		'firstNonFlatArray'   => [[['a1']], [['a2']]],
	];
	
	/**
	 * @dataProvider providerArrayOrdered
	 */
	public function testGroup_ArrayInput_SingleGroup($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group(1)
			->fetch();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0][1] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1][1] == $this->testResults['groupNames'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group([1 => 'groupA'])
			->fetch();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['groupA'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1]['groupA'] == $this->testResults['groupNames'][1]);
	}
	
	/**
	 * @dataProvider providerArrayOrdered
	 */
	public function testGroup_ArrayInput_SingleGroup_Count($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group(1)
			->count('*')
			->fetch();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0][1] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[0]['*'] == $this->testResults['count'][0]);
		$this->assertEquals(true, $groupedData[1][1] == $this->testResults['groupNames'][1]);
		$this->assertEquals(true, $groupedData[1]['*'] == $this->testResults['count'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group([1 => 'groupA'])
			->count('*')->as(2)
			->fetch();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['groupA'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[0][2] == $this->testResults['count'][0]);
		$this->assertEquals(true, $groupedData[1]['groupA'] == $this->testResults['groupNames'][1]);
		$this->assertEquals(true, $groupedData[1][2] == $this->testResults['count'][1]);
	}
	
	/**
	 * @dataProvider providerAssocOrdered
	 */
	public function testGroup_AssocInput_SingleGroup($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group('group')
			->fetch();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['group'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1]['group'] == $this->testResults['groupNames'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(['group' => 'groupA'])
			->fetch();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['groupA'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1]['groupA'] == $this->testResults['groupNames'][1]);
	}
	
	/**
	 * @dataProvider providerAssocOrdered
	 */
	public function testGroup_AssocInput_SingleGroup_Count($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group('group')
			->count('*')
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['*'] == $this->testResults['count'][0]);
		$this->assertEquals(true, $groupedData[1]['*'] == $this->testResults['count'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(['group' => 'groupA'])
			->count('*')->as('countA')
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['countA'] == $this->testResults['count'][0]);
		$this->assertEquals(true, $groupedData[1]['countA'] == $this->testResults['count'][1]);
	}
	
	/**
	 * @dataProvider providerAssocOrdered
	 */
	public function testGroup_AssocInput_SingleGroup_Sum($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group('group', ['integer', 'double'])
			->sum()
			->avg()
			->min()
			->max()
			->median()
			->mode()
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['integer']['sum'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer']['sum'] == $this->testResults['sumInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double']['sum']  == $this->testResults['sumDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double']['sum']  == $this->testResults['sumDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer']['avg'] == $this->testResults['avgInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer']['avg'] == $this->testResults['avgInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double']['avg']  == $this->testResults['avgDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double']['avg']  == $this->testResults['avgDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer']['min'] == $this->testResults['minInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer']['min'] == $this->testResults['minInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double']['min']  == $this->testResults['minDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double']['min']  == $this->testResults['minDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer']['max'] == $this->testResults['maxInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer']['max'] == $this->testResults['maxInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double']['max']  == $this->testResults['maxDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double']['max']  == $this->testResults['maxDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer']['median'] == $this->testResults['medianInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer']['median'] == $this->testResults['medianInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double']['median']  == $this->testResults['medianDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double']['median']  == $this->testResults['medianDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer']['mode'] == $this->testResults['modeInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer']['mode'] == $this->testResults['modeInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double']['mode']  == $this->testResults['modeDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double']['mode']  == $this->testResults['modeDouble'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(
				['group' => 'groupA'],
				['integer' => 'integerA']
			)
			->sum()->as('sumA')
			->dontOptimizeColumnNames()
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['integerA']['sumA'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integerA']['sumA'] == $this->testResults['sumInteger'][1]);
		
		$groupedData = $sloth
			->group(
				['group' => 'groupA'],
				['integer' => 'integerA', 'double']
			)
			->sum()
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['integerA'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integerA'] == $this->testResults['sumInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double'] == $this->testResults['sumDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double'] == $this->testResults['sumDouble'][1]);
	}
	
	/**
	 * @dataProvider providerAssocOrdered
	 */
	public function testGroup_AssocInput_SingleGroup_Accum($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group(
				'group',
				[
					'string',
					'integer',
					'double',
					'boolean',
					'array'
				]
			)
			->accum()
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['string'] == $this->testResults['accumString'][0]);
		$this->assertEquals(true, $groupedData[1]['string'] == $this->testResults['accumString'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer'] == $this->testResults['accumInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer'] == $this->testResults['accumInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double'] == $this->testResults['accumDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double'] == $this->testResults['accumDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['boolean'] == $this->testResults['accumBoolean'][0]);
		$this->assertEquals(true, $groupedData[1]['boolean'] == $this->testResults['accumBoolean'][1]);
		
		$this->assertEquals(true, $groupedData[0]['array'] == $this->testResults['accumArray'][0]);
		$this->assertEquals(true, $groupedData[1]['array'] == $this->testResults['accumArray'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(
				['group' => 'groupA'],
				[
					'string'  => 'stringA',
					'integer' => 'integerA',
					'double'  => 'doubleA',
					'boolean' => 'booleanA',
					'array'   => 'arrayA',
				]
			)
			->accum()->as('accumA')
			->dontOptimizeColumnNames()
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['stringA']['accumA'] == $this->testResults['accumString'][0]);
		$this->assertEquals(true, $groupedData[1]['stringA']['accumA'] == $this->testResults['accumString'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integerA']['accumA'] == $this->testResults['accumInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integerA']['accumA'] == $this->testResults['accumInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['doubleA']['accumA'] == $this->testResults['accumDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['doubleA']['accumA'] == $this->testResults['accumDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['booleanA']['accumA'] == $this->testResults['accumBoolean'][0]);
		$this->assertEquals(true, $groupedData[1]['booleanA']['accumA'] == $this->testResults['accumBoolean'][1]);
		
		$this->assertEquals(true, $groupedData[0]['arrayA']['accumA'] == $this->testResults['accumArray'][0]);
		$this->assertEquals(true, $groupedData[1]['arrayA']['accumA'] == $this->testResults['accumArray'][1]);
		
		$groupedData = $sloth
			->group(
				['group' => 'groupA'],
				[
					'string'  => 'stringA',
					'integer' => 'integerA',
					'double'  => 'doubleA',
					'boolean' => 'booleanA',
					'array'   => 'arrayA',
				]
			)
			->accum()
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['stringA'] == $this->testResults['accumString'][0]);
		$this->assertEquals(true, $groupedData[1]['stringA'] == $this->testResults['accumString'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integerA'] == $this->testResults['accumInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integerA'] == $this->testResults['accumInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['doubleA'] == $this->testResults['accumDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['doubleA'] == $this->testResults['accumDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['booleanA'] == $this->testResults['accumBoolean'][0]);
		$this->assertEquals(true, $groupedData[1]['booleanA'] == $this->testResults['accumBoolean'][1]);
		
		$this->assertEquals(true, $groupedData[0]['arrayA'] == $this->testResults['accumArray'][0]);
		$this->assertEquals(true, $groupedData[1]['arrayA'] == $this->testResults['accumArray'][1]);
	}
	
	/**
	 * @dataProvider providerAssocOrdered
	 */
	public function testGroup_AssocInput_SingleGroup_First($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group('group', 'string')
			->first()
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['string'] == $this->testResults['firstString'][0]);
		$this->assertEquals(true, $groupedData[1]['string'] == $this->testResults['firstString'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(
				['group' => 'groupA'],
				['string' => 'stringA']
			)
			->first()->as('firstA')
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['stringA'] == $this->testResults['firstString'][0]);
		$this->assertEquals(true, $groupedData[1]['stringA'] == $this->testResults['firstString'][1]);
		
		$groupedData = $sloth
			->group(
				['group' => 'groupA'],
				['string' => 'stringA']
			)
			->first()
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['stringA'] == $this->testResults['firstString'][0]);
		$this->assertEquals(true, $groupedData[1]['stringA'] == $this->testResults['firstString'][1]);
	}
	
	/**
	 * @dataProvider providerAssocOrdered
	 */
	public function testGroup_AssocInput_SingleGroup_Accum_Flat($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group('group', 'string')
			->accum()->flat(true)
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['string'] == $this->testResults['accumFlatString'][0]);
		$this->assertEquals(true, $groupedData[1]['string'] == $this->testResults['accumFlatString'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer')
			->accum()->flat(true)
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['integer'] == $this->testResults['accumFlatInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer'] == $this->testResults['accumFlatInteger'][1]);
	}
	
	/**
	 * @dataProvider providerAssocOrdered
	 */
	public function testGroup_AssocInput_SingleGroup_First_NonFlat($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group('group', 'string')
			->first()->flat(false)
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['string'] == $this->testResults['firstNonFlatString'][0]);
		$this->assertEquals(true, $groupedData[1]['string'] == $this->testResults['firstNonFlatString'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer')
			->first()->flat(false)
			->fetch();
		
		$this->assertEquals(true, $groupedData[0]['integer'] == $this->testResults['firstNonFlatInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer'] == $this->testResults['firstNonFlatInteger'][1]);
	}
	
	/**
	 * @dataProvider providerObjectOrdered
	 */
	public function testGroup_ObjectInput_SingleGroup($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group('group')
			->count('*')
			->fetch();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['group'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1]['group'] == $this->testResults['groupNames'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(['group' => 'groupA'])
			->count('*')->as('countA')
			->fetch();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['groupA'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[0]['countA'] == $this->testResults['count'][0]);
		$this->assertEquals(true, $groupedData[1]['groupA'] == $this->testResults['groupNames'][1]);
		$this->assertEquals(true, $groupedData[1]['countA'] == $this->testResults['count'][1]);
	}
	
	/**
	 * @dataProvider providerObjectOrdered
	 */
	public function testGroup_ObjectInput_SingleGroup_Count($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group('group')
			->fetch();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['group'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1]['group'] == $this->testResults['groupNames'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(['group' => 'groupAlias'])
			->fetch();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['groupAlias'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1]['groupAlias'] == $this->testResults['groupNames'][1]);
		
		// Alias for column object
		$groupedData = $sloth
			->group(Column::new('group')->as('groupAlias'))
			->fetch();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['groupAlias'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1]['groupAlias'] == $this->testResults['groupNames'][1]);
	}
	
	/**
	 * @dataProvider providerAssocOrdered
	 */
	public function testGroup_AssocInput_SingleGroup_AsAssoc($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group('group', 'integer')
			->sum('')
			->fetch();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['group'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1]['group'] == $this->testResults['groupNames'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer'] == $this->testResults['sumInteger'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer')
			->sum('')
			->asAssoc()
			->fetch();
		
		$this->assertEquals(true, count(array_keys($groupedData)) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData['group1'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData['group2'] == $this->testResults['sumInteger'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer', 'double')
			->sum('')
			->asAssoc()
			->fetch();
		
		$this->assertEquals(true, count(array_keys($groupedData)) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData['group1'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData['group2'] == $this->testResults['sumInteger'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer', 'double')
			->sum()
			->asAssoc('group', '*')
			->fetch();
		
		$this->assertEquals(true, count(array_keys($groupedData)) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData['group1']['integer'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData['group2']['integer'] == $this->testResults['sumInteger'][1]);
		
		$groupedData = $sloth
			->group('group')
			->count('*')
			->asAssoc()
			->fetch();
		
		$this->assertEquals(true, count(array_keys($groupedData)) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData['group1'] == $this->testResults['count'][0]);
		$this->assertEquals(true, $groupedData['group2'] == $this->testResults['count'][1]);
	}
}