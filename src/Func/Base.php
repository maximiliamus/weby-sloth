<?php
namespace Weby\Sloth\Func;

abstract class Base
{
	public $options;
	public $defaultOptions;
	
	abstract public function onAddGroup(&$group, $groupCol, &$row, $valueCol, &$currValue, &$nextValue);
	abstract public function onUpdateGroup(&$group, $groupCol, &$row, $valueCol, &$currValue, &$nextValue);
	
	public function __construct($options = null)
	{
		$this->options = $this->setOptions((array) $options);
	}
	
	protected function setOptions(array $options)
	{
		return array_merge((array) $this->defaultOptions, $options);
	}
}