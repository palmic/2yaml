<?php

/**
 * Class that helps with queries
 * @author Michal Palma <palmic at email dot cz>
 * @package DbControl
 * @version 1.5
 * @date 2006-01-11
 */
class QueryBuilder implements QueryBuilderInterface
{

	//== Attributes ======================================================================

	/**
	 * current DB task
	 * @var string
	 */
	protected $task;

	/**
	 * platform concrete QueryBuilderPlatform
	 * @var QueryBuilderPlatform
	 */
	protected $queryBuilderPlatform;

	public function __construct(/*string*/ $task = "") {
        if (!is_string($task)) {
            throw new DbControlException("Ilegal parameter task. Must be string.");
        }
		$this->task = $task;
	}

	public function getInsert(/*string*/ $table, /*array*/ $values) {
		try {
			return $this->getQueryBuilderPlatform()->getInsert($table, $values);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function getDelete(/*string*/ $table, QueryBuilderWhere $where = NULL) {
		try {
			return $this->getQueryBuilderPlatform()->getDelete($table, $where);
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function getUpdate(/*string*/ $table, /*array*/ $values, QueryBuilderWhere $where = NULL) {
		try {
			return $this->getQueryBuilderPlatform()->getUpdate($table, $values, $where);
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function getSelectColumns($table, $what = array(), QueryBuilderWhere $where = NULL, $limit	= array(), $groupBy = array(), $orderBy = array()) {
		try {
			return $this->getQueryBuilderPlatform()->getSelectColumns($table, $what, $where, $limit, $groupBy, $orderBy);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function getSelectCount($table, QueryBuilderWhere $where = NULL, $groupBy = array(), $limit	= array()) {
		try {
			return $this->getQueryBuilderPlatform()->getSelectCount($table, $where, $groupBy, $limit);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function getSelectMaxColumns($table, $what = array(), QueryBuilderWhere $where = NULL, $groupBy = array(), $orderBy = array()) {
		try {
			return $this->getQueryBuilderPlatform()->getSelectMaxColumns($table, $what, $where, $groupBy, $orderBy);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function getDoesTableExists($table, $database = "") {
		try {
			return $this->getQueryBuilderPlatform()->getDoesTableExists($table, $database);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function getCreateTable($table, $columns = array(), $attributes = array()) {
		try {
			return $this->getQueryBuilderPlatform()->getCreateTable($table, $columns, $attributes);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function getAddColumns($table, $columns = array()) {
		try {
			return $this->getQueryBuilderPlatform()->getAddColumns($table, $columns);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function getQuotesDatabaseName() {
		return $this->getQueryBuilderPlatform()->getQuotesDatabaseName();
	}

	public function getQuotesTableName() {
		return $this->getQueryBuilderPlatform()->getQuotesTableName();
	}

	public function getQuotesColumnName() {
		return $this->getQueryBuilderPlatform()->getQuotesColumnName();
	}

	public function getQuotesValue() {
		return $this->getQueryBuilderPlatform()->getQuotesValue();
	}
	
	/**
	 * returns concrete QueryBuilderPlatform for platform attached to specified task
	 * @return QueryBuilderPlatform
	 */
	protected function getQueryBuilderPlatform() {
		try {
			if ($this->queryBuilderPlatform instanceof QueryBuilderPlatform) {
				return $this->queryBuilderPlatform;
			}
			$dbSelector = new DbSelector();
			return $this->queryBuilderPlatform	= $dbSelector->getQueryBuilderPlatform($this->task);
		}
		catch (Exception $e) {
			throw $e;
		}
	}
}
?>