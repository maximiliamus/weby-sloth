<?php
namespace Weby\Sloth\Func\Value;

use Weby\Sloth\Exception;

class Accum extends Base
{
	public $defaultOptions = array(
		'flat' => false
	);
	
	public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if ($this->options['flat']) {
			$currValue = $nextValue;
		} else {
			$currValue = array($nextValue);
		}
	}
	
	public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	) {
		if (is_null($nextValue))
			return;
		
		if ($this->options['flat']) {
			switch (gettype($currValue)) {
				case 'string':  $currValue  .= (string)  $nextValue; break;
				case 'integer': $currValue  += (integer) $nextValue; break;
				case 'double':  $currValue  += (float)   $nextValue; break;
				case 'boolean': $currValue   = $currValue && (boolean) $nextValue; break;
				case 'array':   $currValue   = array_merge($currValue, (array) $nextValue); break;
				default:
					throw new Exception('Unsupported value type.');
			}
		} else {
			$currValue[] = $nextValue;
		}
	}
}