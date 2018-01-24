<?php
namespace Weby\Sloth\Operation;

use Weby\Sloth\Sloth;

abstract class Base
{
	const OUTPUT_ARRAY  = 1;
	const OUTPUT_ASSOC  = 2;
	
	protected $sloth = null;
	protected $data = null;
	
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
		$this->data = &$sloth->data;
		
		$this->processGroupCols($groupCols);
		if (empty($this->groupCols))
			throw new \Weby\Sloth\Exception('No group columns.');
		
		$this->processValueCols($valueCols);
	}
	
	protected function processGroupCols($groupCols)
	{
		$firstRow = $this->getFirstRow();
		
		foreach ((array) $groupCols as $groupCol) {
			if (is_array($groupCol)) {
				$fieldName = key($groupCol);
				$fieldValue = current($groupCol);
			} else {
				$fieldName = $groupCol;
				$fieldValue = $groupCol;
			}
			
			if (!array_key_exists($fieldName, $firstRow)) {
				throw new \Weby\Sloth\Exception(sprintf('Unknown group column "%s".', $fieldName));
			}
			
			$this->groupCols[] = $fieldName;
			$this->groupColsAliases[$fieldName] = $fieldValue;
		}
	}
	
	protected function processValueCols($valueCols)
	{
		$firstRow = $this->getFirstRow();
		
		foreach ((array) $valueCols as $valueCol) {
			if (is_array($valueCol)) {
				$fieldName = key($valueCol);
				$fieldValue = current($valueCol);
			} else {
				$fieldName = $valueCol;
				$fieldValue = $valueCol;
			}
			
			if (!array_key_exists($fieldName, $firstRow)) {
				throw new \Weby\Sloth\Exception(sprintf('Unknown value column "%s".', $valueCol));
			}
			
			$this->valueCols[] = $fieldName;
			$this->valueColsAliases[$fieldName] = $fieldValue;
		}
	}
	
	protected function getFirstRow()
	{
		$firstRow = $this->data[0];
		if (!is_array($firstRow)) {
			$firstRow = $this->convertRowToArray($firstRow);
		}
		return $firstRow;
	}
	
	protected function convertRowToArray($row)
	{
		if ($row instanceof \stdClass) {
			$row = (array) $row;
		} elseif (method_exists($row, 'toArray')) {
			$row = $row->toArray();
		} else {
			throw new \Weby\Sloth\Exception('Unsupported input format.');
		}
		
		return $row;
	}
}
