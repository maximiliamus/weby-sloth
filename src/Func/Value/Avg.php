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
class Avg extends Base
{
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		$sumCol   = $this->getStoreColumn($groupCol, $dataCol, 'sum');
		$countCol = $this->getStoreColumn($groupCol, $dataCol, 'count');
		
		$store = &$this->operation->getStore();
		if (is_null($nextValue)) {
			$store[$sumCol]   = 0;
			$store[$countCol] = 0;
		} else {
			$store[$sumCol]   = $nextValue;
			$store[$countCol] = 1;
		}
		$currValue = $nextValue;
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		$sumCol   = $this->getStoreColumn($groupCol, $dataCol, 'sum');
		$countCol = $this->getStoreColumn($groupCol, $dataCol, 'count');
		
		$store = &$this->operation->getStore();
		
		switch ($valueType = gettype($currValue)) {
			case 'NULL':
				// Do nothing.
				break;
				
			case 'integer':
				$store[$sumCol] = (integer) bcadd(
					$store[$sumCol],
					$nextValue
				);
				break;
				
			case 'double':
				$store[$sumCol] = (double) bcadd(
					$store[$sumCol],
					$nextValue,
					$this->operation->getScale()
				);
				break;
				
			default:
				throw new Exception(sprintf('Unsupported value type "%s".', $valueType));
		}
		$store[$countCol]++;
		
		$currValue = (double) bcdiv(
			$store[$sumCol],
			$store[$countCol],
			$this->operation->getScale()
		);
	}
}