<?php
namespace Weby\Sloth\Operation;

use Weby\Sloth\Sloth;
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
	
	public function __construct(Sloth $sloth, $groupCols, $valueCols = null)
	{
		parent::__construct($sloth, $groupCols, $valueCols);
	}
	
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
	
	public function getFuncs()
	{
		return $this->funcs;
	}
	
	public function getGroupCols()
	{
		return $this->groupCols;
	}
	
	public function getGroupColsAliases()
	{
		return $this->groupColsAliases;
	}
	
	/**
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function toArray()
	{
		$this->outputFormat = self::OUTPUT_ARRAY;
		
		return $this;
	}
	
	/**
	 * @param mixed $key
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function toAssocArray($key, $value)
	{
		// TODO: Check existanse of $key, $value columns.
		$this->outputFormat = self::OUTPUT_ASSOC;
		if ($key) {
			if (is_callable($key)) {
				$this->assocKeyFieldName = $key;
			} else {
				$this->assocKeyFieldName = (string) $key;
			}
		} else {
			$this->assocKeyFieldName = $this->groupColsAliases[$this->groupCols[0]];
		}
		if ($value) {
			$this->assocValueFieldName = (string) $value;
		} else {
			$this->assocValueFieldName = null;
		}
		
		return $this;
	}
	
	public function select()
	{
		$this->output = array();
		
		$this->resetGroups();
		foreach ($this->data as $row) {
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
	
	private function buildOutput(&$group)
	{
		switch ($this->outputFormat) {
			case self::OUTPUT_ARRAY:
				$this->output[] = &$group;
				
				break;
				
			case self::OUTPUT_ASSOC:
				if ($this->assocKeyFieldName) {
					if (is_callable($this->assocKeyFieldName)) {
						$assocKey = (string) call_user_func($this->assocKeyFieldName, $group);
					} else {
						$assocKey = $group[$this->assocKeyFieldName];
					}
				} else {
					$assocKey = count($this->output);
				}
				
				if ($this->assocValueFieldName) {
					$this->output[$assocKey] = &$group[$this->assocValueFieldName];
				} else {
					$this->output[$assocKey] = &$group;
				}
				
				break;
		}
	}
	
	private function getGroupKey($row)
	{
		$result = '';
		
		if (is_object($row)) {
			$row = $this->convertRowToArray($row);
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
			$row = $this->convertRowToArray($row);
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
			$row = $this->convertRowToArray($row);
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