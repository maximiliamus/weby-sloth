<?php
namespace Weby\Sloth\Func\Value;

class First extends Base
{
	const FIELD_POSTFIX = 'first';
	
	public $defaultOptions = array(
		'flat' => false
	);
	
	public function onAddGroup(&$group, $groupCol, &$row, $valueCol, &$currValue, &$nextValue)
	{
		if ($this->options['flat']) {
			$currValue = $nextValue;
		} else {
			$currValue = array($nextValue);
		}
	}
	
	public function onUpdateGroup(&$group, $groupCol, &$row, $valueCol, &$currValue, &$nextValue)
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