<?php
namespace Weby\Sloth\Func\Value;

class Min extends Base
{
	const FIELD_SUFFIX = 'sum';
	
	public function onAddGroup(&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue, &$store)
	{
		$currValue = $nextValue;
	}
	
	public function onUpdateGroup(&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue, &$store)
	{
		if ($nextValue < $currValue) {
			$currValue = $nextValue;
		}
	}
}