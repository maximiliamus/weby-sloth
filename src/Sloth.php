<?php
namespace Weby\Sloth;

class Sloth
{
	public $data = null;
	
	public static function from($data)
	{
		return new Sloth($data);
	}
	
	public function __construct($data)
	{
		$this->data = &$data;
		
		if (empty($this->data))
			throw new \Weby\Sloth\Exception('No data.');
	}
	
	public function group($groupCols, $valueCols = null)
	{
		$group = new Operation\Group($this, $groupCols, $valueCols);
		
		return $group;
	}
	
	public function pivot($groupCols, $valueCols, $columnCols)
	{
		$pivot = new Operation\Pivot($this, $groupCols, $valueCols, $columnCols);
		
		return $pivot;
	}
}