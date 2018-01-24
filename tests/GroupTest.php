<?php
namespace Weby\Sloth;

class GroupTest extends \PHPUnit\Framework\TestCase
{
	private $testResults = array(
		'groupCount' => 2,
		'groupNames' => array('group1', 'group2'),
		
		'count' => array(2, 3),
		
		'sum' => array(2, 6),
		
		'accumString'  => array(array('string1', 'string3'), array('string2', 'string5', 'string4')),
		'accumInteger' => array(array(1, 1), array(2, 2, 2)),
		'accumDouble'  => array(array(0.1, 0.1), array(0.2, 0.2, 0.2)),
		'accumBoolean' => array(array(true, true), array(true, false, true)),
		'accumArray'   => array(array(array('a1'), array('a3')), array(array('a2'), array('a5'), array('a4'))),
		'firstString'  => array(array('string1'), array('string2')),
		'firstInteger' => array(array(1), array(2)),
		'firstDouble'  => array(array(0.1), array(0.2)),
		'firstBoolean' => array(array(true), array(true)),
		'firstArray'   => array(array(array('a1')), array(array('a2'))),
		
		'accumFlatString'  => array('string1string3', 'string2string5string4'),
		'accumFlatInteger' => array(2, 6),
		'accumFlatDouble'  => array(0.2, 0.6),
		'accumFlatBoolean' => array(true, false),
		'accumFlatArray'   => array(array('a1', 'a3'), array('a2', 'a5', 'a4')),
		'firstFlatString'  => array('string1', 'string2'),
		'firstFlatInteger' => array(1, 2),
		'firstFlatDouble'  => array(0.1, 0.2),
		'firstFlatBoolean' => array(true, true),
		'firstFlatArray'   => array(array('a1'), array('a2')),
	);
	
	public function providerAssocOrdered()
	{
		return array (
			array(
				array(
					array('id' => 1, 'group' => 'group1', 'string' => 'string1', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => array('a1')),
					array('id' => 3, 'group' => 'group1', 'string' => 'string3', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => array('a3')),
					array('id' => 2, 'group' => 'group2', 'string' => 'string2', 'integer' => 2, 'double' => 0.2, 'boolean' => true,  'array' => array('a2')),
					array('id' => 5, 'group' => 'group2', 'string' => 'string5', 'integer' => 2, 'double' => 0.2, 'boolean' => false, 'array' => array('a5')),
					array('id' => 4, 'group' => 'group2', 'string' => 'string4', 'integer' => 2, 'double' => 0.2, 'boolean' => true,  'array' => array('a4')),
				),
			),
		);
	}
	
	public function providerArrayOrdered()
	{
		return array (
			array(
				array(
					array(1, 'group1', 'string1', 1, 0.1, true,  array('a1')),
					array(3, 'group1', 'string3', 1, 0.1, true,  array('a3')),
					array(2, 'group2', 'string2', 2, 0.2, true,  array('a2')),
					array(5, 'group2', 'string5', 2, 0.2, false, array('a5')),
					array(4, 'group2', 'string4', 2, 0.2, true,  array('a4')),
				),
			),
		);
	}
	
	public function providerObjectOrdered()
	{
		return array (
			array(
				array(
					(object) array('id' => 1, 'group' => 'group1', 'string' => 'string1', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => array('a1')),
					(object) array('id' => 3, 'group' => 'group1', 'string' => 'string3', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => array('a3')),
					(object) array('id' => 2, 'group' => 'group2', 'string' => 'string2', 'integer' => 2, 'double' => 0.2, 'boolean' => true,  'array' => array('a2')),
					(object) array('id' => 5, 'group' => 'group2', 'string' => 'string5', 'integer' => 2, 'double' => 0.2, 'boolean' => false, 'array' => array('a5')),
					(object) array('id' => 4, 'group' => 'group2', 'string' => 'string4', 'integer' => 2, 'double' => 0.2, 'boolean' => true,  'array' => array('a4')),
				),
			),
		);
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
			->group(array(array('group' => 'groupA')))
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
			->group(array(array('group' => 'groupA')))
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
			->group('group', 'integer')
			->sum()
			->select();
		
		$this->assertEquals(true, $groupedData[0]['integer_sum'] == $this->testResults['sum'][0]);
		$this->assertEquals(true, $groupedData[1]['integer_sum'] == $this->testResults['sum'][1]);
		
		// Alias for column
		$groupedData = $sloth
			->group(
				array(array('group' => 'groupA')),
				array(array('integer' => 'integerA'))
			)
			->sum('sumA')
			->select();
		
		$this->assertEquals(true, $groupedData[0]['integerA_sumA'] == $this->testResults['sum'][0]);
		$this->assertEquals(true, $groupedData[1]['integerA_sumA'] == $this->testResults['sum'][1]);
		
		$groupedData = $sloth
			->group(
				array(array('group' => 'groupA')),
				array(array('integer' => 'integerA'))
			)
			->sum('')
			->select();
		
		$this->assertEquals(true, $groupedData[0]['integerA'] == $this->testResults['sum'][0]);
		$this->assertEquals(true, $groupedData[1]['integerA'] == $this->testResults['sum'][1]);
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
				array(array('group' => 'groupA')),
				array(
					array('string'  => 'stringA'),
					array('integer' => 'integerA'),
					array('double'  => 'doubleA'),
					array('boolean' => 'booleanA'),
					array('array'   => 'arrayA'),
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
				array(array('group' => 'groupA')),
				array(
					array('string'  => 'stringA'),
					array('integer' => 'integerA'),
					array('double'  => 'doubleA'),
					array('boolean' => 'booleanA'),
					array('array'   => 'arrayA'),
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
				array(array('group' => 'groupA')),
				array(array('string' => 'stringA'))
			)
			->first('firstA')
			->select();
		
		$this->assertEquals(true, $groupedData[0]['stringA_firstA'] == $this->testResults['firstString'][0]);
		$this->assertEquals(true, $groupedData[1]['stringA_firstA'] == $this->testResults['firstString'][1]);
		
		$groupedData = $sloth
			->group(
				array(array('group' => 'groupA')),
				array(array('string' => 'stringA'))
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
			->group(array(array(1 => 'groupA')))
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
			->group(array(array(1 => 'groupA')))
			->count(2)
			->select();
		
		$this->assertEquals(true, count($groupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData[0]['groupA'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $groupedData[0][2] == $this->testResults['count'][0]);
		$this->assertEquals(true, $groupedData[1]['groupA'] == $this->testResults['groupNames'][1]);
		$this->assertEquals(true, $groupedData[1][2] == $this->testResults['count'][1]);
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
			->group(array(array('group' => 'groupA')))
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
			->group(array(array('group' => 'groupAlias')))
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
		
		$this->assertEquals(true, $groupedData[0]['integer'] == $this->testResults['sum'][0]);
		$this->assertEquals(true, $groupedData[1]['integer'] == $this->testResults['sum'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer')
			->sum('')
			->asAssoc()
			->select();
		
		$this->assertEquals(true, count(array_keys($groupedData)) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData['group1'] == $this->testResults['sum'][0]);
		$this->assertEquals(true, $groupedData['group2'] == $this->testResults['sum'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer', 'double')
			->sum('')
			->asAssoc()
			->select();
		
		$this->assertEquals(true, count(array_keys($groupedData)) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData['group1'] == $this->testResults['sum'][0]);
		$this->assertEquals(true, $groupedData['group2'] == $this->testResults['sum'][1]);
		
		$groupedData = $sloth
			->group('group', 'integer', 'double')
			->sum('')
			->asAssoc('group', '*')
			->select();
		
		$this->assertEquals(true, count(array_keys($groupedData)) == $this->testResults['groupCount']);
		$this->assertEquals(true, $groupedData['group1']['integer'] == $this->testResults['sum'][0]);
		$this->assertEquals(true, $groupedData['group2']['integer'] == $this->testResults['sum'][1]);
		
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