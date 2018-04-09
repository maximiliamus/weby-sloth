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
 * Calculates average (mean) value of a column.
 */
class Count extends Base
{
	/**
	 * {@inheritDoc}
	 * @see \Weby\Sloth\Func\Base::onAddGroup()
	 */
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if (is_null($nextValue)) {
			$currValue = 0;
		} else {
			$currValue = 1;
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Weby\Sloth\Func\Base::onUpdateGroup()
	 */
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if (!is_null($nextValue)) {
			$currValue++;
		}
	}
}