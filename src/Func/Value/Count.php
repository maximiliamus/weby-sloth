<?php
/**
 * Weby\Sloth
 *
 * @vendor      Weby
 * @package     Sloth
 * @link        https://github.com/maximiliamus/weby-sloth
 */

namespace Weby\Sloth\Func\Value;

use Weby\Sloth\Exception;

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
		$currValue = 1;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Weby\Sloth\Func\Base::onUpdateGroup()
	 */
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		$currValue++;
	}
}