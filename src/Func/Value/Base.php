<?php
namespace Weby\Sloth\Func\Value;

abstract class Base extends \Weby\Sloth\Func\Base
{
	const FIELD_POSTFIX = '';
	
	public $funcFieldPostfix;
	
	public function __construct($funcFieldPostfix = null, $options = null)
	{
		parent::__construct($options);
		
		if (!is_null($funcFieldPostfix)) {
			$this->funcFieldPostfix = (string) $funcFieldPostfix;
		} else {
			$funcClass = get_called_class();
			$this->funcFieldPostfix = $funcClass::FIELD_POSTFIX;
		}
	}
}