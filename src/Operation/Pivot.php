<?php
/**
 * Weby\Sloth
 *
 * @vendor      Weby
 * @package     Sloth
 * @link        https://github.com/maximiliamus/weby-sloth
 */

namespace Weby\Sloth\Operation;

use Weby\Sloth\Sloth;
use Weby\Sloth\Utils;

/**
 * Pivot (pivot table) operation.
 */
class Pivot extends Base
{
	private $columnCols = [];
	private $columnColsAliases = [];
	
	private $addedCols = [];
	
	private $groupedData = null;
	private $groups = [];
	
	private $isOneFunc = false;
	private $isOneCol = false;
	
	/**
	 * @var \Weby\Sloth\Operation\Group
	 */
	private $group = null;
	
	public function __construct(Sloth $sloth, $groupCols, $valueCols, $columnCols)
	{
		$groupCols  = Utils::normalizeArray($groupCols);
		$valueCols  = Utils::normalizeArray($valueCols);
		$columnCols = Utils::normalizeArray($columnCols);
		
		parent::__construct($sloth, $groupCols, $valueCols);
		
		$this->columnCols = $columnCols;
		if (!$this->columnCols)
			throw new \Weby\Sloth\Exception('No column columns.');
		
		$this->group = new Group(
			$sloth,
			array_merge($groupCols, $columnCols),
			$valueCols
		);
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::count()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function count($fieldName = null, $options = null)
	{
		$this->group->count($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::accum()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function accum($fieldName = null, $options = null)
	{
		$this->group->accum($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::avg()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function avg($fieldName = null, $options = null)
	{
		$this->group->avg($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::first()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function first($fieldName = null, $options = null)
	{
		$this->group->first($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::median()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function median($fieldName = null, $options = null)
	{
		$this->group->median($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::min()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function max($fieldName = null, $options = null)
	{
		$this->group->max($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::min()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function min($fieldName = null, $options = null)
	{
		$this->group->min($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::mode()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function mode($fieldName = null, $options = null)
	{
		$this->group->mode($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::sum()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function sum($fieldName = null, $options = null)
	{
		$this->group->sum($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * Adds additional column to the output.
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function addColumn($name, $value = null)
	{
		$this->addedCols[$name] = $value;
		
		return $this;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Weby\Sloth\Operation\Base::perform()
	 */
	protected function perform()
	{
		$this->beginPerform();
		$this->doPerform();
		$this->endPerform();
	}
	
	private function beginPerform()
	{
		if (!$this->group->getFuncs()) {
			// Apply default function.
			$this->group->first();
		}
		
		$this->isOneFunc = count($this->group->getFuncs()) == 1;
		$this->isOneCol = count($this->group->getValueCols()) == 1;
		
		$this->resetOutput();
		$this->resetGroups();
	}
	
	private function doPerform()
	{
		$groupedData = $this->group->select();
		foreach ($groupedData as $row) {
			$key = $this->getGroupKey($row);
			if (!$this->isGroup($key)) {
				$group = &$this->addGroup($key, $row);
				$this->output[] = &$group;
			} else {
				$group = &$this->updateGroup($key, $row);
			}
		}
	}
	
	private function endPerform()
	{
		// Do nothing.
	}
	
	private function &addGroup($key, $row)
	{
		$group = [];
		
		foreach ($this->groupCols as $groupCol) {
			$group[$groupCol->alias] = $row[$groupCol->alias];
			
			if (!$this->groups) {
				$this->outputCols[] = $groupCol->alias;
			}
		}
		
		foreach ($this->columnCols as $columnCol) {
			foreach ($this->group->getFuncs() as $func) {
				if ($func instanceof \Weby\Sloth\Func\Group\Base) {
					$fieldName = ($this->isOneCol
						? $func->getFuncName()
						: $func->getFieldName()
					);
					$fieldNameAlias = ($this->isOneFunc
						? $row[$columnCol]
						: (
							  $fieldName
							? $row[$columnCol] . '_' . $fieldName
							: $row[$columnCol]
						)
					);
					
					// If there are different number of cols
					// in rows then add missed ones.
					$this->propagateColumns($group);
					if (!isset($this->columnColsAliases[$fieldNameAlias])) {
						$this->columnColsAliases[$fieldNameAlias] = true;
						$this->backPropagateColumn($fieldNameAlias);
						$this->outputCols[] = $fieldNameAlias;
					}
					
					$group[$fieldNameAlias] = $row[$fieldName];
				} else {
					foreach ($this->valueCols as $valueCol) {
						$fieldName = ($this->isOneCol
							? $func->getFuncName()
							: $func->getFieldName($valueCol->alias)
						);
						$fieldNameAlias = ($this->isOneFunc
							? $row[$columnCol]
							: (
								  $fieldName
								? $row[$columnCol] . '_' . $fieldName
								: $row[$columnCol]
							)
						);
						
						// If there are different number of cols
						// in rows then add missed ones.
						$this->propagateColumns($group);
						if (!isset($this->columnColsAliases[$fieldNameAlias])) {
							$this->columnColsAliases[$fieldNameAlias] = true;
							$this->backPropagateColumn($fieldNameAlias);
							$this->outputCols[] = $fieldNameAlias;
						}
						
						$group[$fieldNameAlias] = $row[$fieldName];
					}
				}
			}
		}
		
		foreach ($this->addedCols as $addedCol => $colDef) {
			$value = null;
			if ($colDef instanceof \Closure) {
				$value = call_user_func($colDef, $group);
			} else {
				$value = $colDef;
			}
			
			if (!$this->groups) {
				$this->outputCols[] = $fieldNameAlias;
			}
						
			$group[$addedCol] = $value;
		}
		
		$this->groups[$key] = &$group;
		
		return $group;
	}
	
	private function &updateGroup($key, $row)
	{
		$result = &$this->groups[$key];
		
		foreach ($this->columnCols as $columnCol) {
			foreach ($this->group->getFuncs() as $func) {
				if ($func instanceof \Weby\Sloth\Func\Group\Base) {
					$fieldName = ($this->isOneCol
						? $func->getFuncName()
						: $func->getFieldName()
					);
					$fieldNameAlias = ($this->isOneFunc
						? $row[$columnCol]
						: (
							  $fieldName
							? $row[$columnCol] . '_' . $fieldName
							: $row[$columnCol]
						)
					);
					
					if (!isset($this->columnColsAliases[$fieldNameAlias])) {
						$this->columnColsAliases[$fieldNameAlias] = true;
						$this->backPropagateColumn($fieldNameAlias);
						$this->outputCols[] = $fieldNameAlias;
					}
					
					$result[$fieldNameAlias] = $row[$fieldName];
				} else {
					foreach ($this->valueCols as $valueCol) {
						$fieldName = ($this->isOneCol
							? $func->getFuncName()
							: $func->getFieldName($valueCol->alias)
						);
						$fieldNameAlias = ($this->isOneFunc
							? $row[$columnCol]
							: (
								  $fieldName
								? $row[$columnCol] . '_' . $fieldName
								: $row[$columnCol]
							)
						);
						
						if (!isset($this->columnColsAliases[$fieldNameAlias])) {
							$this->columnColsAliases[$fieldNameAlias] = true;
							$this->backPropagateColumn($fieldNameAlias);
							$this->outputCols[] = $fieldNameAlias;
						}
						
						$result[$fieldNameAlias] = $row[$fieldName];
					}
				}
			}
		}
		
		foreach ($this->addedCols as $addedCol => $colDef) {
			$value = null;
			if ($colDef instanceof \Closure) {
				$value = call_user_func($colDef, $result);
			} else {
				$value = $colDef;
			}
			$result[$addedCol] = $value;
		}
		
		return $result;
	}
	
	private function backPropagateColumn($col)
	{
		foreach ($this->groups as $key => &$row) {
			if (!array_key_exists($col, $row)) {
				$row[$col] = null;
			}
		}
	}
	
	private function propagateColumns(&$row)
	{
		foreach ($this->columnColsAliases as $columnColAlias => $isSet) {
			if (!array_key_exists($columnColAlias, $row)) {
				$row[$columnColAlias] = null;
			}
		}
	}
	
	private function getGroupKey($row)
	{
		$result = '';
		
		foreach ($this->groupCols as $groupCol) {
			$result .= $row[$groupCol->alias];
		}
		
		$result = md5($result);
		
		return $result;
	}
	
	private function resetOutput()
	{
		$this->output = [];
	}
	
	private function resetGroups()
	{
		$this->groups = [];
	}
	
	private function isGroup($key)
	{
		return isset($this->groups[$key]);
	}
	
	public function setScale($scale)
	{
		$this->group->setScale($scale);
		
		return parent::setScale($scale);
	}
}