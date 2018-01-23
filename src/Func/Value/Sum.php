<?php
namespace Weby\Sloth\Func\Value;

class Sum extends Base
{
	const FIELD_POSTFIX = 'sum';
	
	public function onAddGroup(&$group, $groupCol, &$row, $valueCol, &$currValue, &$nextValue)
	{
		$currValue = $nextValue;
	}
	
	public function onUpdateGroup(&$group, $groupCol, &$row, $valueCol, &$currValue, &$nextValue)
	{
		$currValue += $nextValue;
	}
}