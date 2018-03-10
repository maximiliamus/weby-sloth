<?php
namespace Weby\Sloth;

class GroupTest extends \PHPUnit\Framework\TestCase
{
	public function providerArrayOrdered()
	{
		return array (
			array(
				array(
					array(1, 'group1', 's1', 1, 0.1, true,  array('a1')),
					array(3, 'group1', 's3', 1, 0.1, true,  array('a3')),
					array(2, 'group2', 's2', 2, 0.2, true,  array('a2')),
					array(5, 'group2', 's5', 2, 0.2, false, array('a5')),
					array(4, 'group2', 's4', 2, 0.2, true,  array('a4')),
				),
			),
		);
	}
	
	public function providerAssocOrdered()
	{
		return array (
			array(
				array(
					array('id' => 1, 'group' => 'group1', 'string' => 's1', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => array('a1')),
					array('id' => 3, 'group' => 'group1', 'string' => 's3', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => array('a3')),
					array('id' => 2, 'group' => 'group2', 'string' => 's2', 'integer' => 2, 'double' => 0.2, 'boolean' => true,  'array' => array('a2')),
					array('id' => 5, 'group' => 'group2', 'string' => 's5', 'integer' => 2, 'double' => 0.2, 'boolean' => false, 'array' => array('a5')),
					array('id' => 4, 'group' => 'group2', 'string' => 's4', 'integer' => 2, 'double' => 0.2, 'boolean' => true,  'array' => array('a4')),
				),
			),
		);
	}
	
	public function providerObjectOrdered()
	{
		return array (
			array(
				array(
					(object) array('id' => 1, 'group' => 'group1', 'string' => 's1', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => array('a1')),
					(object) array('id' => 3, 'group' => 'group1', 'string' => 's3', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => array('a3')),
					(object) array('id' => 2, 'group' => 'group2', 'string' => 's2', 'integer' => 2, 'double' => 0.2, 'boolean' => true,  'array' => array('a2')),
					(object) array('id' => 5, 'group' => 'group2', 'string' => 's5', 'integer' => 2, 'double' => 0.2, 'boolean' => false, 'array' => array('a5')),
					(object) array('id' => 4, 'group' => 'group2', 'string' => 's4', 'integer' => 2, 'double' => 0.2, 'boolean' => true,  'array' => array('a4')),
				),
			),
		);
	}
	
	private $testResults = array(
		'groupCount' => 2,
		'groupNames' => array('group1', 'group2'),
		
		'count' => array(2, 3),
		
		'sumInteger' => array(2, 6),
		'sumDouble' => array(0.2, 0.6),
		
		'avgInteger' => array(1, 2),
		'avgDouble' => array(0.1, 0.2),
		
		'maxInteger' => array(1, 2),
		'maxDouble' => array(0.1, 0.2),
		
		'minInteger' => array(1, 2),
		'minDouble' => array(0.1, 0.2),
		
		'medianInteger' => array(1, 2),
		'medianDouble' => array(0.1, 0.2),
		
		'modeInteger' => array(1, 2),
		'modeDouble' => array(0.1, 0.2),
		
		'accumInteger' => array(array(1, 1), array(2, 2, 2)),
		'accumDouble'  => array(array(0.1, 0.1), array(0.2, 0.2, 0.2)),
		'accumString'  => array(array('s1', 's3'), array('s2', 's5', 's4')),
		'accumBoolean' => array(array(true, true), array(true, false, true)),
		'accumArray'   => array(array(array('a1'), array('a3')), array(array('a2'), array('a5'), array('a4'))),
		
		'firstInteger' => array(array(1), array(2)),
		'firstDouble'  => array(array(0.1), array(0.2)),
		'firstString'  => array(array('s1'), array('s2')),
		'firstBoolean' => array(array(true), array(true)),
		'firstArray'   => array(array(array('a1')), array(array('a2'))),
		
		'accumFlatString'  => array('s1s3', 's2s5s4'),
		'accumFlatInteger' => array(2, 6),
		'accumFlatDouble'  => array(0.2, 0.6),
		'accumFlatBoolean' => array(true, false),
		'accumFlatArray'   => array(array('a1', 'a3'), array('a2', 'a5', 'a4')),
		'firstFlatString'  => array('s1', 's2'),
		'firstFlatInteger' => array(1, 2),
		'firstFlatDouble'  => array(0.1, 0.2),
		'firstFlatBoolean' => array(true, true),
		'firstFlatArray'   => array(array('a1'), array('a2')),
	);
	
	/**
	 * @dataProvider providerArrayOrdered
	 */
	public function testGroup_ArrayInput_SingleGroup($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group(1)
			->select();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0][1] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1][1] == $this->testResults['groupNames'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(array(1 => 'groupA'))
			->select();
		
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
			->count()
			->select();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0][1] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[0]['count'] == $this->testResults['count'][0]);
		$this->assertEquals(true, $groupedData[1][1] == $this->testResults['groupNames'][1]);
		$this->assertEquals(true, $groupedData[1]['count'] == $this->testResults['count'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(array(1 => 'groupA'))
			->count(2)
			->select();
		
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
			->select();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['group'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1]['group'] == $this->testResults['groupNames'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(array('group' => 'groupA'))
			->select();
		
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
			->count()
			->select();
		
		$this->assertEquals(true, $groupedData[0]['count'] == $this->testResults['count'][0]);
		$this->assertEquals(true, $groupedData[1]['count'] == $this->testResults['count'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(array('group' => 'groupA'))
			->count('countA')
			->select();
		
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
			->group('group', array('integer', 'double'))
			->sum()
			->avg()
			->min()
			->max()
			->median()
			->mode()
			->select();
		
		$this->assertEquals(true, $groupedData[0]['integer_sum'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer_sum'] == $this->testResults['sumInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double_sum']  == $this->testResults['sumDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double_sum']  == $this->testResults['sumDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer_avg'] == $this->testResults['avgInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer_avg'] == $this->testResults['avgInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double_avg']  == $this->testResults['avgDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double_avg']  == $this->testResults['avgDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer_min'] == $this->testResults['minInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer_min'] == $this->testResults['minInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double_min']  == $this->testResults['minDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double_min']  == $this->testResults['minDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer_max'] == $this->testResults['maxInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer_max'] == $this->testResults['maxInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double_max']  == $this->testResults['maxDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double_max']  == $this->testResults['maxDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer_median'] == $this->testResults['medianInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer_median'] == $this->testResults['medianInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double_median']  == $this->testResults['medianDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double_median']  == $this->testResults['medianDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer_mode'] == $this->testResults['modeInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer_mode'] == $this->testResults['modeInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double_mode']  == $this->testResults['modeDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double_mode']  == $this->testResults['modeDouble'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(
				array('group' => 'groupA'),
				array('integer' => 'integerA')
			)
			->sum('sumA')
			->select();
		
		$this->assertEquals(true, $groupedData[0]['integerA_sumA'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integerA_sumA'] == $this->testResults['sumInteger'][1]);
		
		$groupedData = $sloth
			->group(
				array('group' => 'groupA'),
				array('integer' => 'integerA', 'double')
			)
			->sum('')
			->select();
		
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
				array(
					'string',
					'integer',
					'double',
					'boolean',
					'array'
				)
			)
			->accum()
			->select();
		
		$this->assertEquals(true, $groupedData[0]['string_accum'] == $this->testResults['accumString'][0]);
		$this->assertEquals(true, $groupedData[1]['string_accum'] == $this->testResults['accumString'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer_accum'] == $this->testResults['accumInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer_accum'] == $this->testResults['accumInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['double_accum'] == $this->testResults['accumDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['double_accum'] == $this->testResults['accumDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['boolean_accum'] == $this->testResults['accumBoolean'][0]);
		$this->assertEquals(true, $groupedData[1]['boolean_accum'] == $this->testResults['accumBoolean'][1]);
		
		$this->assertEquals(true, $groupedData[0]['array_accum'] == $this->testResults['accumArray'][0]);
		$this->assertEquals(true, $groupedData[1]['array_accum'] == $this->testResults['accumArray'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(
				array('group' => 'groupA'),
				array(
					'string'  => 'stringA',
					'integer' => 'integerA',
					'double'  => 'doubleA',
					'boolean' => 'booleanA',
					'array'   => 'arrayA',
				)
			)
			->accum('accumA')
			->select();
		
		$this->assertEquals(true, $groupedData[0]['stringA_accumA'] == $this->testResults['accumString'][0]);
		$this->assertEquals(true, $groupedData[1]['stringA_accumA'] == $this->testResults['accumString'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integerA_accumA'] == $this->testResults['accumInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integerA_accumA'] == $this->testResults['accumInteger'][1]);
		
		$this->assertEquals(true, $groupedData[0]['doubleA_accumA'] == $this->testResults['accumDouble'][0]);
		$this->assertEquals(true, $groupedData[1]['doubleA_accumA'] == $this->testResults['accumDouble'][1]);
		
		$this->assertEquals(true, $groupedData[0]['booleanA_accumA'] == $this->testResults['accumBoolean'][0]);
		$this->assertEquals(true, $groupedData[1]['booleanA_accumA'] == $this->testResults['accumBoolean'][1]);
		
		$this->assertEquals(true, $groupedData[0]['arrayA_accumA'] == $this->testResults['accumArray'][0]);
		$this->assertEquals(true, $groupedData[1]['arrayA_accumA'] == $this->testResults['accumArray'][1]);
		
		$groupedData = $sloth
			->group(
				array('group' => 'groupA'),
				array(
					'string'  => 'stringA',
					'integer' => 'integerA',
					'double'  => 'doubleA',
					'boolean' => 'booleanA',
					'array'   => 'arrayA',
				)
			)
			->accum('')
			->select();
		
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
			->select();
		
		$this->assertEquals(true, $groupedData[0]['string_first'] == $this->testResults['firstString'][0]);
		$this->assertEquals(true, $groupedData[1]['string_first'] == $this->testResults['firstString'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(
				array('group' => 'groupA'),
				array('string' => 'stringA')
			)
			->first('firstA')
			->select();
		
		$this->assertEquals(true, $groupedData[0]['stringA_firstA'] == $this->testResults['firstString'][0]);
		$this->assertEquals(true, $groupedData[1]['stringA_firstA'] == $this->testResults['firstString'][1]);
		
		$groupedData = $sloth
			->group(
				array('group' => 'groupA'),
				array('string' => 'stringA')
			)
			->first('')
			->select();
		
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
			->accum(null, array('flat' => true))
			->select();
		
		$this->assertEquals(true, $groupedData[0]['string_accum'] == $this->testResults['accumFlatString'][0]);
		$this->assertEquals(true, $groupedData[1]['string_accum'] == $this->testResults['accumFlatString'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer')
			->accum(null, array('flat' => true))
			->select();
		
		$this->assertEquals(true, $groupedData[0]['integer_accum'] == $this->testResults['accumFlatInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer_accum'] == $this->testResults['accumFlatInteger'][1]);
	}
	
	/**
	 * @dataProvider providerAssocOrdered
	 */
	public function testGroup_AssocInput_SingleGroup_First_Flat($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group('group', 'string')
			->first(null, array('flat' => true))
			->select();
		
		$this->assertEquals(true, $groupedData[0]['string_first'] == $this->testResults['firstFlatString'][0]);
		$this->assertEquals(true, $groupedData[1]['string_first'] == $this->testResults['firstFlatString'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer')
			->first(null, array('flat' => true))
			->select();
		
		$this->assertEquals(true, $groupedData[0]['integer_first'] == $this->testResults['firstFlatInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer_first'] == $this->testResults['firstFlatInteger'][1]);
	}
	
	/**
	 * @dataProvider providerObjectOrdered
	 */
	public function testGroup_ObjectInput_SingleGroup($data)
	{
		$sloth = Sloth::from($data);
		$groupedData = $sloth
			->group('group')
			->count()
			->select();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['group'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1]['group'] == $this->testResults['groupNames'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(array('group' => 'groupA'))
			->count('countA')
			->select();
		
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
			->select();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['group'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1]['group'] == $this->testResults['groupNames'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(array('group' => 'groupAlias'))
			->select();
		
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
			->select();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['group'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[1]['group'] == $this->testResults['groupNames'][1]);
		
		$this->assertEquals(true, $groupedData[0]['integer'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData[1]['integer'] == $this->testResults['sumInteger'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer')
			->sum('')
			->asAssoc()
			->select();
		
		$this->assertEquals(true, count(array_keys($groupedData)) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData['group1'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData['group2'] == $this->testResults['sumInteger'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer', 'double')
			->sum('')
			->asAssoc()
			->select();
		
		$this->assertEquals(true, count(array_keys($groupedData)) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData['group1'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData['group2'] == $this->testResults['sumInteger'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer', 'double')
			->sum('')
			->asAssoc('group', '*')
			->select();
		
		$this->assertEquals(true, count(array_keys($groupedData)) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData['group1']['integer'] == $this->testResults['sumInteger'][0]);
		$this->assertEquals(true, $groupedData['group2']['integer'] == $this->testResults['sumInteger'][1]);
		
		$groupedData = $sloth
			->group('group')
			->count()
			->asAssoc()
			->select();
		
		$this->assertEquals(true, count(array_keys($groupedData)) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData['group1'] == $this->testResults['count'][0]);
		$this->assertEquals(true, $groupedData['group2'] == $this->testResults['count'][1]);
	}
}