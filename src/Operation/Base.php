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
use Weby\Sloth\Column;

/**
 * Base class for all operations.
 */
abstract class Base
{
	const OUTPUT_ARRAY  = 1;
	const OUTPUT_ASSOC  = 2;
	
	/**
	 * Reference to Sloth object.
	 * 
	 * @var Sloth
	 */
	protected $sloth = null;
	
	protected $groupCols = [];
	protected $valueCols = [];
	
	protected $groupFuncs = [];
	protected $valueFuncs = [];
	
	protected $output = [];
	protected $outputFormat = self::OUTPUT_ARRAY;
	
	protected $outputCols = [];
	protected $outputValueCols = [];
	
	protected $store = array();
	protected $scale = 2;
	
	protected $isOptimizeColumnNames = true;
	protected $isOneCol = false;
	
	protected $isFlatOutput = false;
	
	protected $context = null;
	protected $colToFuncMap = null;
	
	private $assocKeyFieldName = null;
	private $assocValueFieldName = null;
	
	/**
	 * Constructs operation.
	 * 
	 * @param Sloth $sloth
	 * @param int|string|array $groupCols
	 * @param int|string|array $valueCols
	 * @throws \Weby\Sloth\Exception
	 */
	public function __construct(Sloth $sloth, $groupCols, $valueCols)
	{
		$groupCols  = Utils::normalizeArray($groupCols);
		$valueCols  = Utils::normalizeArray($valueCols);
		
		$this->sloth = $sloth;
		
		$this->assignGroupCols($groupCols);
		if (!$this->groupCols)
			throw new \Weby\Sloth\Exception('No group columns.');
		
		$this->assignValueCols($valueCols);
	}
	
	protected function assignGroupCols($groupCols)
	{
		foreach ($groupCols as $colName => $colDef) {
			if ($this->sloth->isArray) {
				if (!is_string($colDef)) {
					// If not aliased column was specified...
					$colName = $colDef;
				}
			} else {
				if (is_numeric($colName)) {
					$colName = $colDef;
				}
			}
			
			$col = null;
			if ($colName instanceof \Weby\Sloth\Column) {
				// If column object was specified...
				$col = $colName;
			} else {
				$col = Column::new($colName)
					->as($colDef);
			}
			
			if (!$this->sloth->isColExists($col->name)) {
				throw new \Weby\Sloth\Exception(
					sprintf('Unknown group column "%s".', $col->name)
				);
			}
			
			$this->groupCols[] = $col;
		}
	}
	
	protected function assignValueCols($valueCols)
	{
		foreach ($valueCols as $colName => $colDef) {
			if ($this->sloth->isArray) {
				if (!is_string($colDef)) {
					// If not aliased column was specified...
					$colName = $colDef;
				}
			} else {
				if (is_numeric($colName)) {
					$colName = $colDef;
				}
			}
			
			$col = null;
			if ($colName instanceof \Weby\Sloth\Column) {
				// If column object was specified...
				$col = $colName;
			} else {
				$col = Column::new($colName)
					->as($colDef);
			}
			
			if (!$this->sloth->isColExists($col->name)) {
				throw new \Weby\Sloth\Exception(
					sprintf('Unknown value column "%s".', $col->name)
				);
			}
			
			$this->valueCols[] = $col;
		}
	}
	
	/**
	 * Performs operation.
	 * 
	 * @return \Weby\Sloth\Operation\Base
	 */
	public function perform()
	{
		$this->context = null;
		
		$this->isOneCol = count($this->valueCols) == 1;
		
		$this->validatePerform();
		$this->beginPerform();
		$this->doPerform();
		$this->endPerform();
		
		return $this;
	}
	
	abstract protected function validatePerform();
	abstract protected function beginPerform();
	abstract protected function doPerform();
	abstract protected function endPerform();
	
	/**
	 * Returns info about what functions will be applied to each value column.
	 * 
	 * @return array
	 */
	public function getColToFuncMap()
	{
		return $this->colToFuncMap;
	}
	
	/**
	 * Returns list of group columns that were specified for operation.
	 * 
	 * @return array
	 */
	public function getGroupCols()
	{
		$this->context = null;
		
		return $this->groupCols;
	}
	
	/**
	 * Returns list of value columns that were specified for operation.
	 * 
	 * @return array
	 */
	public function getValueCols()
	{
		$this->context = null;
		
		return $this->valueCols;
	}
	
	/**
	 * Returns data that were produced by the operation.
	 * 
	 * @return array
	 */
	public function getOutput()
	{
		$this->context = null;
		
		return $this->output;
	}
	
	/**
	 * Returns list of column names that were produced by the operation.
	 * 
	 * @return array List of column names
	 */
	public function getOutputCols()
	{
		$this->context = null;
		
		return $this->outputCols;
	}
	
	/**
	 * Returns list of value column names that were produced by the operation.
	 * 
	 * @return array List of column names
	 */
	public function getOutputValueCols()
	{
		$this->context = null;
		
		return $this->outputValueCols;
	}
	
	/**
	 * Performs a data manipulation and returns an operation output.
	 * It is a shourtcut action for ->perform()->getOutput() action sequence.
	 * 
	 * @see \Weby\Sloth\Operation\Base::perform()
	 * @see \Weby\Sloth\Operation\Base::getOutput()
	 * 
	 * @return array
	 */
	public function fetch()
	{
		$this->context = null;
		
		if (!$this->output) {
			$this->perform();
		}
		
		return $this->getOutput();
	}
	
	/**
	 * Prints an operation output on screen.
	 * 
	 * @param boolean $onlyData Weather to print only data without column names.
	 * @return \Weby\Sloth\Operation\Base
	 */
	public function printOutput($onlyData = false)
	{
		$this->context = null;
		
		if (!$this->output) {
			return $this;
		}
		
		if ($this->isFlatOutput) {
			$this->printArrayOutput($onlyData);
		} else {
			$this->printAssocOutput($onlyData);
		}
		
		return $this;
	}
	
	private function printArrayOutput($onlyData)
	{
		if (!$onlyData) {
			foreach ($this->outputCols as $col) {
				$parts = explode(Sloth::COLUMN_SEPARATOR, $col);
				$col = implode(Sloth::ARRAY_OUTPUT_COLUMN_SEPARATOR, $parts);
				echo $col, "\t";
			}
			echo "\n";
		}
		
		foreach ($this->output as $row) {
			foreach ($row as $col) {
				echo $this->renderValue($col), "\t";
			}
			echo "\n";
		}
	}
	
	private function printAssocOutput($onlyData)
	{
		if (!$onlyData) {
			foreach ($this->outputCols as $col) {
				$parts = explode(Sloth::COLUMN_SEPARATOR, $col);
				$col = implode(Sloth::ASSOC_OUTPUT_COLUMN_SEPARATOR, $parts);
				echo $col, "\t";
			}
			echo "\n";
		}
		
		foreach ($this->output as $row) {
			foreach ($this->outputCols as $col) {
				$value = null;
				
				$parts = explode(Sloth::COLUMN_SEPARATOR, $col);
				switch (count($parts)) {
					case 1: $value = $row[$parts[0]]; break;
					case 2: $value = $row[$parts[0]][$parts[1]]; break;
					case 3: $value = $row[$parts[0]][$parts[1]][$parts[2]]; break;
				}
				
				echo $this->renderValue($value), "\t";
			}
			echo "\n";
		}
	}
	
	/**
	 * Performs a data manipulation and prints an operation output.
	 * It is a shourtcut action for ->perform()->printOutput() action sequence.
	 * 
	 * @see \Weby\Sloth\Operation\Base::perform()
	 * @see \Weby\Sloth\Operation\Base::printOutput()
	 * 
	 * @return array
	 */
	public function print($onlyData = false)
	{
		$this->context = null;
		
		if (!$this->output) {
			$this->perform();
		}
		
		return $this->printOutput($onlyData);
	}
	
	private function renderValue($col)
	{
		$result = $col;
		
		if (is_array($col)) {
			if (count($col) == 1) {
				$result = sprintf('[%s]', $col[0]);
			} else {
				$result = sprintf('[%s,...]', $col[0]);
			}
		}
		
		return $result;
	}
	
	protected function buildGroupFuncColumnName($groupFunc)
	{
		return $groupFunc->alias;
	}
	
	protected function buildValueFuncColumnName($valueCol, $valueFunc, $isOneFunc)
	{
		$result = null;
		
		$colName = $valueCol->alias;
		$funcName = $valueFunc->alias;
		
		if ($this->isOptimizeColumnNames) {
			$result = (
				  $this->isOneCol && $isOneFunc
				? $colName
				: (
					  $this->isOneCol
					? $funcName
					: (
						  $isOneFunc
						? $colName
						: $colName . Sloth::COLUMN_SEPARATOR . $funcName
					)
				)
			);
		} else {
			$result = $colName . Sloth::COLUMN_SEPARATOR . $funcName;
		}
		
		return $result;
	}
	
	protected function isFunctionContext()
	{
		return $this->context instanceof \Weby\Sloth\Func\Base;
	}
	
	protected function ensureFunctionContext()
	{
		if (!$this->isFunctionContext()) {
			throw new Exception('Command can not be used in the current context.');
		}
	}
	
	/**
	 * Sets alias for function.
	 * 
	 * @param string $alias
	 * @return \Weby\Sloth\Operation\Base
	 */
	public function as($alias)
	{
		$this->ensureFunctionContext();
		
		$func = \Weby\Sloth\Func\Base::cast($this->context);
		$func->alias = $alias;
		
		return $this;
	}
	
	/**
	 * Sets single function's option via fluent "manner".
	 * 
	 * @param string $method
	 * @param array $args
	 * @return \Weby\Sloth\Operation\Base
	 */
	public function __call($method, $args)
	{
		$this->ensureFunctionContext();
		
		$func = \Weby\Sloth\Func\Base::cast($this->context);
		$func->setOption($method, $args[0]);
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return \Weby\Sloth\Operation\Base
	 */
	public function setOption($name, $value)
	{
		$this->ensureFunctionContext();
		
		$func = \Weby\Sloth\Func\Base::cast($this->context);
		$func->setOption($name, $value);
		
		return $this;
	}
	
	public function setOptions($options)
	{
		$this->ensureFunctionContext();
		
		$func = \Weby\Sloth\Func\Base::cast($this->context);
		$func->setOptions($options);
		
		return $this;
	}
	
	/**
	 * Returns list of functions that will be applied to entire group only.
	 * 
	 * @return array
	 */
	public function getGroupFuncs()
	{
		$this->context = null;
		
		return $this->groupFuncs;
	}
	
	/**
	 * Returns list of functions that will be applied to value columns only.
	 * 
	 * @return array
	 */
	public function getValueFuncs()
	{
		$this->context = null;
		
		return $this->valueFuncs;
	}
	
	/**
	 * Returns list of all functions that were specified for the operation.
	 * 
	 * @return array
	 */
	public function getFuncs()
	{
		$this->context = null;
		
		return array_merge($this->groupFuncs, $this->valueFuncs);
	}
	
	/**
	 * Sets scale for BC Math operations on double values.
	 * 
	 * @param integer $scale
	 * @return \Weby\Sloth\Operation\Base
	 */
	public function setScale(int $scale)
	{
		$this->scale = $scale;
		
		return $this;
	}
	
	/**
	 * Whether to optimize column names (to make them simpler by dropping repeated parts)
	 * when only one value column or function were specified to perform an operation.
	 * 
	 * @param bool $value
	 * @return \Weby\Sloth\Operation\Base
	 */
	public function setOptimizeColumnNames(bool $value)
	{
		$this->isOptimizeColumnNames = $value;
		
		return $this;
	}
	
	/**
	 * Whether to produce flat output.
	 * 
	 * Flat output - the output is an array of indexed (non-assoc) arrays.
	 * Non-flat output - the output is an array of assoc arrays.
	 * By default the output is non-flat.
	 * 
	 * @param boolean $value
	 * @return \Weby\Sloth\Operation\Base
	 */
	public function setFlattenOutput(bool $value)
	{
		$this->isFlatOutput = $value;
		
		return $this;
	}
	
	/**
	 * Returns scale for BC Math operations on double values.
	 * 
	 * @return integer
	 */
	public function getScale()
	{
		return $this->scale;
	}
	
	/**
	 * Returns reference to auxiliary store that can be used by 
	 * the operation functions for their purposes (for example,
	 * accumulate some values).
	 * 
	 * @return array
	 */
	public function &getStore()
	{
		return $this->store;
	}
	
	/**
	 * Shortcut for $this->setFlattenOutput(true).
	 */
	public function flattenOutput()
	{
		return $this->setFlattenOutput(true);
	}
	
	/**
	 * Shortcut for $this->setFlattenOutput(false).
	 */
	public function dontFlattenOutput()
	{
		return $this->setFlattenOutput(false);
	}
	
	/**
	 * Shorctcut for $this->setOptimizeColumnNames(true);
	 */
	public function optimizeColumnNames()
	{
		return $this->setOptimizeColumnNames(true);
	}
	
	/**
	 * Shorctcut for $this->setOptimizeColumnNames(false);
	 */
	public function dontOptimizeColumnNames()
	{
		return $this->setOptimizeColumnNames(false);
	}
}