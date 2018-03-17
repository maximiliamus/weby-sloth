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
 * Provides auxiliary functions.
 */
class Utils
{
	/**
	 * Converts an object data row to an array.
	 * 
	 * @param object $row
	 * @return array
	 */
	public static function toArray($row)
	{
		if ($row instanceof \stdClass) {
			$row = (array) $row;
		} elseif (method_exists($row, 'toArray')) {
			$row = $row->toArray();
		}
		return $row;
	}
	
	/**
	 * Checks whether a data row is an assoc array.
	 * 
	 * @param array $row
	 * @return boolean
	 */
	public static function isAssoc(&$row)
	{
		return is_string(key($row));
	}
	
	/**
	 * Normalize data to be an array regardless of initial type.
	 * 
	 * @param mixed $data
	 * @return array
	 */
	public static function normalizeArray($data)
	{
		$result = null;
		
		if (!$data) {
			$result = [];
		} elseif (is_array($data)) {
			$result = $data;
		} else {
			$result = [$data];
		}
		
		return $result;
	}
}