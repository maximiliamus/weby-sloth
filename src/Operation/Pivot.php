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
		$this->group->setFlatOutput(true);
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
	
	protected function validatePerform()
	{
		// Do nothing.
	}
	
	protected function beginPerform()
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
	
	protected function doPerform()
	{
		$groupedData = $this->group->fetch();
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
	
	protected function buildColumnName($valueCol, $func, $columnCol = null)
	{
		$groupColName = parent::buildColumnName($valueCol, $func);
		$pivotColName = $columnCol;
		
		if ($this->isOptimizeColumnNames) {
			$pivotColName = (
				  $this->isOneCol && $this->isOneFunc
				? $pivotColName
				: $pivotColName . Sloth::FLAT_FIELD_SEPARATOR . $groupColName
			);
		} else {
			$pivotColName = $pivotColName . Sloth::FLAT_FIELD_SEPARATOR . $groupColName;
		}
		
		return [$groupColName, $pivotColName];
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
			foreach ($this->valueCols as $valueCol) {
				foreach ($this->group->getFuncs() as $func) {
					list($groupColName, $pivotColName) = $this->buildColumnName(
						$valueCol, $func, $row[$columnCol->name]
					);
					
					// If there are different number of cols
					// in rows then add missed ones.
					$this->propagateColumns($group);
					if (!isset($this->columnColsAliases[$pivotColName])) {
						$this->columnColsAliases[$pivotColName] = true;
						$this->backPropagateColumn($pivotColName);
						
						$this->outputCols[] = $pivotColName;
						$this->outputValueCols[] = $pivotColName;
					}
					
					if ($this->isFlatOutput) {
						$group[$pivotColName] = $row[$groupColName];
					} else {
						$parts = explode(Sloth::FLAT_FIELD_SEPARATOR, $pivotColName);
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
		
		$this->groups[$key] = &$group;
		
		return $group;
	}
	
	private function &updateGroup($key, $row)
	{
		$group = &$this->groups[$key];
		
		foreach ($this->columnCols as $columnCol) {
			foreach ($this->valueCols as $valueCol) {
				foreach ($this->group->getFuncs() as $func) {
					list($groupColName, $pivotColName) = $this->buildColumnName(
						$valueCol, $func, $row[$columnCol->name]
					);
					
					if (!isset($this->columnColsAliases[$pivotColName])) {
						$this->columnColsAliases[$pivotColName] = true;
						$this->backPropagateColumn($pivotColName);
						
						$this->outputCols[] = $pivotColName;
						$this->outputValueCols[] = $pivotColName;
					}
					
					if ($this->isFlatOutput) {
						$group[$pivotColName] = $row[$groupColName];
					} else {
						$parts = explode(Sloth::FLAT_FIELD_SEPARATOR, $pivotColName);
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
		
		foreach ($this->addedCols as $addedCol => $colDef) {
			$value = null;
			if ($colDef instanceof \Closure) {
				$value = call_user_func($colDef, $group);
			} else {
				$value = $colDef;
			}
			$group[$addedCol] = $value;
		}
		
		return $group;
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
			$parts = explode(Sloth::FLAT_FIELD_SEPARATOR, $col);
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
				$parts = explode(Sloth::FLAT_FIELD_SEPARATOR, $columnColAlias);
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
	
	public function setScale(int $scale)
	{
		$this->group->setScale($scale);
		
		return parent::setScale($scale);
	}
	
	public function setOptimizeColumnNames(bool $value)
	{
		$this->group->setOptimizeColumnNames($value);
		
		return parent::setOptimizeColumnNames($value);
	}
	
	public function setFlatOutput(bool $value)
	{
		return parent::setFlatOutput($value);
	}
}