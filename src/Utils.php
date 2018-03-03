<?php
namespace Weby\Sloth;

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
}