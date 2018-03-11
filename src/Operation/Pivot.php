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

/**
 * Pivot (pivot table) operation.
 */
class Pivot extends Base
{
	private $columnCols = array();
	private $columnColsAliases = array();
	
	private $addedColumnCols = array();
	
	private $groupedData = null;
	private $groups = array();
	
	/**
	 * @var \Weby\Sloth\Operation\Group
	 */
	private $group = null;
	
	public function __construct(Sloth $sloth, $groupCols, $columnCols, $valueCols)
	{
		parent::__construct($sloth, $groupCols, $valueCols);
		
		$this->columnCols = (array) $columnCols;
		if (!$this->columnCols)
			throw new \Weby\Sloth\Exception('No column columns.');
		
		$this->group = new Group(
			$sloth,
			array_merge((array) $groupCols, (array) $columnCols),
			$valueCols
		);
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::count()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function count($fieldName = null, $options = null)
	{
		$this->group->count($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::accum()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function accum($fieldName = null, $options = null)
	{
		$this->group->accum($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::avg()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function avg($fieldName = null, $options = null)
	{
		$this->group->avg($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::first()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function first($fieldName = null, $options = null)
	{
		$this->group->first($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::median()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function median($fieldName = null, $options = null)
	{
		$this->group->median($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::min()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function max($fieldName = null, $options = null)
	{
		$this->group->max($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::min()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function min($fieldName = null, $options = null)
	{
		$this->group->min($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::mode()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function mode($fieldName = null, $options = null)
	{
		$this->group->mode($fieldName, $options);
		
		return $this;
	}
	
	/**
	 * @see \Weby\Sloth\Operation\Group::sum()
	 * @param string $fieldName
	 * @param array $options
	 * @return \Weby\Sloth\Operation\Pivot
	 */
	public function sum($fieldName = null, $options = null)
	{
		$this->group->sum($fieldName, $options);
		
		return $this;
	}
	
	public function addColumn($name, $values = null)
	{
		$this->addedColumnCols[$name] = $values;
		
		return $this;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Weby\Sloth\Operation\Base::perform()
	 */
	protected function perform()
	{
		$this->beginPerform();
		$this->doPerform();
		$this->endPerform();
	}
	
	private function beginPerform()
	{
		$this->output = array();
		
		if (!$this->group->getFuncs()) {
			// Apply default function.
			$this->group->first(null, array('flat' => true));
		}
		
		$this->resetGroups();
	}
	
	private function doPerform()
	{
		$groupedData = $this->group->select();
		foreach ($groupedData as $row) {
			$key = $this->getGroupKey($row);
			if (!$this->isGroup($key)) {
				$group = &$this->addGroup($key, $row);
				$this->output[] = &$group;
			} else {
				$group = &$this->updateGroup($key, $row);
			}
		}
	}
	
	private function endPerform()
	{
		// Do nothing.
	}
	
	private function &addGroup($key, $row)
	{
		$result = array();
		
		foreach ($this->groupColsAliases as $groupColAlias) {
			$result[$groupColAlias] = $row[$groupColAlias];
		}
		
		foreach ($this->columnCols as $columnCol) {
			foreach ($this->group->getFuncs() as $func) {
				if ($func instanceof \Weby\Sloth\Func\Group\Base) {
					$fieldName = $func->getFieldName();
					$fieldNameAlias = ($fieldName
						? $row[$columnCol] . '_' . $fieldName
						: $row[$columnCol]
					);
					
					// If there are different number of cols
					// in rows then add missed ones.
					$this->propagateColumns($result);
					if (!isset($this->columnColsAliases[$fieldNameAlias])) {
						$this->columnColsAliases[$fieldNameAlias] = true;
						$this->backPropagateColumn($fieldNameAlias);
					}
					
					$result[$fieldNameAlias] = $row[$fieldName];
				} else {
					foreach ($this->valueColsAliases as $valueColAlias) {
						$fieldName = $func->getFieldName($valueColAlias);
						$fieldNameAlias = ($fieldName
							? $row[$columnCol] . '_' . $fieldName
							: $row[$columnCol]
						);
						
						// If there are different number of cols
						// in rows then add missed ones.
						$this->propagateColumns($result);
						if (!isset($this->columnColsAliases[$fieldNameAlias])) {
							$this->columnColsAliases[$fieldNameAlias] = true;
							$this->backPropagateColumn($fieldNameAlias);
						}
						
						$result[$fieldNameAlias] = $row[$fieldName];
					}
				}
			}
		}
		
		foreach ($this->addedColumnCols as $addedColumnCol => $values) {
			$value = null;
			if (is_array($values)) {
				// TODO
			} elseif ($values instanceof \Closure) {
				$value = call_user_func($values, $result);
			} else {
				$value = $values;
			}
			$result[$addedColumnCol] = $value;
		}
		$this->groups[$key] = &$result;
		
		return $result;
	}
	
	private function &updateGroup($key, $row)
	{
		$result = &$this->groups[$key];
		
		foreach ($this->columnCols as $columnCol) {
			foreach ($this->group->getFuncs() as $func) {
				if ($func instanceof \Weby\Sloth\Func\Group\Base) {
					$fieldName = $func->getFieldName();
					$fieldNameAlias = ($fieldName
						? $row[$columnCol] . '_' . $fieldName
						: $row[$columnCol]
					);
					
					if (!isset($this->columnColsAliases[$fieldNameAlias])) {
						$this->columnColsAliases[$fieldNameAlias] = true;
						$this->backPropagateColumn($fieldNameAlias);
					}
					
					$result[$fieldNameAlias] = $row[$fieldName];
				} else {
					foreach ($this->valueColsAliases as $valueColAlias) {
						$fieldName = $func->getFieldName($valueColAlias);
						$fieldNameAlias = ($fieldName
							? $row[$columnCol] . '_' . $fieldName
							: $row[$columnCol]
						);
						
						if (!isset($this->columnColsAliases[$fieldNameAlias])) {
							$this->columnColsAliases[$fieldNameAlias] = true;
							$this->backPropagateColumn($fieldNameAlias);
						}
						
						$result[$fieldNameAlias] = $row[$fieldName];
					}
				}
			}
		}
		
		foreach ($this->addedColumnCols as $addedColumnCol => $values) {
			$value = null;
			if (is_array($values)) {
				// TODO
			} elseif ($values instanceof \Closure) {
				$value = call_user_func($values, $result);
			} else {
				$value = $values;
			}
			$result[$addedColumnCol] = $value;
		}
		
		return $result;
	}
	
	private function backPropagateColumn($col)
	{
		foreach ($this->groups as $key => &$row) {
			if (!array_key_exists($col, $row)) {
				$row[$col] = null;
			}
		}
	}
	
	private function propagateColumns(&$row)
	{
		foreach ($this->columnColsAliases as $columnColAlias => $isSet) {
			if (!array_key_exists($columnColAlias, $row)) {
				$row[$columnColAlias] = null;
			}
		}
	}
	
	private function getGroupKey($row)
	{
		$result = '';
		
		foreach ($this->groupColsAliases as $groupColAlias) {
			$result .= $row[$groupColAlias];
		}
		
		$result = md5($result);
		
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
	
	public function setScale($scale)
	{
		$this->group->setScale($scale);
		
		return parent::setScale($scale);
	}
}