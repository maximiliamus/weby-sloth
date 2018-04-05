<?php
/**
 * Weby\Sloth
 *
 * @vendor      Weby
 * @package     Sloth
 * @link        https://github.com/maximiliamus/weby-sloth
 */

namespace Weby\Sloth\Func;

use Weby\Sloth\Sloth;
use Weby\Sloth\Utils;

/**
 * Base class for all functions.
 */
abstract class Base
{
	/**
	 * Function name.
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * Function alias.
	 * 
	 * @var string
	 */
	public $alias;
	
	/**
	 * Options of the function.
	 * 
	 * @var array
	 */
	public $options;
	
	/**
	 * Default options of the function.
	 * 
	 * @var array
	 */
	public $defaultOptions;
	
	/**
	 * Reference to the operation.
	 * 
	 * @var \Weby\Sloth\Operation\Base
	 */
	protected $operation;
	
	public function __construct(
		\Weby\Sloth\Operation\Base $operation,
		string $alias = null,
		array $options = null
	) {
		$this->name = $this->getFuncName();
		$this->alias = $alias ? $alias : $this->name;
		$this->operation = $operation;
		$this->options = $this->setOptions($options);
	}
	
	/**
	 * @param array $group Group of output data.
	 * @param string $groupCol Col name of output group.
	 * @param array $data Row of input data.
	 * @param string $dataCol Col name of input row.
	 * @param mixed $currValue Current value of column of input row.
	 * @param mixed $nextValue Next value of column of input row.
	 * @param array $store External aux store for the custom needs of the function.
	 */
	abstract public function onAddGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	);
	abstract public function onUpdateGroup(
		&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue
	);
	
	protected function getFuncName()
	{
		$parts = explode('\\', get_called_class());
		$lastPart = array_pop($parts);
		
		return lcfirst($lastPart);
	}
	
	protected function setOptions($options)
	{
		$options = Utils::normalizeArray($options);
		$defaultOptions = Utils::normalizeArray($this->defaultOptions);
		
		return array_merge($defaultOptions, $options);
	}
	
	protected function getStoreColumn($groupCol, $dataCol, $storeCol)
	{
		return ($groupCol
			. Sloth::FLAT_FIELD_SEPARATOR . $dataCol
			. Sloth::FLAT_FIELD_SEPARATOR . $storeCol
		);
	}
}