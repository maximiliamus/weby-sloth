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
class Accum extends Base
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
		
		if ($this->options['flat']) {
			switch ($valueType = gettype($currValue)) {
				case 'string':
					$currValue .= (string) $nextValue;
					break;
					
				case 'integer':
					$currValue += (integer) $nextValue;
					break;
					
				case 'double':
					$currValue += (float) $nextValue;
					break;
					
				case 'boolean':
					$currValue = $currValue && (boolean) $nextValue;
					break;
					
				case 'array':
					$currValue = array_merge(
						$currValue,
						Utils::normalizeArray($nextValue)
					);
					break;
					
				default:
					throw new Exception(sprintf('Unsupported value type "%s".', $valueType));
			}
		} else {
			$currValue[] = $nextValue;
		}
	}
}