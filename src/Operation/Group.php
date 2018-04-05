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
	private $funcs = [];
	protected $groups = [];
	
	private $assocKeyFieldName = null;
	private $assocValueFieldName = null;
	
	public function __construct(Sloth $sloth, $groupCols, $valueCols)
	{
		parent::__construct($sloth, $groupCols, $valueCols);
	}
	
	/**
	 * Whether to calculate record count in a group.
	 * Column with name "${Value Column Name/Alias}_count" will be added to the result.
	 * The column suffix "sum" may be overriden if $fieldSuffix is specified.
	 * 
	 * @param string $fieldSuffix
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function count($fieldSuffix = null, $options = null)
	{
		$this->funcs[Count::class] = new Count($this, $fieldName, $options);
		
		return $this;
	}
	
	/**
	 * Whether to sum values for each value column in a group.
	 * Column with name "${Value Column Name/Alias}_sum" will be added to the result.
	 * The column suffix "sum" may be overriden if $fieldSuffix is specified.
	 * 
	 * @param string $fieldSuffix
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function sum($fieldSuffix = null, $options = null)
	{
		$this->funcs[Sum::class] = new Sum($this, $fieldSuffix, $options);
		
		return $this;
	}
	
	/**
	 * Whether to caclulate average value for each value column in group.
	 * Column with name "${Value Column Name/Alias}_avg" will be added to the result.
	 * The column suffix "avg" may be overriden if $fieldSuffix is specified.
	 * 
	 * @param string $fieldSuffix
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function avg($fieldSuffix = null, $options = null)
	{
		$this->funcs[Avg::class] = new Avg($this, $fieldSuffix, $options);
		
		return $this;
	}
	
	/**
	 * Whether to accumulate values for each value column in group.
	 * Column with name "${Value Column Name/Alias}_accum" will be added to the result.
	 * The column suffix "accum" may be overriden if $fieldSuffix is specified.
	 * 
	 * @param string $fieldSuffix
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function accum($fieldSuffix = null, $options = null)
	{
		$this->funcs[Accum::class] = new Accum($this, $fieldSuffix, $options);
		
		return $this;
	}
	
	/**
	 * Whether to accumulate only a first value for each value column in group.
	 * Column with name "${Value Column Name/Alias}_first" will be added to the result.
	 * The column suffix "first" may be overwriten if $fieldSuffix is specified.
	 * 
	 * @param string $fieldSuffix
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function first($fieldSuffix = null, $options = null)
	{
		$this->funcs[First::class] = new First($this, $fieldSuffix, $options);
		
		return $this;
	}
	
	/**
	 * Whether to caclulate min value for each value column in group.
	 * Column with name "${Value Column Name/Alias}_min" will be added to the result.
	 * The column suffix "min" may be overriden if $fieldSuffix is specified.
	 * 
	 * @param string $fieldSuffix
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function min($fieldSuffix = null, $options = null)
	{
		$this->funcs[Min::class] = new Min($this, $fieldSuffix, $options);
		
		return $this;
	}
	
	/**
	 * Whether to caclulate max value for each value column in group.
	 * Column with name "${Value Column Name/Alias}_max" will be added to the result.
	 * The column suffix "max" may be overriden if $fieldSuffix is specified.
	 * 
	 * @param string $fieldSuffix
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function max($fieldSuffix = null, $options = null)
	{
		$this->funcs[Max::class] = new Max($this, $fieldSuffix, $options);
		
		return $this;
	}
	
	/**
	 * Whether to caclulate median value for each value column in group.
	 * Column with name "${Value Column Name/Alias}_median" will be added to the result.
	 * The column suffix "median" may be overriden if $fieldSuffix is specified.
	 * 
	 * @param string $fieldSuffix
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function median($fieldSuffix = null, $options = null)
	{
		$this->funcs[Median::class] = new Median($this, $fieldSuffix, $options);
		
		return $this;
	}
	
	/**
	 * Whether to caclulate mode value for each value column in group.
	 * Column with name "${Value Column Name/Alias}_mode" will be added to the result.
	 * The column suffix "mode" may be overriden if $fieldSuffix is specified.
	 * 
	 * @param string $fieldSuffix
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function mode($fieldSuffix = null, $options = null)
	{
		$this->funcs[Mode::class] = new Mode($this, $fieldSuffix, $options);
		
		return $this;
	}
	
	/**
	 * Returns list of functions that were specified for operation.
	 * 
	 * @return array
	 */
	public function getFuncs()
	{
		return $this->funcs;
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
			} else {
				$this->assocValueFieldName = (string) $valueColumn;
			}
		} else {
			$this->assocValueFieldName = ($this->valueCols
				// Use alias of a first value column.
				? $this->valueCols[0]->alias
				: (isset($this->funcs[Count::class])
					// Use filed name of a single group function.
					? $this->funcs[Count::class]->getFieldName()
					: '*'
				)
			);
		}
		
		return $this;
	}
	
	protected function validatePerform()
	{
		$funcs = $this->getFuncs();
		if (!$this->valueCols && count($funcs) && !array_key_exists(Count::class, $funcs)) {
			throw new \Weby\Sloth\Exception('No value columns to apply a function.');
		}
	}
	
	protected function beginPerform()
	{
		$this->isOneFunc = count($this->funcs) == 1;
		$this->isOneCol = count($this->valueCols) == 1;
		
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
				} elseif ($this->assocValueFieldName == '*') {
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
		
		foreach ($this->groupCols as $groupCol) {
			$group[$groupCol->alias] = $row[$groupCol->name];
			
			if (!$this->groups) {
				$this->outputCols[] = $groupCol->alias;
			}
		}
		
		foreach ($this->valueCols as $valueCol) {
			foreach ($this->funcs as $func) {
				$colName = $this->buildColumnName($valueCol, $func);
				
				if (!$this->groups) {
					$this->outputCols[] = $colName;
					$this->outputValueCols[] = $colName;
				}
				
				$group[$colName] = null;
				$currValue = &$group[$colName];
				$nextValue = &$row[$valueCol->name];
				
				$func->onAddGroup(
					$group, $colName, $row, $valueCol->name, $currValue, $nextValue
				);
				
				if (!$this->isFlatOutput) {
					$parts = explode(Sloth::FLAT_FIELD_SEPARATOR, $colName);
					if (count($parts) == 2) {
						$group[$parts[0]][$parts[1]] = &$group[$colName];
						unset($group[$colName]);
					}
				}
			}
		}
		
		$this->groups[$key] = &$group;
		
		return $group;
	}
	
	private function &updateGroup($key, $row)
	{
		$group = &$this->groups[$key];
		
		if (is_object($row)) {
			$row = Utils::toArray($row);
		}
		
		foreach ($this->valueCols as $valueCol) {
			foreach ($this->funcs as $func) {
				$colName = $this->buildColumnName($valueCol, $func);
				
				if (!$this->isFlatOutput) {
					$parts = explode(Sloth::FLAT_FIELD_SEPARATOR, $colName);
					if (count($parts) == 2) {
						$group[$colName] = &$group[$parts[0]][$parts[1]];
					}
				}
				
				$currValue = &$group[$colName];
				$nextValue = &$row[$valueCol->name];
				
				$func->onUpdateGroup(
					$group, $colName, $row, $valueCol->name, $currValue, $nextValue
				);
				
				if (!$this->isFlatOutput) {
					$parts = explode(Sloth::FLAT_FIELD_SEPARATOR, $colName);
					if (count($parts) == 2) {
						$group[$parts[0]][$parts[1]] = &$group[$colName];
						unset($group[$colName]);
					}
				}
			}
		}
		
		return $group;
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