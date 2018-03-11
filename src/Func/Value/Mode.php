<?php
namespace Weby\Sloth\Func\Value;

class Mode extends Base
{
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		$storeCol = $this->getStoreColumn($groupCol, $dataCol, 'accum');
		
		$store = &$this->operation->getStore();
		$store[$storeCol] = array();
		$store[$storeCol][] = (string) $nextValue;
		
		$currValue = $nextValue;
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		$storeCol = $this->getStoreColumn($groupCol, $dataCol, 'accum');
		
		$store = &$this->operation->getStore();
		$store[$storeCol][] = (string) $nextValue;
		
		$currValue = $this->mode($store[$storeCol]);
	}
	
	private function mode(&$data)
	{
		$vals = array_count_values($data);
		arsort($vals);
		reset($vals);
		
		return key($vals);
	}
}