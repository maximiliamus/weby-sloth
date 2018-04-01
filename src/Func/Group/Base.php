<?php
/**
 * Weby\Sloth
 *
 * @vendor      Weby
 * @package     Sloth
 * @link        https://github.com/maximiliamus/weby-sloth
 */

namespace Weby\Sloth\Func\Group;

/**
 * Base class for "group" functions, i.e.
 * functions that are applied to entire group
 * instead of value columns.
 */
abstract class Base extends \Weby\Sloth\Func\Base
{
	private $funcFieldName;
	
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
	
	/**
	 * Returns filed name that will hold function's result
	 * in the output array.
	 * 
	 * @return string
	 */
	public function getFieldName()
	{
		return $this->getFuncName();
	}
	
	public function getFuncName()
	{
		return ($this->funcFieldName
			? $this->funcFieldName
			: parent::getFuncName()
		);
	}
}