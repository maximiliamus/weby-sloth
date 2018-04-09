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
use Weby\Sloth\Exception;
use Weby\Sloth\Utils;
use Weby\Sloth\Func\Group\Count as CountAsterix;
use Weby\Sloth\Func\Value\Count;
use Weby\Sloth\Func\Value\Accum;
use Weby\Sloth\Func\Value\First;
use Weby\Sloth\Func\Value\Sum;
use Weby\Sloth\Func\Value\Avg;
use Weby\Sloth\Func\Value\Min;
use Weby\Sloth\Func\Value\Max;
use Weby\Sloth\Func\Value\Median;
use Weby\Sloth\Func\Value\Mode;

/**
 * Group (group by) operation.
 */
class Group extends Base
{
	const ASSOC_ALL = -1;
	
	private $groups = [];
	
	private $assocKeyFieldName = null;
	private $assocValueFieldName = null;
	
	public function __construct(Sloth $sloth, $groupCols, $valueCols)
	{
		parent::__construct($sloth, $groupCols, $valueCols);
	}
	
	/**
	 * Whether to calculate record count in a group.
	 * 
	 * @param string $alias
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function count($alias = null, $options = null)
	{
		if ($alias == '*') {
			$this->context = new CountAsterix($this, $alias, $options);
			$this->groupFuncs[CountAsterix::class] = $this->context;
		} else {
			$this->context = new Count($this, $alias, $options);
			$this->valueFuncs[Count::class] = $this->context;
		}
		
		return $this;
	}
	
	/**
	 * Whether to sum values for each value column in a group.
	 * 
	 * @param string $alias
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function sum($alias = null, $options = null)
	{
		$this->context = new Sum($this, $alias, $options);
		$this->valueFuncs[Sum::class] = $this->context;
		
		return $this;
	}
	
	/**
	 * Whether to caclulate average value for each value column in group.
	 * 
	 * @param string $alias
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function avg($alias = null, $options = null)
	{
		$this->context = new Avg($this, $alias, $options);
		$this->valueFuncs[Avg::class] = $this->context;
		
		return $this;
	}
	
	/**
	 * Whether to accumulate values for each value column in group.
	 * 
	 * @param string $alias
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function accum($alias = null, $options = null)
	{
		$this->context = new Accum($this, $alias, $options);
		$this->valueFuncs[Accum::class] = $this->context;
		
		return $this;
	}
	
	/**
	 * Whether to accumulate only a first value for each value column in group.
	 * 
	 * @param string $alias
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function first($alias = null, $options = null)
	{
		$this->context = new First($this, $alias, $options);
		$this->valueFuncs[First::class] = $this->context;
		
		return $this;
	}
	
	/**
	 * Whether to caclulate min value for each value column in group.
	 * 
	 * @param string $alias
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function min($alias = null, $options = null)
	{
		$this->context = new Min($this, $alias, $options);
		$this->valueFuncs[Min::class] = $this->context;
		
		return $this;
	}
	
	/**
	 * Whether to caclulate max value for each value column in group.
	 * 
	 * @param string $alias
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function max($alias = null, $options = null)
	{
		$this->context = new Max($this, $alias, $options);
		$this->valueFuncs[Max::class] = $this->context;
		
		return $this;
	}
	
	/**
	 * Whether to caclulate median value for each value column in group.
	 * 
	 * @param string $alias
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function median($alias = null, $options = null)
	{
		$this->context = new Median($this, $alias, $options);
		$this->valueFuncs[Median::class] = $this->context;
		
		return $this;
	}
	
	/**
	 * Whether to caclulate mode value for each value column in group.
	 * 
	 * @param string $alias
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function mode($alias = null, $options = null)
	{
		$this->context = new Mode($this, $alias, $options);
		$this->valueFuncs[Mode::class] = $this->context;
		
		return $this;
	}
	
	/**
	 * Whether to convert the output into the assoc array.
	 * Keys of the assoc array will be values of $keyColumn.
	 * Values of the assoc array will be values of $valueColumn.
	 * - If $key is omitted then first group column will be used.
	 * - If $value is omitted then first non-group column will be used.
	 * - If $value is specified as '*' then value will be entire output row.
	 * 
	 * @param mixed $keyColumn
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function asAssoc($keyColumn = null, $valueColumn = null)
	{
		$this->context = null;
		$this->outputFormat = self::OUTPUT_ASSOC;
		
		if ($keyColumn) {
			if ($keyColumn instanceof \Closure) {
				$this->assocKeyFieldName = $keyColumn;
			} else {
				$this->assocKeyFieldName = (string) $keyColumn;
			}
		} else {
			// Use alias of a first group column.
			$this->assocKeyFieldName = $this->groupCols[0]->alias;
		}
		
		if ($valueColumn) {
			if ($valueColumn  instanceof \Closure) {
				$this->assocValueFieldName = $valueColumn;
			} elseif ($valueColumn == '*') {
				$this->assocValueFieldName = self::ASSOC_ALL;
			} else {
				$this->assocValueFieldName = (string) $valueColumn;
			}
		} else {
			$this->assocValueFieldName = ($this->valueCols
				// Use alias of a first value column.
				? $this->valueCols[0]->alias
				: ($this->groupFuncs
					// Use filed name of a first group function.
					? array_values($this->groupFuncs)[0]->alias
					// Use all value columns.
					: self::ASSOC_ALL
				)
			);
		}
		
		return $this;
	}
	
	private function hasCountAsterixFunc()
	{
		return isset($this->getGroupFuncs()[CountAsterix::class]);
	}
	
	protected function validatePerform()
	{
		if (
			   !$this->valueCols
			&& count($this->valueFuncs)
		) {
			throw new \Weby\Sloth\Exception('No value columns to apply a function.');
		}
		
		if (
			   $this->valueCols
			&& !count($this->valueFuncs)
		) {
			throw new \Weby\Sloth\Exception('No functions to apply to value columns.');
		}
	}
	
	protected function beginPerform()
	{
		$this->resetOutput();
		$this->resetStore();
		$this->resetOutputCols();
		$this->resetGroups();
	}
	
	protected function doPerform()
	{
		foreach ($this->sloth->data as $row) {
			$key = $this->getGroupKey($row);
			if (!$this->isGroup($key)) {
				$group = &$this->addGroup($key, $row);
				$this->buildOutput($group);
			} else {
				$group = &$this->updateGroup($key, $row);
			}
		}
	}
	
	private function buildOutput(&$row)
	{
		switch ($this->outputFormat) {
			case self::OUTPUT_ARRAY:
				$this->output[] = &$row;
				
				break;
				
			case self::OUTPUT_ASSOC:
				if ($this->assocKeyFieldName instanceof \Closure) {
					$assocKey = call_user_func(
						$this->assocKeyFieldName, $row
					);
				} else {
					$assocKey = $row[$this->assocKeyFieldName];
				}
				
				if (array_key_exists($assocKey, $this->output)) {
					throw new Exception(
						sprintf('Duplicate key "%s" for assoc output.', $assocKey)
					);
				}
				
				$assocValue = null;
				if ($this->assocValueFieldName instanceof \Closure) {
					$assocValue = call_user_func(
						$this->assocValueFieldName, $assocKey, $row
					);
				} elseif ($this->assocValueFieldName == self::ASSOC_ALL) {
					$assocValue = &$row;
				} else {
					$assocValue = &$row[$this->assocValueFieldName];
				}
				$this->output[$assocKey] = &$assocValue;
				
				break;
		}
	}
	
	private function getGroupKey($row)
	{
		$result = '';
		
		if (is_object($row)) {
			$row = Utils::toArray($row);
		}
		
		foreach ($this->groupCols as $groupCol) {
			$result .= $row[$groupCol->name];
		}
		
		$result = md5($result);
		
		return $result;
	}
	
	private function &addGroup($key, $row)
	{
		$group = [];
		
		if (is_object($row)) {
			$row = Utils::toArray($row);
		}
		
		$this->addGroup_processGroupCols($group, $row);
		$this->addGroup_processGroupFuncs($group, $row);
		$this->addGroup_processValueFuncs($group, $row);
		
		$this->groups[$key] = &$group;
		
		return $group;
	}
	
	private function addGroup_processGroupCols(&$group, &$row)
	{
		foreach ($this->groupCols as $groupCol) {
			$group[$groupCol->alias] = $row[$groupCol->name];
			
			if (!$this->groups) {
				$this->outputCols[] = $groupCol->alias;
			}
		}
	}
	
	private function addGroup_processGroupFuncs(&$group, &$row)
	{
		foreach ($this->groupFuncs as $groupFunc) {
			$colName = $this->buildGroupFuncColumnName($groupFunc);
			
			if (!$this->groups) {
				$this->outputCols[] = $colName;
			}
			
			$group[$colName] = null;
			$currValue = &$group[$colName];
			$nextValue = null;
			
			$groupFunc->onAddGroup(
				$group, $colName, $row, null, $currValue, $nextValue
			);
		}
	}
	
	private function addGroup_processValueFuncs(&$group, &$row)
	{
		foreach ($this->valueCols as $valueCol) {
			foreach ($this->valueFuncs as $valueFunc) {
				$colName = $this->buildValueFuncColumnName($valueCol, $valueFunc);
				
				if (!$this->groups) {
					$this->outputCols[] = $colName;
					$this->outputValueCols[] = $colName;
				}
				
				$group[$colName] = null;
				$currValue = &$group[$colName];
				$nextValue = &$row[$valueCol->name];
				
				$valueFunc->onAddGroup(
					$group, $colName, $row, $valueCol->name, $currValue, $nextValue
				);
				
				if (!$this->isFlatOutput) {
					$parts = explode(Sloth::ARRAY_OUTPUT_COLUMN_SEPARATOR, $colName);
					if (count($parts) == 2) {
						$group[$parts[0]][$parts[1]] = &$group[$colName];
						unset($group[$colName]);
					}
				}
			}
		}
	}
	
	private function &updateGroup($key, $row)
	{
		$group = &$this->groups[$key];
		
		if (is_object($row)) {
			$row = Utils::toArray($row);
		}
		
		$this->updateGroup_processGroupFuncs($group, $row);
		$this->updateGroup_processValueFuncs($group, $row);
		
		return $group;
	}
	
	private function updateGroup_processGroupFuncs(&$group, &$row)
	{
		foreach ($this->groupFuncs as $groupFunc) {
			$colName = $this->buildGroupFuncColumnName($groupFunc);
			
			$currValue = &$group[$colName];
			$nextValue = null;
			
			$groupFunc->onUpdateGroup(
				$group, $colName, $row, null, $currValue, $nextValue
			);
		}
	}
	
	private function updateGroup_processValueFuncs(&$group, &$row)
	{
		foreach ($this->valueCols as $valueCol) {
			foreach ($this->valueFuncs as $valueFunc) {
				$colName = $this->buildValueFuncColumnName($valueCol, $valueFunc);
				
				if (!$this->isFlatOutput) {
					$parts = explode(Sloth::ARRAY_OUTPUT_COLUMN_SEPARATOR, $colName);
					if (count($parts) == 2) {
						$group[$colName] = &$group[$parts[0]][$parts[1]];
					}
				}
				
				$currValue = &$group[$colName];
				$nextValue = &$row[$valueCol->name];
				
				$valueFunc->onUpdateGroup(
					$group, $colName, $row, $valueCol->name, $currValue, $nextValue
				);
				
				if (!$this->isFlatOutput) {
					$parts = explode(Sloth::ARRAY_OUTPUT_COLUMN_SEPARATOR, $colName);
					if (count($parts) == 2) {
						$group[$parts[0]][$parts[1]] = &$group[$colName];
						unset($group[$colName]);
					}
				}
			}
		}
	}
	
	protected function endPerform()
	{
		// Do nothing.
	}
	
	private function resetOutput()
	{
		$this->output = [];
	}
	
	private function resetStore() {
		$this->store = [];
	}
	
	private function resetOutputCols()
	{
		$this->outputCols = [];
	}
	
	private function resetGroups()
	{
		$this->groups = [];
	}
	
	private function isGroup($key)
	{
		return isset($this->groups[$key]);
	}
}