<?php
namespace Weby\Sloth\Func\Value;

class Sum extends Base
{
	const FIELD_SUFFIX = 'sum';
	
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		$currValue = $nextValue;
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		switch (gettype($currValue)) {
			case 'integer':
				$currValue = (integer) bcadd($currValue, $nextValue);
				break;
				
			case 'double':
				$currValue = (double) bcadd(
					$currValue,
					$nextValue,
					$this->operation->getScale()
				);
				break;
				
			default:
				$currValue += $nextValue;
				break;
		}
	}
}