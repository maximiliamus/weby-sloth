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
	
	private $assocKeyFieldName = null;
	private $assocValueFieldName = null;
	
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
}
