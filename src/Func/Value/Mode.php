<?php
namespace Weby\Sloth\Func\Value;

class Mode extends Base
{
	const FIELD_SUFFIX = 'mode';
	
	public function onAddGroup(&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue, &$store)
	{
		$storeCol = $this->getStoreColumn($groupCol, $dataCol, 'accum');
		
		$store[$storeCol] = array();
		$store[$storeCol][] = $nextValue;
		
		$currValue = $nextValue;
	}
	
	public function onUpdateGroup(&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue, &$store)
	{
		$storeCol = $this->getStoreColumn($groupCol, $dataCol, 'accum');
		
		$store[$storeCol][] = $nextValue;
		
		$currValue = $this->mode($store[$storeCol]);
	}
	
	private function mode(&$data)
	{
		$vals = array_count_values($data); 
		arsort($vals);
		reset($vals);
		
		return current($vals);
	}
}