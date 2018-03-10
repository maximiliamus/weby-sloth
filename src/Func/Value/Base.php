<?php
namespace Weby\Sloth\Func\Value;

abstract class Base extends \Weby\Sloth\Func\Base
{
	private $funcFieldSuffix;
	
	public function __construct(
		\Weby\Sloth\Operation\Base $operation,
		$funcFieldSuffix = null,
		$options = null
	) {
		parent::__construct($operation, $options);
		
		if (!is_null($funcFieldSuffix)) {
			$this->funcFieldSuffix = (string) $funcFieldSuffix;
		}
	}
	
	public function getFieldName($dataCol)
	{
		$suffix = (!is_null($this->funcFieldSuffix)
			? $this->funcFieldSuffix
			: $this->getFuncName()
		);
		
		return $dataCol . ($suffix ?  '_' . $suffix : '');
	}
}