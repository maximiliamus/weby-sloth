<?php
/**
 * Weby\Sloth
 *
 * @vendor      Weby
 * @package     Sloth
 * @link        https://github.com/maximiliamus/weby-sloth
 */

namespace Weby\Sloth\Func\Group;

/**
 *  Counts record number in a group.
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