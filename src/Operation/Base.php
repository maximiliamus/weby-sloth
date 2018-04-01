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
	
	protected $groupCols = array();
	protected $valueCols = array();
	
	protected $output = array();
	protected $outputFormat = self::OUTPUT_ARRAY;
	protected $outputCols = array();
	
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
	 */
	abstract protected function perform();
	
	/**
	 * Returns list of group columns that were specified for operation.
	 * 
	 * @return array
	 */
	public function getGroupCols()
	{
		return $this->groupCols;
	}
	
	/**
	 * Returns list of value columns that were specified for operation.
	 * 
	 * @return array
	 */
	public function getValueCols()
	{
		return $this->valueCols;
	}
	
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
	 * @param boolean $onlyData Weather to print only data without column names.
	 * @return \Weby\Sloth\Operation\Base
	 */
	public function print($onlyData = false)
	{
		if (!$this->output)
			$this->perform();
		
		if (!$onlyData) {
			foreach ($this->outputCols as $col) {
				echo $col, "\t";
			}
			echo "\n";
		}
		
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