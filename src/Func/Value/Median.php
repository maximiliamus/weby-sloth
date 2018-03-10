<?php
namespace Weby\Sloth\Func\Value;

class Median extends Base
{
	const FIELD_SUFFIX = 'median';
	
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
		
		$currValue = $this->median($store[$storeCol]);
	}
	
	private function median(&$data)
	{
		sort($data);
		
		$count = count($data);
		$middleVal = floor(($count - 1) / 2);
		if ($count % 2) {
			$median = $data[$middleVal];
		} else {
			$low = $data[$middleVal];
			$high = $data[$middleVal + 1];
			$median = ($low + $high) / 2;
		}
		
		return $median;
	}
}