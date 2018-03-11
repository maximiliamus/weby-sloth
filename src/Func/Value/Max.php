<?php
namespace Weby\Sloth\Func\Value;

class Max extends Base
{
	const FIELD_SUFFIX = 'max';
	
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		$currValue = $nextValue;
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if ($currValue < $nextValue) {
			$currValue = $nextValue;
		}
	}
}