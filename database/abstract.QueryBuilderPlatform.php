<?php
abstract class QueryBuilderPlatform
{
	protected $regExpDynamic	= "<<(.*)>>";
	
	/**
	 * returns prior INSERT query
	 * @param string $table
	 * @param array $values
	 * @return string
	 */
	public abstract function getInsert(/*string*/ $table, /*array*/ $values = array());
	
	/**
	 * returns prior DELETE query
	 * @param string $table
	 * @param QueryBuilderWhere $where
	 * @return string
	 */
	public abstract function getDelete(/*string*/ $table, QueryBuilderWhere $where = NULL);
	
	/**
	 * returns prior UPDATE query
	 * @param string $table
	 * @param array $values - array("colName" => value), where value like "<<(.*)>>" is used as is: (4ex. array("price" => "<<price*1.2>>") --> price=price*1.2)
	 * @param QueryBuilderWhere $where
	 * @return string
	 */
	public abstract function getUpdate(/*string*/ $table, /*array*/ $values, QueryBuilderWhere $where = NULL);
	
	/**
	 * returns database name quotes - array("quote before", "quote after")
	 * @return array
	 */
    public abstract function getQuotesDatabaseName();

	/**
	 * returns table name quotes - array("quote before", "quote after")
	 * @return array
	 */
    public abstract function getQuotesTableName();

	/**
	 * returns column name quotes - array("quote before", "quote after")
	 * @return array
	 */
    public abstract function getQuotesColumnName();

	/**
	 * returns value quotes - array("quote before", "quote after")
	 * @return array
	 */
    public abstract function getQuotesValue();

    /**
     * Vraci string bezneho selectu
     * @param string $table
     * @param array $what
     * @param QueryBuilderWhere $where - in form array("columnName" => "columnValue", ...)
     * @param array $groupBy - in form array("columnName", ...)
     * @param array $orderBy - in form array("columnName" => 0/1, ...) 1 for asc, 0 for desc
	 * @param array $limit - array(min, count). For example $limit = array(0, 10) returns rows 1-11
     */
    public abstract function getSelectColumns($table, $what = array(), QueryBuilderWhere $where = NULL, $limit	= array(), $groupBy = array(), $orderBy = array());
    
    /**
     * vraci string select count
     * @param string $table
     * @param QueryBuilderWhere $where
     * @param array $groupBy
	 * @param array $limit - array(min, count). For example $limit = array(0, 10) returns rows 1-11
     */
    public abstract function getSelectCount($table, QueryBuilderWhere $where = NULL, $groupBy = array(), $limit	= array());
   
	/**
	 * vraci string select MAX($columnName) pro vsechny columnNames v poli $what
	 * @param string $table
	 * @param array $what
	 * @param QueryBuilderWhere $where
	 * @param array $groupBy
	 * @param array $orderBy
	 */
	public abstract function getSelectMaxColumns($table, $what = array(), QueryBuilderWhere $where = NULL, $groupBy = array(), $orderBy = array());
	
	/**
	 * vraci dotaz na existenci tabulky v databazi podle schematu
	 * @param string $table
	 * @param string $schema
	 * @return string
	 */
	public abstract function getDoesTableExists($table, $database = "");
    
	/**
	 * vraci SQL pro vytvoreni tabulky v databazi
	 * @param string $table
	 * @param array $columns array("name"=>"<colname>", "type"=>"int|shorttext|longtext|richtext". "notnull" => <bool default true>,
	 * 								"autoincrement" => bool default false, "default" => <default value>)
	 * @param array $attributes array("pk" => <primary key colname>)
	 * @return string
	 */
	public abstract function getCreateTable($table, $columns = array(), $attributes = array());
	
	/**
	 * vraci SQL pro pridani sloupcu do tabulky
	 * @param string $table
	 * @param array $columns
	 * @return string
	 */
	public abstract function getAddColumns($table, $columns = array());
	
	/**
	 * vraci datum zformatovane podle standardniho datetime typu konkretni databaze
	 * @param int $timeStamp
	 */
	public abstract function getValueFormatedDateTime($timeStamp	= 0);
    
	/**
	 * relevant database task
	 * @var string
	 */
	protected $task;
	
	public function __construct($task = "") {
		try {
			if (strlen($task) < 1) {
				throw new DbControlException("Ilegal parameter task. Must be NOT NULL string.");
			}
			$this->task	= $task;
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	/**
     * validuje hodnotu podle syntaxe konkretniho DB enginu
     * @param mixed $value
     * @return mixed
     */
    protected function getValueWrapped($value = "") {
    	// value is defined for direct use
    	if (ereg($this->regExpDynamic, $value, $regs) > 0) {
    		return $regs[1];
    	}
    	switch (true) {
			case (is_float($value) || ctype_digit($value)):
        /*$out = (int)$value;
			   break;*/
			case strtoupper($value) == "NULL":
					$out	= $value;
				break;
			case $value === FALSE:
					$out	= "FALSE";
				break;
			case $value === TRUE:
					$out	= "TRUE";
				break;
			default:
				$out	= reset($this->getQuotesValue()) . $this->escapeString($value) . end($this->getQuotesValue());
		}
		return $out;
    }

	/**
    * Make separated values string from array
    * @return string
    */
    protected function arrayToStr(/*array*/ $values, /*string*/ $separator = ", ") {
        if (!is_array($values)) {
            throw new DbControlException("Ilegal parameter values. Must be array.");
        }
        if (!is_string($separator)) {
            throw new DbControlException("Ilegal parameter separator. Must be string.");
        }
        return implode($separator, $values);
    }

    /**
     * returns clean escaped string for current DB platform
     * @param string $string
     * @return string
     */
	protected abstract function escapeString($string = "");
}
?>