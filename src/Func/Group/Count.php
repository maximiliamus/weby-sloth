<?php
namespace Weby\Sloth\Func\Group;

class Count extends Base
{
	const FIELD_NAME = 'count';
	
	public function onAddGroup(
		&$group, $groupCol, &$row, $valueCol, &$currValue, &$nextValue
	) {
		$currValue = 1;
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$row, $valueCol, &$currValue, &$nextValue
	) {
		$currValue++;
	}
}