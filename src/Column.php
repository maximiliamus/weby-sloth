<?php
/**
 * Weby\Sloth
 *
 * @vendor      Weby
 * @package     Sloth
 * @link        https://github.com/maximiliamus/weby-sloth
 */

namespace Weby\Sloth;

/**
 * Column of input data.
 */
class Column
{
	/**
	 * Name of column.
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * Alias of column.
	 * 
	 * @var string
	 */
	public $alias;
	
	/**
	 * Fabric to create new column instances.
	 * 
	 * @param string $name
	 * @return \Weby\Sloth\Column
	 */
	static public function new($name)
	{
		return new Column($name);
	}
	
	/**
	 * Constructs column object.
	 * 
	 * @param string $name Name of column.
	 */
	public function __construct($name)
	{
		$this->name = $name;
		$this->alias = $name;
	}
	
	/**
	 * Sets alias for column via fluent interface.
	 * 
	 * @param string $alias Alias os column.
	 */
	public function as ($alias)
	{
		$this->alias = $alias;
		
		return $this;
	}
}