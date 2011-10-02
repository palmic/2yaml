<?php

/**
 * Class defining universal FULLTEXT where conditions for QueryBuilder objects
 * @author Michal Palma <palmic at email dot cz>
 * @package DbControl
 * @version 1.5
 * @date 2009-04-22
 */
class QueryBuilderWhereFullText extends QueryBuilderWhere
{
	/**
	 * conditions array
	 * @var array
	 */
	protected $columns	= array();
	
	/**
	 * exact phrases of search
	 * @var array
	 */
	protected $phrases	= array();
	
	/**
	 * adds search columns
	 * @param array $columns
	 */
	public function addColumns(/*array*/ $columns = array()) {
		if (count($columns) < 1) {
			throw new DbControlException("Ilegal parameter column. Must be NOT-NULL array!");
		}
		$this->columns	= array_merge((array)$this->columns, (array)$columns);
	}

	/**
	 * adds search phrase
	 * @param string $phrase
	 */
	public function addPhrase(/*string*/ $phrase = "") {
		if (strlen($phrase) < 1) {
			throw new DbControlException("Ilegal parameter column. Must be NOT-NULL string!");
		}
		$this->phrases[]	= $phrase;
	}
	
	/**
	 * getter for search columns
	 * @return array
	 */
	public function getColumns() {
		try {
			return $this->columns;
		}
		catch(Exception $e) {
			throw $e;
		}
	}
	
	/**
	 * getter for phrases
	 * @return array
	 */
	public function getPhrases() {
		try {
			return $this->phrases;
		}
		catch(Exception $e) {
			throw $e;
		}
	}
	
	public function addConditionColumn(/*string*/ $column = "", $value = "", /*int*/ $comparison	= 0, $glue	= 0) {
		try {
			throw new LBoxException("This function is disabled in fulltext search");
		}
		catch(Exception $e) {
			throw $e;
		}
	}
}
?>