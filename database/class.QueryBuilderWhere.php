<?php

/**
 * Class defining universal where conditions for QueryBuilder objects
 * @author Michal Palma <palmic at email dot cz>
 * @package DbControl
 * @version 1.5
 * @date 2008-11-12
 */
class QueryBuilderWhere
{
	/**
	 * conditions array
	 * @var array
	 */
	protected $conditions	= array();
	
	/**
	 * additional "wheres" array
	 * @var array
	 */
	protected $wheres	= array();
	
	/**
	 * adds condition
	 * @param string $column - condition column
	 * @param mixed $value - compared value
	 * @param int $comparison 	- type of comparison (-3 = !=, -2 = <, -1 = <=, 0 = equaling, 1 = >=, 2 = >, 3 LIKE '%value', 4 LIKE '%value%', 5 LIKE 'value%')
	 * @param int $glue 		- type of split (0 = AND, 1 = OR)
	 */
	public function addConditionColumn(/*string*/ $column = "", $value = "", /*int*/ $comparison	= 0, $glue	= 0) {
		if (strlen($column) < 1) {
			throw new DbControlException("Ilegal parameter column. Must be NOT-NULL string!");
		}
		if (!is_numeric($comparison)) {
			throw new DbControlException("Ilegal parameter comparison. Must be numeric!");
		}
		$this->conditions[]	= array("column"		=> $column,
									"value" 		=> $value,
									"comparison" 	=> $comparison,
									"glue" 			=> $glue,
		);
	}

	/**
	 * adds where into clauses definitions
	 * - its used like WHERE ($this->getConditions()) OR $where->getConditions() OR ....
	 * @param QueryBuilderWhere $where
	 * @param int $glue 		- type of split (0 = AND, 1 = OR)
	 */
	public function addWhere (QueryBuilderWhere $where, $glue	= 0) {
		try {
			$this->wheres[]	= array("where" 		=> $where,
									"glue"			=> $glue,
			);
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	/**
	 * returns all conditions defined
	 * @return array
	 */
	public function getConditions() {
		return $this->conditions;
	}

	/**
	 * returns all additional "OR wheres" defined
	 * @return array
	 */
	public function getWheres() {
		return $this->wheres;
	}
	
    /**
     * checks, if contains condition for given column name
     * @param string $columnName
     * @param QueryBuilderWhere $where
     * @return bool
     */
	public function doesContainsConditionColumn($columnName = "", QueryBuilderWhere $where = NULL) {
		try {
			if (!$where) {
				$where	= $this;
			}
			if (strlen($columnName) < 1) {
    			throw new LBoxException(LBoxException::MSG_PARAM_STRING_NOTNULL, LBoxException::CODE_BAD_PARAM);
    		}
    		// checks subwheres recursive
    		foreach ($where->getWheres() as $subWhere) {
	    		if ($this->doesContainsConditionColumn($columnName, $subWhere["where"])) {
	    			return true;
	    		}
	    	}
	    	// checks conditions
	    	foreach ($where->getConditions() as $condition) {
	    		if ($condition["column"] == $columnName) {
	    			return true;
	    		}
	    	}
	    	return false;
		}
		catch (Exception $e) {
			throw $e;
		}
	}
}
?>