<?php
namespace Weby\Sloth\Func\Value;

class Avg extends Base
{
	const FIELD_SUFFIX = 'avg';
	
	public function onAddGroup(&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue, &$store)
	{
		$sumCol   = $this->getStoreColumn($groupCol, $dataCol, 'sum');
		$countCol = $this->getStoreColumn($groupCol, $dataCol, 'count');
		
		$store[$sumCol] = $nextValue;
		$store[$countCol] = 1;
		
		$currValue = $nextValue;
	}
	
	public function onUpdateGroup(&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue, &$store)
	{
		$sumCol   = $this->getStoreColumn($groupCol, $dataCol, 'sum');
		$countCol = $this->getStoreColumn($groupCol, $dataCol, 'count');
		
		$store[$sumCol] += $nextValue;
		$store[$countCol]++;
		
		$currValue = $store[$sumCol] / $store[$countCol];
	}
}