<?php

/**
 * Interface of class that helps with queries
 * @author Michal Palma <palmic at email dot cz>

 * @package DbControl
 * @version 1.5

 * @date 2006-01-11
 */
interface QueryBuilderInterface
{

	//== constructors ====================================================================

	public function __construct(/*string*/ $platform = "");


	//== public functions ===============================================================

	/**
	 * returns prior INSERT query
	 * @param string $table
	 * @param array $values
	 * @return string
	 */
	public function getInsert(/*string*/ $table, /*array*/ $values);

	/**
	 * returns prior DELETE query
	 * @param string $table
	 * @param QueryBuilderWhere $where
	 * @return string
	 */
	public function getDelete(/*string*/ $table, QueryBuilderWhere $where = NULL);
	
	/**
	 * returns prior UPDATE query
	 * @param string $table
	 * @param array $values
	 * @param QueryBuilderWhere $where
	 * @return string
	 */
	public function getUpdate(/*string*/ $table, /*array*/ $values, QueryBuilderWhere $where = NULL);
	
	/**
	 * Vraci string bezneho selectu
	 * @param string $table
	 * @param array $what
	 * @param QueryBuilderWhere $where - in form array("columnName" => "columnValue", ...)
	 * @param array $groupBy - in form array("columnName", ...)
	 * @param array $orderBy - in form array("columnName" => 0/1, ...) 1 for asc, 0 for desc
	 * @param array $limit - array(min, count). For example $limit = array(0, 10) returns rows 1-11
	 */
	public function getSelectColumns($table, $what = array(), QueryBuilderWhere $where = NULL, $limit	= array(), $groupBy = array(), $orderBy = array());

	/**
	 * vraci string select count
	 * @param string $table
	 * @param QueryBuilderWhere $where
	 * @param array $groupBy
	 * @param array $limit - array(min, count). For example $limit = array(0, 10) returns rows 1-11
	 */
	public function getSelectCount($table, QueryBuilderWhere $where = NULL, $groupBy = array(), $limit = array());
	
	/**
	 * vraci string select MAX($columnName) pro vsechny columnNames v poli $what
	 * @param string $table
	 * @param array $what
	 * @param QueryBuilderWhere $where
	 * @param array $groupBy
	 * @param array $orderBy
	 */
	public function getSelectMaxColumns($table, $what = array(), QueryBuilderWhere $where = NULL, $groupBy = array(), $orderBy = array());

	/**
	 * vraci select na zjisteni jestli tabulka v databazi existuje
	 * @param string $table
	 * @param string $database
	 * @return string
	 */
	public function getDoesTableExists($table, $database = "");

	/**
	 * vraci SQL na vytvoreni tabulky
	 * @param string $table
	 * @param array $columns
	 * @param array $attributes
	 * @return string
	 */
	public function getCreateTable($table, $columns = array(), $attributes = array());

	/**
	 * vraci select na pridani sloupcu do tabulky
	 * @param array $table
	 * @param array $columns
	 * @return string
	 */
	public function getAddColumns($table, $columns = array());

	/**
	 * returns database name quotes - array("quote before", "quote after")
	 * @return array
	 */
    public function getQuotesDatabaseName();

	/**
	 * returns table name quotes - array("quote before", "quote after")
	 * @return array
	 */
    public function getQuotesTableName();

	/**
	 * returns column name quotes - array("quote before", "quote after")
	 * @return array
	 */
    public function getQuotesColumnName();

	/**
	 * returns value quotes - array("quote before", "quote after")
	 * @return array
	 */
    public function getQuotesValue();
}
?>