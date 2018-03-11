<?php
/**
 * Weby\Sloth
 *
 * @vendor      Weby
 * @package     Sloth
 * @link        https://github.com/maximiliamus/weby-sloth
 */

namespace Weby\Sloth\Func\Value;

/**
 * Calculates sum of a column.
 */
class Sum extends Base
{
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