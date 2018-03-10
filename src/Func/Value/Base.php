<?php
namespace Weby\Sloth\Func\Value;

abstract class Base extends \Weby\Sloth\Func\Base
{
	const FIELD_SUFFIX = '';
	
	public $funcFieldPostfix;
	
	public function __construct(
		\Weby\Sloth\Operation\Base $operation,
		$funcFieldSuffix = null,
		$options = null
	) {
		parent::__construct($operation, $options);
		
		if (!is_null($funcFieldSuffix)) {
			$this->funcFieldPostfix = (string) $funcFieldSuffix;
		} else {
			$funcClass = get_called_class();
			$this->funcFieldPostfix = $funcClass::FIELD_SUFFIX;
		}
	}
	
	protected function getStoreColumn($groupCol, $dataCol, $storeCol)
	{
		return $groupCol . '_' . $dataCol . '_' . $storeCol;
	}
}