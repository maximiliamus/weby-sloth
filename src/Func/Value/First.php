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
 * Accumulates only a first value of a column.
 * 
 * @see \Weby\Sloth\Func\Value\Accum
 */
class First extends Base
{
	public $defaultOptions = [
		'flat' => false
	];
	
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if ($this->options['flat']) {
			$currValue = $nextValue;
		} else {
			$currValue = [$nextValue];
		}
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if (is_null($nextValue))
			return;
		
		if (!is_null($currValue)) {
			// Do nothing.
			// First non-null value is already stored.
			return;
		}
		
		if ($this->options['flat']) {
			$currValue = $nextValue;
		} else {
			$currValue = [$nextValue];
		}
	}
}