<?php
/**
 * Weby\Sloth
 *
 * @vendor      Weby
 * @package     Sloth
 * @link        https://github.com/maximiliamus/weby-sloth
 */

namespace Weby\Sloth\Func\Value;

use Weby\Sloth\Utils;
use Weby\Sloth\Exception;

/**
 * Accumulates values of a column.
 */
class Concat extends Base
{
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if (is_null($nextValue)) {
			$currValue = '';
		} else {
			$currValue = $nextValue;
		}
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if (is_null($nextValue))
			return;
		
		switch ($valueType = gettype($nextValue)) {
			case 'boolean':
				$currValue .= $nextValue ? '1' : '0';
				break;
				
			case 'array':
				$currValue = array_merge(
					$currValue,
					Utils::normalizeArray($nextValue)
				);
				break;
				
			default:
				$currValue .= (string) $nextValue;
				break;
		}
	}
}