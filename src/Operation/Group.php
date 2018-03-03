<?php
namespace Weby\Sloth\Operation;

use Weby\Sloth\Exception;
use Weby\Sloth\Utils;
use Weby\Sloth\Func\Group\Count;
use Weby\Sloth\Func\Value\Accum;
use Weby\Sloth\Func\Value\First;
use Weby\Sloth\Func\Value\Sum;

class Group extends Base
{
	private $funcs = array();
	protected $groups = array();
	
	private $assocKeyFieldName = null;
	private $assocValueFieldName = null;
	
	/**
	 * Whether to count records in groups.
	 * Column with name 'count' will be added to Sloth's result.
	 * This column name can be overriden if $fieldName is specified.
	 * 
	 * @param string $countFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function count($countFieldName = null, $options = null)
	{
		$this->funcs[] = new Count($countFieldName, $options);
		
		return $this;
	}
	
	/**
	 * Whether to sum values of value cols in groups.
	 * Column with name "${Value Column Name/Alias}_sum" will be added to Sloth's result.
	 * This column name can be overriden if $fieldName is specified.
	 * 
	 * @param string $sumFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function sum($sumFieldName = null, $options = null)
	{
		if (!$this->valueCols) {
			throw new \Weby\Sloth\Exception('No value columns to apply a function.');
		}
		
		$this->funcs[] = new Sum($sumFieldName, $options);
		
		return $this;
	}
	
	/**
	 * Whether to accumulate values of value cols in groups.
	 * Column with name "${Value Column Name/Alias}_accum" will be added to Sloth's result.
	 * This column name can be overriden if $fieldName is specified.
	 * 
	 * @param string $accumFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function accum($accumFieldName = null, $options = null)
	{
		if (!$this->valueCols) {
			throw new \Weby\Sloth\Exception('No value columns to apply a function.');
		}
		
		$this->funcs[] = new Accum($accumFieldName, $options);
		
		return $this;
	}
	
	/**
	 * Whether to accumulate only a first value of value cols in group.
	 * Column with name "${Value Column Name/Alias}_first" will be added to Sloth's result.
	 * This column name can be overwriten if $fieldName is specified.
	 * 
	 * @param string $accumFirstFieldName
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function first($accumFirstFieldName = null, $options = null)
	{
		if (!$this->valueCols) {
			throw new \Weby\Sloth\Exception('No value columns to apply a function.');
		}
		
		$this->funcs[] = new First($accumFirstFieldName, $options);
		
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
	
	/**
	 * Performs a data manipulation and returns a result.
	 * 
	 * @return array
	 */
	public function select()
	{
		$this->output = array();
		
		$this->resetGroups();
		foreach ($this->sloth->data as $row) {
			$key = $this->getGroupKey($row);
			if (!$this->isGroup($key)) {
				$group = &$this->addGroup($key, $row);
				$this->buildOutput($group);
			} else {
				$group = &$this->updateGroup($key, $row);
			}
		}
		
		return $this->output;
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
	
	private function resetGroups()
	{
		$this->groups = array();
	}
	
	private function isGroup($key)
	{
		return isset($this->groups[$key]);
	}
}