<?php
namespace Weby\Sloth\Func;

abstract class Base
{
	public $options;
	public $defaultOptions;
	
	/**
	 * @param array $group Group of output data.
	 * @param string $groupCol Col name of output group.
	 * @param array $data Row of input data.
	 * @param string $dataCol Col name of input row.
	 * @param mixed $currValue Current value of column of input row.
	 * @param mixed $nextValue Next value of column of input row.
	 * @param array $store External aux store for the custom needs of the function.
	 */
	abstract public function onAddGroup(&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue, &$store);
	abstract public function onUpdateGroup(&$group, $groupCol, &$data, $dataCol, &$currValue, &$nextValue, &$store);
	
	public function __construct($options = null)
	{
		$this->options = $this->setOptions((array) $options);
	}
	
	protected function setOptions(array $options)
	{
		return array_merge((array) $this->defaultOptions, $options);
	}
}