<?php
namespace Weby\Sloth\Func;

abstract class Base
{
	public $options;
	public $defaultOptions;
	
	/**
	 * 
	 * @var \Weby\Sloth\Operation\Base
	 */
	protected $operation;
	
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
	
	public function __construct(
		\Weby\Sloth\Operation\Base $operation,
		$options = null
	) {
		$this->operation = $operation;
		$this->options = $this->setOptions((array) $options);
	}
	
	protected function setOptions(array $options)
	{
		return array_merge((array) $this->defaultOptions, $options);
	}
}