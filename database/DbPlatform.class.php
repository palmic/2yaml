<?php

/**
* Database platform control implementation. Classes for exact platform must be inherited from it
* @author Michal Palma <palmic at centrum dot cz>
* @copyleft (l) 2005-2006  Michal Palma
* @package DbControl
* @version 1.5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @date 2006-01-11
*/
abstract class DbPlatform
{

    //== Attributes ======================================================================

    /**
    * database connection
    * @type specific database connection resource
    */
    protected $connection;

    /**
    * Messenger for DBMS connection parameters
    * @type DbParametersMessenger
    */
    private $dbParametersMessenger;

    /**
    * Handler of shared attributes
    * @type DbStateHandler
    */
    protected $dbStateHandler;

    /**
    * Active Transaction flag
    * @type boolean
    */
    protected $activeTransaction = false;

    /**
    * Auto commit yes or not
    * @type boolean
    */
    protected $autoCommit;


    //== constructors ====================================================================

    //php does not accept abstract definition of constructor
    //public function DbMysql(DbParametersMessenger $DbParametersMessenger, DbStateHandler $dbStateHandler);


    //== public functions ================================================================

    /**
    * Fetch a gived result and return actual row in array
    * @return array
    */
    abstract public function fetchAssoc($result);

    /**
    * Return ID of last created Row
    * @return string
    */
    abstract public function getLastId();

    /**
    * Select working DB schema
    * @return void
    */
    abstract public function selectDb(/*string*/ $dbName);

    /**
    * Query database platform
    * @return database result
    */
    abstract public function query(/*string*/ $query);

    /**
    * Gets the number of rows in last result
    * @return integer
    */
    abstract public function getNumRows(/*resource*/ $result);

    /**
    * returns Column names in given result
    * @return array
    */
    abstract public function getColnames(/*resource*/ $result);

    /**
    * returns array with table columns asociated by their names
    * @return array
    */
    abstract public function getTableColumns(/*string*/ $schemaName, /*string*/ $tableName);

    /**
    * Begin transaction sequence
    * @return void
    */
    abstract public function transactionStart(/*boolean*/ $autoCommit);

    /**
    * Commit active transaction
    * @return void
    */
    abstract public function transactionCommit();

    /**
    * Rollback active transaction
    * @return void
    */
    abstract public function transactionRollback();


    //== protected functions =============================================================

    /**
    * Opens connection on database
    * @return void
    */
    abstract protected function connect();
}

?>