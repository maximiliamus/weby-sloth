<?php
namespace Weby\Sloth\Func\Value;

class First extends Base
{
	const FIELD_SUFFIX = 'first';
	
	public $defaultOptions = array(
		'flat' => false
	);
	
	public function onAddGroup(&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue, &$store)
	{
		if ($this->options['flat']) {
			$currValue = $nextValue;
		} else {
			$currValue = array($nextValue);
		}
	}
	
	public function onUpdateGroup(&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue, &$store)
	{
		if (is_null($nextValue))
			return;
		
		if (!is_null($currValue)) {
			// Do nothing.
			// First non-null value is already stored.
			return;
		}
		
		if ($this->options['flat']) {
			$currValue = $nextValue;
		} else {
			$currValue = array($nextValue);
		}
	}
}