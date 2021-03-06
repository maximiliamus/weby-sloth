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
use Weby\Sloth\Column;

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
		
		$this->assignColumnCols($columnCols);
		if (!$this->columnCols)
			throw new \Weby\Sloth\Exception('No column columns.');
		
		$this->group = new Group(
			$sloth,
			array_merge($groupCols, $columnCols),
			$valueCols
		);
		$this->group->setFlattenOutput(true);
	}
	
	protected function assignColumnCols($columnCols)
	{
		foreach ($columnCols as $colName) {
			$col = Column::new($colName);
			$this->columnCols[] = $col;
		}
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::count()
	 * 
	 * @param mixed $cols
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function count($cols = null)
	{
		$this->group->count($cols);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::accum()
	 * 
	 * @param mixed $cols
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function accum($cols = null)
	{
		$this->group->accum($cols);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::avg()
	 * 
	 * @param mixed $cols
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function avg($cols = null)
	{
		$this->group->avg($cols);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::first()
	 * 
	 * @param mixed $cols
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function first($cols = null)
	{
		$this->group->first($cols);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::median()
	 * 
	 * @param mixed $cols
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function median($cols = null)
	{
		$this->group->median($cols);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::min()
	 * 
	 * @param mixed $cols
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function max($cols = null)
	{
		$this->group->max($cols);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::min()
	 * 
	 * @param mixed $cols
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function min($cols = null)
	{
		$this->group->min($cols);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::mode()
	 * 
	 * @param mixed $cols
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function mode($cols = null)
	{
		$this->group->mode($cols);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::sum()
	 * 
	 * @param mixed $cols
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function sum($cols = null)
	{
		$this->group->sum($cols);
		
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
	
	protected function validatePerform()
	{
		if (!$this->group->getValueFuncs()) {
			// Apply default function.
			$this->group->first();
		}
	}
	
	protected function beginPerform()
	{
		$this->isOneCol = count($this->group->getValueCols()) == 1;
		
		$this->resetOutput();
		$this->resetGroups();
	}
	
	protected function doPerform()
	{
		$groupedData = $this->group->fetch();
		//print_r($groupedData);
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
	
	protected function endPerform()
	{
		// Do nothing.
	}
	
	protected function buildGroupFuncColumnName($groupFunc)
	{
		$groupColName = parent::buildGroupFuncColumnName($groupFunc);
		$pivotColName = $groupColName;
		
		return [$groupColName, $pivotColName];
	}
	
	protected function buildValueFuncColumnName($valueCol, $valueFunc, $isOneFunc, $columnCol = null)
	{
		$groupColName = parent::buildValueFuncColumnName($valueCol, $valueFunc, $isOneFunc);
		$pivotColName = $columnCol;
		
		if ($this->isOptimizeColumnNames) {
			$pivotColName = (
				  $this->isOneCol && $isOneFunc
				? $pivotColName
				: $pivotColName . Sloth::COLUMN_SEPARATOR . $groupColName
			);
		} else {
			$pivotColName = $pivotColName . Sloth::COLUMN_SEPARATOR . $groupColName;
		}
		
		return [$groupColName, $pivotColName];
	}
	
	private function &addGroup($key, $row)
	{
		$group = null;
		
		switch ($this->sloth->dataType) {
			case Sloth::DATA_ARRAY:
			case Sloth::DATA_ASSOC:
				$group = [];
				break;
				
			case Sloth::DATA_OBJECT:
				$group = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
				break;
		}
		
		$this->addGroup_processGroupCols($group, $row);
		$this->addGroup_processGroupFuncs($group, $row);
		$this->addGroup_processValueFuncs($group, $row);
		$this->addGroup_processAddedCols($group, $row);
		
		$this->groups[$key] = &$group;
		
		return $group;
	}
	
	private function addGroup_processGroupCols(&$group, &$row)
	{
		foreach ($this->groupCols as $groupCol) {
			$group[$groupCol->alias] = $row[$groupCol->alias];
			
			if (!$this->groups) {
				$this->outputCols[] = $groupCol->alias;
			}
		}
	}
	
	private function addGroup_processGroupFuncs(&$group, &$row)
	{
		foreach ($this->group->getGroupFuncs() as $groupFunc) {
			list($groupColName, $pivotColName) = $this->buildGroupFuncColumnName(
				$groupFunc
			);
			
			if (!$this->groups) {
				$this->outputCols[] = $pivotColName;
			}
			
			$group[$pivotColName] = $row[$groupColName];
		}
	}
	
	private function addGroup_processValueFuncs(&$group, &$row)
	{
		foreach ($this->columnCols as $columnCol) {
			if (is_null($row[$columnCol->name])) {
				// Skip null column values.
				continue;
			}
			foreach ($this->valueCols as $valueCol) {
				$valueFuncs = $this->group->getColToFuncMap()[$valueCol->alias];
				$isOneFunc = count($valueFuncs) == 1;
				foreach ($valueFuncs as $valueFunc) {
					list($groupColName, $pivotColName) = $this->buildValueFuncColumnName(
						$valueCol, $valueFunc, $isOneFunc, $row[$columnCol->name]
					);
					
					// If there are different number of cols
					// in rows then add missed ones.
					$this->propagateColumns($group);
					if (!isset($this->columnColsAliases[$pivotColName])) {
						$this->columnColsAliases[(string) $pivotColName] = true;
						$this->backPropagateColumn($pivotColName);
						
						$this->outputCols[] = $pivotColName;
						$this->outputValueCols[] = $pivotColName;
					}
					
					if ($this->isFlatOutput) {
						$group[$pivotColName] = $row[$groupColName];
					} else {
						$parts = explode(Sloth::COLUMN_SEPARATOR, $pivotColName);
						switch (count($parts)) {
							case 1:
								$group[$parts[0]] = $row[$groupColName];
								break;
								
							case 2:
								$group[$parts[0]][$parts[1]] = $row[$groupColName];
								break;
								
							case 3:
								$group[$parts[0]][$parts[1]][$parts[2]] = $row[$groupColName];
								break;
						}
					}
				}
			}
		}
	}
	
	private function addGroup_processAddedCols(&$group, &$row)
	{
		foreach ($this->addedCols as $addedCol => $colDef) {
			$value = null;
			if ($colDef instanceof \Closure) {
				$value = call_user_func($colDef, $group);
			} else {
				$value = $colDef;
			}
			
			if (!$this->groups) {
				$this->outputCols[] = $addedCol;
			}
			
			$group[$addedCol] = $value;
		}
	}
	
	private function &updateGroup($key, $row)
	{
		$group = &$this->groups[$key];
		
		$this->updateGroup_processGroupFuncs($group, $row);
		$this->updateGroup_processValueFuncs($group, $row);
		$this->updateGroup_processAddedCols($group, $row);
		
		return $group;
	}
	
	private function updateGroup_processGroupFuncs(&$group, &$row)
	{
		foreach ($this->group->getGroupFuncs() as $groupFunc) {
			list($groupColName, $pivotColName) = $this->buildGroupFuncColumnName(
				$groupFunc
			);
			
			$group[$pivotColName] = $row[$groupColName];
		}
	}
	
	private function updateGroup_processValueFuncs(&$group, &$row)
	{
		foreach ($this->columnCols as $columnCol) {
			if (is_null($row[$columnCol->name])) {
				// Skip null column values.
				continue;
			}
			foreach ($this->valueCols as $valueCol) {
				$valueFuncs = $this->group->getColToFuncMap()[$valueCol->alias];
				$isOneFunc = count($valueFuncs) == 1;
				foreach ($valueFuncs as $valueFunc) {
					list($groupColName, $pivotColName) = $this->buildValueFuncColumnName(
						$valueCol, $valueFunc, $isOneFunc, $row[$columnCol->name]
					);
					
					if (!isset($this->columnColsAliases[$pivotColName])) {
						$this->columnColsAliases[(string) $pivotColName] = true;
						$this->backPropagateColumn($pivotColName);
						
						$this->outputCols[] = $pivotColName;
						$this->outputValueCols[] = $pivotColName;
					}
					
					if ($this->isFlatOutput) {
						$group[$pivotColName] = $row[$groupColName];
					} else {
						$parts = explode(Sloth::COLUMN_SEPARATOR, $pivotColName);
						switch (count($parts)) {
							case 1:
								$group[$parts[0]] = $row[$groupColName];
								break;
								
							case 2:
								$group[$parts[0]][$parts[1]] = $row[$groupColName];
								break;
								
							case 3:
								$group[$parts[0]][$parts[1]][$parts[2]] = $row[$groupColName];
								break;
						}
					}
				}
			}
		}
	}
	
	private function updateGroup_processAddedCols(&$group, &$row)
	{
		foreach ($this->addedCols as $addedCol => $colDef) {
			$value = null;
			
			if ($colDef instanceof \Closure) {
				$value = call_user_func($colDef, $group);
			} else {
				$value = $colDef;
			}
			
			$group[$addedCol] = $value;
		}
	}
	
	private function backPropagateColumn($col)
	{
		if ($this->isFlatOutput) {
			foreach ($this->groups as $key => &$group) {
				if (!array_key_exists($col, $group)) {
					$group[$col] = null;
				}
			}
		} else {
			$parts = explode(Sloth::COLUMN_SEPARATOR, $col);
			switch (count($parts)) {
				case 1:
					foreach ($this->groups as $key => &$group) {
						if (!array_key_exists($parts[0], $group)) {
							$group[$parts[0]] = null;
						}
					}
					break;
					
				case 2:
					foreach ($this->groups as $key => &$group) {
						if (
							   !array_key_exists($parts[0], $group)
							|| !array_key_exists($parts[1], $group[$parts[0]])
						) {
							$group[$parts[0]][$parts[1]] = null;
						}
					}
					break;
					
				case 3:
					foreach ($this->groups as $key => &$group) {
						if (
							   !array_key_exists($parts[0], $group)
							|| !array_key_exists($parts[1], $group[$parts[0]])
							|| !array_key_exists($parts[2], $group[$parts[0]][$parts[1]])
						) {
							$group[$parts[0]][$parts[1]][$parts[2]] = null;
						}
					}
					break;
			}
		}
	}
	
	private function propagateColumns(&$group)
	{
		foreach ($this->columnColsAliases as $columnColAlias => $isSet) {
			if ($this->isFlatOutput) {
				if (!array_key_exists($columnColAlias, $group)) {
					$group[$columnColAlias] = null;
				}
			} else {
				$parts = explode(Sloth::COLUMN_SEPARATOR, $columnColAlias);
				switch (count($parts)) {
					case 1:
						if (!array_key_exists($parts[0], $group)) {
							$group[$parts[0]] = null;
						}
						break;
						
					case 2:
						if (
							   !array_key_exists($parts[0], $group)
							|| !array_key_exists($parts[1], $group[$parts[0]])
						) {
							$group[$parts[0]][$parts[1]] = null;
						}
						break;
						
					case 3:
						if (
							   !array_key_exists($parts[0], $group)
							|| !array_key_exists($parts[1], $group[$parts[0]])
							|| !array_key_exists($parts[2], $group[$parts[0]][$parts[1]])
						) {
							$group[$parts[0]][$parts[1]][$parts[2]] = null;
						}
						break;
				}
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
	
	/**
	 * {@inheritDoc}
	 * @see \Weby\Sloth\Operation\Base::setScale()
	 */
	public function setScale(int $scale)
	{
		$this->group->setScale($scale);
		
		return parent::setScale($scale);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Weby\Sloth\Operation\Base::setOptimizeColumnNames()
	 */
	public function setOptimizeColumnNames(bool $value)
	{
		$this->group->setOptimizeColumnNames($value);
		
		return parent::setOptimizeColumnNames($value);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Weby\Sloth\Operation\Base::setFlatOutput()
	 */
	public function setFlattenOutput(bool $value)
	{
		return parent::setFlatOutput($value);
	}
}