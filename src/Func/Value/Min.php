<?php
namespace Weby\Sloth\Func\Value;

class Min extends Base
{
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		$currValue = $nextValue;
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if ($nextValue < $currValue) {
			$currValue = $nextValue;
		}
	}
}