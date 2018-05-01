<?php
/**
 * Weby\Sloth
 *
 * @vendor      Weby
 * @package     Sloth
 * @link        https://github.com/maximiliamus/weby-sloth
 */

namespace Weby\Sloth;

/**
 * Main class to perform data manipulations.
 */
class Sloth
{
	const DATA_UNKNOWN = 0;
	const DATA_ARRAY   = 1;
	const DATA_ASSOC   = 2;
	const DATA_OBJECT  = 3;
	
	const COLUMN_SEPARATOR = "\u{241F}"; // "Unit Separator" unicode symbol.
	const ARRAY_OUTPUT_COLUMN_SEPARATOR = '.';
	const ASSOC_OUTPUT_COLUMN_SEPARATOR = '|';
	
	/**
	 * Input data.
	 * 
	 * @var array
	 */
	public $data = null;
	
	/**
	 * Type of input data.
	 * 
	 * @var int
	 */
	public $dataType = self::DATA_UNKNOWN;
	
	/**
	 * Input data is array of non-assoc arrays.
	 * 
	 * @var bool
	 */
	public $isArray  = false;
	
	/**
	 * Input data is array of assoc arrays.
	 * 
	 * @var bool
	 */
	public $isAssoc  = false;
	
	/**
	 * Input data is array of objects.
	 * 
	 * @var bool
	 */
	public $isObject = false;
	
	private $firstRow;
	
	/**
	 * Returns Sloth's instance created for specified data.
	 * 
	 * @param array $data
	 * @return \Weby\Sloth\Sloth
	 */
	public static function from(&$data)
	{
		return new Sloth($data);
	}
	
	/**
	 * Creates Sloth's instance for specified data.
	 * 
	 * @param array $data
	 * @throws \Weby\Sloth\Exception
	 */
	public function __construct(&$data)
	{
		$this->data = &$data;
		
		if (!$this->data) {
			throw new \Weby\Sloth\Exception('No data.');
		}
		
		$this->assignDataType();
		$this->assignFirstRow();
	}
	
	/**
	 * Provides fluent interface to "group" operation.
	 * 
	 * @param string|array $groupCols
	 * @param string|array $valueCols
	 * @return \Weby\Sloth\Operation\Group
	 */
	public function group($groupCols, $valueCols = null)
	{
		$group = new Operation\Group($this, $groupCols, $valueCols);
		
		return $group;
	}
	
	/**
	 * Provides fluent interface to "pivot" operation.
	 * 
	 * @param string|array $groupCols
	 * @param string|array $columnCols
	 * @param string|array $valueCols
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function pivot($groupCols, $columnCols, $valueCols)
	{
		$pivot = new Operation\Pivot($this, $groupCols, $valueCols, $columnCols);
		
		return $pivot;
	}
	
	private function assignFirstRow()
	{
		$this->firstRow = $this->data[0];
		if (!is_array($this->firstRow)) {
			$this->firstRow = Utils::toArray($this->firstRow);
		}
	}
	
	private function assignDataType()
	{
		$firstRow = $this->data[0];
		if (is_array($firstRow)) {
			if (Utils::isAssoc($firstRow)) {
				$this->isAssoc = true;
				$this->dataType = self::DATA_ASSOC;
			} else {
				$this->isArray = true;
				$this->dataType = self::DATA_ARRAY;
			}
		} elseif (is_object($firstRow)) {
			$this->isObject = true;
			$this->dataType = self::DATA_OBJECT;
		} else {
			throw new \Weby\Sloth\Exception('Unknown data type.');
		}
	}
	
	/**
	 * Checks whether a colum exists in the input data.
	 * 
	 * @param string|int $colName Name or index of column.
	 * @return bool
	 */
	public function isColExists($colName)
	{
		return array_key_exists($colName, $this->firstRow);
	}
}