<?php
namespace Weby\Sloth\Func\Group;

abstract class Base extends \Weby\Sloth\Func\Base
{
	public $funcFieldName;
	
	public function __construct(
		\Weby\Sloth\Operation\Base $operation,
		$funcFieldName = null,
		$options = null
	) {
		parent::__construct($operation, $options);
		
		if (!is_null($funcFieldName)) {
			$this->funcFieldName = (string) $funcFieldName;
		}
	}
	
	public function getFieldName()
	{
		return ($this->funcFieldName
			? $this->funcFieldName
			: $this->getFuncName()
		);
	}
}