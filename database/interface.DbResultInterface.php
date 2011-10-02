<?php

/**
* interface of class controling db result
* @author Michal Palma <palmic at email dot cz>

* @package DbControl
* @version 1.5

* @date 2006-01-11
*/
interface DbResultInterface
{

    //== constructors ====================================================================

    public function DbResult(/*resource*/ $result, DbStateHandler $dbStateHandler);


    //== public functions ================================================================

    /**
    * Getter for  particular column of actual record
    * @return column content or all columns of record in associated array
    * @param string columnName ("*" for all columns of record in associated array)
    */
     public function __get(/*string*/ $columnName = "*");

     /**
     * Alias of __get()
     */
     public function get($columnName = "*");


    /**
    * set a cursor at start
    * @return void
    */
     public function first();

    /**
    * set a cursor at end
    * @return void
    */
     public function last();

    /**
    * set a cursor at previous row
    * @return boolean
    */
     public function previous();

    /**
    * set a cursor at next row
    * @return boolean
    */
     public function next();

    /**
    * getter of column names of result
    * @return array
    */
     public function getColnames();

    /**
    * Gets the number of rows in current result
    * @return integer
    */
    public function getNumRows();
}

?>
