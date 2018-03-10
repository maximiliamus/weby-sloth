<?php
namespace Weby\Sloth\Operation;

use Weby\Sloth\Sloth;
use Weby\Sloth\Exception;
use Weby\Sloth\Utils;
use Weby\Sloth\Func\Group\Count;
use Weby\Sloth\Func\Value\Accum;
use Weby\Sloth\Func\Value\First;
use Weby\Sloth\Func\Value\Sum;
use Weby\Sloth\Func\Value\Avg;
use Weby\Sloth\Func\Value\Min;
use Weby\Sloth\Func\Value\Max;
use Weby\Sloth\Func\Value\Median;
use Weby\Sloth\Func\Value\Mode;

class Group extends Base
{
	private $funcs = array();
	protected $groups = array();
	
	private $assocKeyFieldName = null;
	private $assocValueFieldName = null;
	
	public function __construct(Sloth $sloth, $groupCols, $valueCols)
	{
		parent::__construct($sloth, $groupCols, $valueCols);
	}
	
	/**
	 * Whether to calculate record count in group.
	 * Column with name 'count' will be added to the result.
	 * This column name may be overriden if $fieldName is specified.
	 * 
	 * @param string $countFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function count($countFieldName = null, $options = null)
	{
		$this->funcs[Count::class] = new Count($this, $countFieldName, $options);
		
		return $this;
	}
	
	/**
	 * Whether to sum values for each value column in group.
	 * Column with name "${Value Column Name/Alias}_sum" will be added to the result.
	 * This column name may be overriden if $fieldName is specified.
	 * 
	 * @param string $sumFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function sum($sumFieldName = null, $options = null)
	{
		$this->funcs[Sum::class] = new Sum($this, $sumFieldName, $options);
		
		return $this;
	}
	
	/**
	 * Whether to caclulate average value for each value column in group.
	 * Column with name "${Value Column Name/Alias}_avg" will be added to the result.
	 * This column name may be overriden if $fieldName is specified.
	 * 
	 * @param string $sumFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function avg($sumFieldName = null, $options = null)
	{
		$this->funcs[Avg::class] = new Avg($this, $sumFieldName, $options);
		
		return $this;
	}
	
	/**
	 * Whether to accumulate values for each value column in group.
	 * Column with name "${Value Column Name/Alias}_accum" will be added to the result.
	 * This column name may be overriden if $fieldName is specified.
	 * 
	 * @param string $accumFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function accum($accumFieldName = null, $options = null)
	{
		$this->funcs[Accum::class] = new Accum($this, $accumFieldName, $options);
		
		return $this;
	}
	
	/**
	 * Whether to accumulate only a first value for each value column in group.
	 * Column with name "${Value Column Name/Alias}_first" will be added to the result.
	 * This column name may be overwriten if $fieldName is specified.
	 * 
	 * @param string $accumFirstFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function first($accumFirstFieldName = null, $options = null)
	{
		$this->funcs[First::class] = new First($this, $accumFirstFieldName, $options);
		
		return $this;
	}
	
	/**
	 * Whether to caclulate min value for each value column in group.
	 * Column with name "${Value Column Name/Alias}_min" will be added to the result.
	 * This column name may be overriden if $fieldName is specified.
	 * 
	 * @param string $sumFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function min($sumFieldName = null, $options = null)
	{
		$this->funcs[Min::class] = new Min($this, $sumFieldName, $options);
		
		return $this;
	}
	
	/**
	 * Whether to caclulate max value for each value column in group.
	 * Column with name "${Value Column Name/Alias}_max" will be added to the result.
	 * This column name may be overriden if $fieldName is specified.
	 * 
	 * @param string $sumFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function max($sumFieldName = null, $options = null)
	{
		$this->funcs[Max::class] = new Max($this, $sumFieldName, $options);
		
		return $this;
	}
	
	/**
	 * Whether to caclulate median value for each value column in group.
	 * Column with name "${Value Column Name/Alias}_median" will be added to the result.
	 * This column name may be overriden if $fieldName is specified.
	 * 
	 * @param string $sumFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function median($sumFieldName = null, $options = null)
	{
		$this->funcs[Median::class] = new Median($this, $sumFieldName, $options);
		
		return $this;
	}
	
	/**
	 * Whether to caclulate mode value for each value column in group.
	 * Column with name "${Value Column Name/Alias}_mode" will be added to the result.
	 * This column name may be overriden if $fieldName is specified.
	 * 
	 * @param string $sumFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function mode($sumFieldName = null, $options = null)
	{
		$this->funcs[Mode::class] = new Mode($this, $sumFieldName, $options);
		
		return $this;
	}
	
	/**
	 * Returns list of functions.
	 * 
	 * @return array[\Weby\Sloth\Func\Base]
	 */
	public function getFuncs()
	{
		return $this->funcs;
	}
	
	/**
	 * Returns list of group columns.
	 * 
	 * @return array
	 */
	public function getGroupCols()
	{
		return $this->groupCols;
	}
	
	/**
	 * Returns list of column aliases.
	 * 
	 * @return array
	 */
	public function getGroupColsAliases()
	{
		return $this->groupColsAliases;
	}
	
	/**
	 * Whether to convert the output into the assoc array.
	 * Keys of the assoc array will be values of $keyColumn.
	 * Values of the assoc array will be values of $valueColumn.
	 * If $key is omitted then first group column will be used.
	 * If $value is omitted then first non-group (value or func) column will be used.
	 * If $value is specified as '*' then value will be entire output row.
	 * 
	 * @param mixed $keyColumn
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function asAssoc($keyColumn = null, $valueColumn = null)
	{
		$this->outputFormat = self::OUTPUT_ASSOC;
		
		if ($keyColumn) {
			if ($keyColumn instanceof \Closure) {
				$this->assocKeyFieldName = $keyColumn;
			} else {
				$this->assocKeyFieldName = (string) $keyColumn;
			}
		} else {
			$this->assocKeyFieldName = $this->groupColsAliases[$this->groupCols[0]];
		}
		
		if ($valueColumn) {
			if ($valueColumn  instanceof \Closure) {
				$this->assocValueFieldName = $valueColumn;
			} else {
				$this->assocValueFieldName = (string) $valueColumn;
			}
		} else {
			$this->assocValueFieldName = ($this->valueCols
				? $this->valueColsAliases[$this->valueCols[0]]
				: null
			);
		}
		
		return $this;
	}
	
	protected function perform()
	{
		$this->validatePerform();
		$this->beginPerform();
		$this->doPerform();
		$this->endPerform();
	}
	
	private function validatePerform()
	{
		$funcs = $this->getFuncs();
		if (!$this->valueCols && count($funcs) && !array_key_exists(Count::class, $funcs)) {
			throw new \Weby\Sloth\Exception('No value columns to apply a function.');
		}
	}
	
	private function beginPerform()
	{
		$this->output = array();
		$this->store = array();
		$this->resetGroups();
	}
	
	private function doPerform()
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
				if ($this->assocValueFieldName) {
					if ($this->assocValueFieldName instanceof \Closure) {
						print_r($this->assocValueFieldName);
						$assocValue = call_user_func(
							$this->assocValueFieldName, $assocKey, $row
						);
					} elseif ($this->assocValueFieldName == '*') {
						$assocValue = &$row;
					} else {
						$assocValue = &$row[$this->assocValueFieldName];
					}
				} else {
					if ($this->searchAssocValueFieldName($row)) {
						$assocValue = &$row[$this->assocValueFieldName];
					}
				}
				$this->output[$assocKey] = &$assocValue;
				
				break;
		}
	}
	
	/**
	 * Searches first non-group column to use it as
	 * value column for the output assoc array.
	 * 
	 * @param array $row Output row
	 * @return boolean
	 */
	private function searchAssocValueFieldName(&$row)
	{
		$availableCols = array_keys($row);
		$groupColsAliases = array_flip($this->groupColsAliases);
		
		$result = false;
		foreach ($availableCols as $col) {
			if (!isset($groupColsAliases[$col])) {
				$this->assocValueFieldName = $col;
				$result = true;
				break;
			};
		}
		
		return $result;
	}
	
	private function getGroupKey($row)
	{
		$result = '';
		
		if (is_object($row)) {
			$row = Utils::toArray($row);
		}
		
		foreach ($this->groupCols as $groupCol) {
			$result .= $row[$groupCol];
		}
		
		$result = md5($result);
		
		return $result;
	}
	
	private function &addGroup($key, $row)
	{
		$result = array();
		
		if (is_object($row)) {
			$row = Utils::toArray($row);
		}
		
		foreach ($this->groupCols as $group) {
			$result[$this->groupColsAliases[$group]] = $row[$group];
		}
		
		foreach ($this->funcs as $func) {
			if ($func instanceof \Weby\Sloth\Func\Group\Base) {
				$fieldName = $func->funcFieldName;
				$result[$fieldName] = null;
				$currValue = &$result[$fieldName];
				$nextValue = null;
				$func->onAddGroup(
					$result, $fieldName, $row, null, $currValue, $nextValue
				);
			} else {
				foreach ($this->valueCols as $valueCol) {
					$fieldName = (
						  $func->funcFieldPostfix
						? $this->valueColsAliases[$valueCol] . '_' . $func->funcFieldPostfix
						: $this->valueColsAliases[$valueCol]
					);
					$result[$fieldName] = null;
					$currValue = &$result[$fieldName];
					$nextValue = &$row[$valueCol];
					$func->onAddGroup(
						$result, $fieldName, $row, $valueCol, $currValue, $nextValue
					);
				}
			}
		}
		
		$this->groups[$key] = &$result;
		
		return $result;
	}
	
	private function &updateGroup($key, $row)
	{
		$result = &$this->groups[$key];
		
		if (is_object($row)) {
			$row = Utils::toArray($row);
		}
		
		foreach ($this->funcs as $func) {
			if ($func instanceof \Weby\Sloth\Func\Group\Base) {
				$fieldName = $func->funcFieldName;
				$currValue = &$result[$fieldName];
				$nextValue = null;
				$func->onUpdateGroup(
					$result, $fieldName, $row, null, $currValue, $nextValue
				);
			} else {
				foreach ($this->valueCols as $valueCol) {
					$fieldName = (
						  $func->funcFieldPostfix
						? $this->valueColsAliases[$valueCol] . '_' . $func->funcFieldPostfix
						: $this->valueColsAliases[$valueCol]
					);
					$currValue = &$result[$fieldName];
					$nextValue = &$row[$valueCol];
					$func->onUpdateGroup(
						$result, $fieldName, $row, $valueCol, $currValue, $nextValue
					);
				}
			}
		}
		
		return $result;
	}
	
	private function endPerform()
	{
		// Do nothing.
	}
	
	private function resetGroups()
	{
		$this->groups = array();
	}
	
	private function isGroup($key)
	{
		return isset($this->groups[$key]);
	}
}