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
 * Calculates sum of a column.
 */
class Sum extends Base
{
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if (is_null($nextValue)) {
			$currValue = 0;
		} else {
			$currValue = $nextValue;
		}
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		switch ($valueType = gettype($nextValue)) {
			case 'NULL':
				// Do nothing.
				break;
				
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
				throw new Exception(sprintf('Unsupported value type "%s".', $valueType));
		}
	}
}