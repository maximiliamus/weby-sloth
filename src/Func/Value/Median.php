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
 * Calculates median of a column.
 */
class Median extends Base
{
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		$storeCol = $this->getStoreColumn($groupCol, $dataCol, 'accum');
		
		$store = &$this->operation->getStore();
		$store[$storeCol] = [];
		if (!is_null($nextValue)) {
			$store[$storeCol][] =  $nextValue;
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
		$store[$storeCol][] = $nextValue;
		
		$currValue = $this->median($store[$storeCol]);
	}
	
	private function median(&$data)
	{
		sort($data);
		
		$count = count($data);
		$middleVal = floor(($count - 1) / 2);
		if ($count % 2) {
			$median = $data[$middleVal];
		} else {
			$low = $data[$middleVal];
			$high = $data[$middleVal + 1];
			$median = (double) bcdiv(
				bcadd(
					$low,
					$high,
					$this->operation->getScale()
				),
				2,
				$this->operation->getScale()
			);
		}
		
		return $median;
	}
}