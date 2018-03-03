<?php
namespace Weby\Sloth;

class PivotTest extends \PHPUnit\Framework\TestCase
{
	private $testResults = array(
		'groupCount' => 2,
		'groupNames' => array('group1', 'group2'),
		'sum' => array(array(1, 2, 0, 0), array(0, 4, 3, 5)),
	);
	
	public function providerAssocOrdered()
	{
		return array (
			array(
				array(
					array('id' => 1, 'group' => 'group1', 'string' => 'string1', 'integer' => 1, 'double' => 0.1, 'boolean' => true,  'array' => array('a1'), 'date' => '2000-01-01'),
					array('id' => 3, 'group' => 'group1', 'string' => 'string3', 'integer' => 2, 'double' => 0.1, 'boolean' => true,  'array' => array('a3'), 'date' => '2000-01-03'),
					array('id' => 2, 'group' => 'group2', 'string' => 'string2', 'integer' => 3, 'double' => 0.2, 'boolean' => true,  'array' => array('a2'), 'date' => '2000-01-02'),
					array('id' => 5, 'group' => 'group2', 'string' => 'string5', 'integer' => 4, 'double' => 0.2, 'boolean' => false, 'array' => array('a5'), 'date' => '2000-01-03'),
					array('id' => 4, 'group' => 'group2', 'string' => 'string4', 'integer' => 5, 'double' => 0.2, 'boolean' => true,  'array' => array('a4'), 'date' => '2000-01-04'),
				),
			),
		);
	}
	
	/**
	 * @dataProvider providerAssocOrdered
	 */
	public function testPivot_AssocInput_SingleGroup($data)
	{
		$sloth = Sloth::from($data);
		$goupedData = $sloth
			->pivot('group', 'date', 'integer')
			->sum()
			->select();
		
		$this->assertEquals(true, count($goupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $goupedData[0]['group'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $goupedData[1]['group'] == $this->testResults['groupNames'][1]);
		
		$this->assertEquals(true, $goupedData[0]['2000-01-01_integer_sum'] == $this->testResults['sum'][0][0]);
		$this->assertEquals(true, $goupedData[0]['2000-01-03_integer_sum'] == $this->testResults['sum'][0][1]);
		$this->assertEquals(true, $goupedData[0]['2000-01-02_integer_sum'] == $this->testResults['sum'][0][2]);
		$this->assertEquals(true, $goupedData[0]['2000-01-04_integer_sum'] == $this->testResults['sum'][0][3]);
		
		$this->assertEquals(true, $goupedData[1]['2000-01-01_integer_sum'] == $this->testResults['sum'][1][0]);
		$this->assertEquals(true, $goupedData[1]['2000-01-03_integer_sum'] == $this->testResults['sum'][1][1]);
		$this->assertEquals(true, $goupedData[1]['2000-01-02_integer_sum'] == $this->testResults['sum'][1][2]);
		$this->assertEquals(true, $goupedData[1]['2000-01-04_integer_sum'] == $this->testResults['sum'][1][3]);
		
		// Alias for column
		$goupedData = $sloth
			->pivot(
				array('group' => 'groupA'),
				array('date'),
				array('integer' => '')
			)
			->sum('')
			->select();
		
		$this->assertEquals(true, count($goupedData) == $this->testResults['groupCount']);
		$this->assertEquals(true, $goupedData[0]['groupA'] == $this->testResults['groupNames'][0]);
		$this->assertEquals(true, $goupedData[1]['groupA'] == $this->testResults['groupNames'][1]);
		
		$this->assertEquals(true, $goupedData[0]['2000-01-01'] == $this->testResults['sum'][0][0]);
		$this->assertEquals(true, $goupedData[0]['2000-01-03'] == $this->testResults['sum'][0][1]);
		$this->assertEquals(true, $goupedData[0]['2000-01-02'] == $this->testResults['sum'][0][2]);
		$this->assertEquals(true, $goupedData[0]['2000-01-04'] == $this->testResults['sum'][0][3]);
		
		$this->assertEquals(true, $goupedData[1]['2000-01-01'] == $this->testResults['sum'][1][0]);
		$this->assertEquals(true, $goupedData[1]['2000-01-03'] == $this->testResults['sum'][1][1]);
		$this->assertEquals(true, $goupedData[1]['2000-01-02'] == $this->testResults['sum'][1][2]);
		$this->assertEquals(true, $goupedData[1]['2000-01-04'] == $this->testResults['sum'][1][3]);
		
	}
}