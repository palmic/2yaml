<?php

/**
* Interface for Database control PHP class
* @author Michal Palma <palmic at email dot cz>

* @package DbControl
* @version 1.5

* @date 2006-01-11
*/
interface DbControlInterface
{

    //== constructors ====================================================================

    public function DbControl($task, $charset = "utf8");


    //== public functions ================================================================

    /**
    * Sets a query for database
    * @return void
    */
    public function setQuery(/*string*/ $query, /*boolean*/ $noCache = true);

    /**
    * initiate a database query
    * @return DbResultInterface
    */
    public function initiate();

    /**
    * initiate given database query
    * @return DbResultInterface
    */
    public function initiateQuery(/*string*/ $query, /*boolean*/ $noCache = true);

    /**
    * Select working db schema
    * @return boolean
    */
    public function selectDb($dbName);

    /**
    * Return ID of last created Row
    * @return string
    */
    public function getLastId();

    /**
    * Begin transaction sequence. (Check Your MySQL table types. Must be InnoDB, or BDB)
    * @return void
    */
    public function transactionStart($autoCommit = false);

    /**
    * Commit active transaction
    * @return void
    */
    public function transactionCommit();

    /**
    * Rollback active transaction
    * @return void
    */
    public function transactionRollback();

}

?>