<?php
/**
 * Weby\Sloth
 *
 * @vendor      Weby
 * @package     Sloth
 * @link        https://github.com/maximiliamus/weby-sloth
 */

namespace Weby\Sloth\Func;

use Weby\Sloth\Exception;
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
	
	/**
	 * "Casts" mixed variable to func.
	 * Auxiliary method for dev purposes.
	 * After such cast IDE undestands type of var
	 * and we can use code completion functionallity.
	 * 
	 * @return \Weby\Sloth\Func\Base
	 */
	static public function cast($object) : \Weby\Sloth\Func\Base
	{
		return $object;
	}
	
	public function __construct(\Weby\Sloth\Operation\Base $operation)
	{
		$this->operation = $operation;
		$this->name = $this->getFuncName();
		$this->alias = $this->name;
		$this->options = $this->defaultOptions;
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
	
	private function ensureKnownOption($name)
	{
		if (!isset($this->defaultOptions[$name])) {
			throw new Exception(sprintf('Unknown option "%s".', $name));
		}
	}
	
	/**
	 * Sets single function's option.
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function setOption($name, $value)
	{
		$this->ensureKnownOption($name);
		
		$this->options[$name] = $value;
	}
	
	/**
	 * Sets multiple function's options.
	 * 
	 * @param array $options
	 */
	public function setOptions($options)
	{
		$options = Utils::normalizeArray($options);
		
		foreach ($options as $name => $value) {
			$this->setOption($name, $value);
		}
	}
	
	protected function getStoreColumn($groupCol, $dataCol, $storeCol)
	{
		return ($groupCol
			. Sloth::ARRAY_OUTPUT_COLUMN_SEPARATOR . $dataCol
			. Sloth::ARRAY_OUTPUT_COLUMN_SEPARATOR . $storeCol
		);
	}
}