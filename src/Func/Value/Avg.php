<?php
namespace Weby\Sloth\Func\Value;

use \Weby\Sloth\Exception;

class Avg extends Base
{
	const FIELD_SUFFIX = 'avg';
	
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		$sumCol   = $this->getStoreColumn($groupCol, $dataCol, 'sum');
		$countCol = $this->getStoreColumn($groupCol, $dataCol, 'count');
		
		$store = &$this->operation->getStore();
		$store[$sumCol] = $nextValue;
		$store[$countCol] = 1;
		
		$currValue = $nextValue;
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		$sumCol   = $this->getStoreColumn($groupCol, $dataCol, 'sum');
		$countCol = $this->getStoreColumn($groupCol, $dataCol, 'count');
		
		$store = &$this->operation->getStore();
		
		switch ($valueType = gettype($currValue)) {
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
				throw new Exception('Unsupported value type.');
		}
		$store[$countCol]++;
		
		$currValue = (double) bcdiv(
			$store[$sumCol],
			$store[$countCol],
			$this->operation->getScale()
		);
	}
}