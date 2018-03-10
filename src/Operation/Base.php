<?php
namespace Weby\Sloth\Operation;

use Weby\Sloth\Sloth;

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
	
	protected $groupCols = array();
	protected $groupColsAliases = array();
	
	protected $valueCols = array();
	protected $valueColsAliases = array();
	
	protected $output = array();
	protected $outputFormat = self::OUTPUT_ARRAY;
	
	protected $store = array();
	protected $scale = 2;
	
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
		$this->sloth = $sloth;
		
		$this->assignGroupCols($groupCols);
		if (empty($this->groupCols))
			throw new \Weby\Sloth\Exception('No group columns.');
		
		$this->assignValueCols($valueCols);
	}
	
	protected function assignGroupCols($groupCols)
	{
		foreach ((array) $groupCols as $colName => $colDef) {
			if ($this->sloth->isArray) {
				if (!is_string($colDef)) {
					// If this is not aliased column.
					$colName = $colDef;
				}
			} else {
				if (is_numeric($colName)) {
					$colName = $colDef;
				}
			}
			
			if (!$this->sloth->isColExists($colName)) {
				throw new \Weby\Sloth\Exception(
					sprintf('Unknown group column "%s".', $colName)
				);
			}
			
			$this->groupCols[] = $colName;
			$this->groupColsAliases[$colName] = $colDef;
		}
	}
	
	protected function assignValueCols($valueCols)
	{
		foreach ((array) $valueCols as $colName => $colDef) {
			if ($this->sloth->isArray) {
				if (!is_string($colDef)) {
					// If this is not aliased column.
					$colName = $colDef;
				}
			} else {
				if (is_numeric($colName)) {
					$colName = $colDef;
				}
			}
			
			if (!$this->sloth->isColExists($colName)) {
				throw new \Weby\Sloth\Exception(
					sprintf('Unknown value column "%s".', $colDef)
				);
			}
			
			$this->valueCols[] = $colName;
			$this->valueColsAliases[$colName] = $colDef;
		}
	}
	
	abstract protected function perform();
	
	/**
	 * Performs a data manipulation and returns a result.
	 * 
	 * @return array
	 */
	public function select()
	{
		if (!$this->output)
			$this->perform();
		
		return $this->output;
	}
	
	/**
	 * Prints operation's results on screen.
	 * 
	 * @return \Weby\Sloth\Operation\Base
	 */
	public function print()
	{
		if (!$this->output)
			$this->perform();
		
		foreach ($this->output as $row) {
			foreach ($row as $col) {
				echo $this->renderCol($col), "\t";
			}
			echo "\n";
		}
		
		return $this;
	}
	
	private function renderCol($col)
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
	
	/**
	 * Sets scale for BC Math operations on double values.
	 * 
	 * @param integer $scale
	 * @return \Weby\Sloth\Operation\Base
	 */
	public function setScale($scale)
	{
		$this->scale = $scale;
		
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
}