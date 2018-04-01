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
 * Base class for "value" functions, i.e.
 * functions that are applied to value column
 * instead of entire group.
 */
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
	
	/**
	 * Returns filed name that will hold function's result
	 * in the output array. Function will be applied to a specified
	 * column of input data.
	 * 
	 * @return string
	 */
	public function getFieldName($dataCol)
	{
		$funcName = $this->getFuncName();
		
		return $dataCol . ($funcName ?  '_' . $funcName : '');
	}
	
	public function getFuncName()
	{
		return (!is_null($this->funcFieldSuffix)
			? $this->funcFieldSuffix
			: parent::getFuncName()
		);
	}
}