<?php
namespace Weby\Sloth\Func\Group;

abstract class Base extends \Weby\Sloth\Func\Base
{
	const FIELD_NAME = '';
	
	public $funcFieldName;
	
	public function __construct($funcFieldName = null, $options = null)
	{
		parent::__construct($options);
		
		if (!is_null($funcFieldName)) {
			$this->funcFieldName = (string) $funcFieldName;
		} else {
			$funcClass = get_called_class();
			$this->funcFieldName = $funcClass::FIELD_NAME;
		}
	}
}