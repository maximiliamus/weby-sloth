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
 * Calculates mode of a column.
 */
class Mode extends Base
{
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		$storeCol = $this->getStoreColumn($groupCol, $dataCol, 'accum');
		
		$store = &$this->operation->getStore();
		$store[$storeCol] = [];
		if (!is_null($nextValue)) {
			$store[$storeCol][] = (string) $nextValue;
		}
		
		$currValue = $nextValue;
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if (is_null($nextValue))
			return;
		
		$storeCol = $this->getStoreColumn($groupCol, $dataCol, 'accum');
		
		$store = &$this->operation->getStore();
		$store[$storeCol][] = (string) $nextValue;
		
		$currValue = $this->mode($store[$storeCol]);
	}
	
	private function mode(&$data)
	{
		$result = null;
		
		$vals = array_count_values($data);
		if ($vals) {
			arsort($vals);
			reset($vals);
			$result = key($vals);
		}
		
		return $result;
	}
}