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
 * Accumulates values of a column.
 */
class Accum extends Base
{
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if (is_null($nextValue)) {
			$currValue = [];
		} else {
			$currValue = [$nextValue];
		}
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if (is_null($nextValue))
			return;
		
		$currValue[] = $nextValue;
	}
}