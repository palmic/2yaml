<?php


/**
* Database platform implementation for MSSQL.
* @author Michal Palma <palmic at centrum dot cz>
* @copyleft (l) 2005-2006  Michal Palma
* @package DbControl
* @version 1.5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @date 2006-01-11
*/
class DbMssql extends DbPlatform
{

    //== Attributes ======================================================================
    //== constructors ====================================================================

    public function DbMssql(DbParametersMessenger $dbParametersMessenger, DbStateHandler $dbStateHandler) {
        if (!in_array("mssql", get_loaded_extensions())) {
            throw new DbControlException("Your PHP configuration does not support MSSQL extension. Check php.ini.");
        }
        $this->dbParametersMessenger = $dbParametersMessenger;
        $this->dbStateHandler = $dbStateHandler;
    }

    //== destructors =====================================================================

    public function __destruct() {
        if ($this->activeTransaction) {
            if ($this->autoCommit) {
                $this->transactionCommit();
            }
            else {
                $this->transactionRollback();
            }
        }
        @mssql_close($this->connection);
    }


    //== public functions ================================================================

    public function fetchAssoc($result) {
        if (!is_resource($result)) {
            throw new DbControlException("Ilegal parameter result. Must be valid result resource.");
        }
        else {
            return @mssql_fetch_assoc($result);
        }
    }

    public function getLastId() {
        try {
            $result = $this->query("SELECT LAST_INSERT_ID()");
            $id = mssql_result($result, 0, 0);
        }
        catch (Exception $e) {
            throw new DbControlException("Error in trying to acquire Last inserted id.\n" . $e->getMessage(), $e->getCode());
        }
        return $id;
    }

    public function selectDb(/*string*/ $dbName) {
        if (!is_string($dbName)) {
            throw new DbControlException("Ilegal parameter dbName. Must be string.");
        }
        try {
            $this->query("USE ". $dbName);
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function query(/*string*/ $query) {
        if (!is_string($query)) {
            throw new DbControlException("Ilegal parameter query. Must be string.");
        }
        try {
            if (!$result = @mssql_query($query, $this->getConnection())) {
                $this->throwMssqlException("SQL query caused Error. Query: ". $query);
            }
        }
        catch (Exception $e) {
            throw $e;
        }

        return $result;
    }

    public function getNumRows(/*resource*/ $result) {
        if (!is_resource($result)) {
            throw new DbControlException("Ilegal parameter result. Must be valid result resource.");
        }
        else {
            return @mssql_num_rows($result);
        }
    }

    public function getColnames(/*resource*/ $result) {
        if (!is_resource($result)) {
            throw new DbControlException("Ilegal parameter result. Must be valid result resource.");
        }
        if (!$numFields = @mssql_num_fields($result)) {
            throw new DbControlException("No Column in result.");
        }
        for ($i = 0; $i < $numFields; $i++) {
            if (!$colname = @mssql_field_name($result, $i)) {
                $this->throwMssqlException("Colnames reading error.");
            }
            $colnames[$i] = $colname;
        }
        return $colnames;
    }

    public function getTableColumns(/*string*/ $schemaName, /*string*/ $tableName) {
        throw new DbControlException("Method is not implemented yet!");
    }

    public function transactionStart(/*boolean*/ $autoCommit) {
        if (!is_bool($autoCommit)) {
            throw new DbControlException("Ilegal parameter autoCommit. Must be boolean.");
        }
        if (!$this->activeTransaction) {
            try {
                $this->query("BEGIN WORK");
            }
            catch (Exception $e) {
                throw $e;
            }
            $this->autoCommit = $autoCommit;
            $this->activeTransaction = true;
        }
        else {
            throw new DbControlException("Multiple transactions are not supported.");
        }
    }

    public function transactionCommit() {
        if ($this->activeTransaction) {
            try {
                $this->query("COMMIT");
            }
            catch (Exception $e) {
                throw $e;
            }
            $this->activeTransaction = false;
        }
        else {
            throw new DbControlException("No transaction active.");
        }
    }

    public function transactionRollback() {
        if ($this->activeTransaction) {
            try {
                $this->query("ROLLBACK");
            }
            catch (Exception $e) {
                throw $e;
            }
            $this->activeTransaction = false;
        }
        else {
            throw new DbControlException("No transaction active.");
        }
    }


    //== protected functions =============================================================

    protected function connect() {
        if(!is_resource($this->connection)) {

            // we are not using port value in MSSQL connection - there was problems with that.
//            if (strlen($this->dbParametersMessenger->port) > 0) {
//                $hostString = $this->dbParametersMessenger->loginHost .":". $this->dbParametersMessenger->port;
//            }
//            else {
//                $hostString = $this->dbParametersMessenger->loginHost;
//            }
            
            $this->connection = @mssql_pconnect($hostString, $this->dbParametersMessenger->loginName, $this->dbParametersMessenger->loginPassword);
            if (!is_resource($this->connection)) {
                throw new DbControlException("Cant connect to database Mssql.\nhost = ". $this->dbParametersMessenger->loginHost, -1);
            }
        }
    }

    /**
    * getter for connection
    * @return valid connection resource
    */
    protected function getConnection() {
        try {
            $this->connect();
        }
        catch (Exception $e) {
            throw $e;
        }
        return $this->connection;
    }

    /**
    * Throws Exception with Mssql error infos
    * @return void
    */
    protected function throwMssqlException(/*string*/ $addToMessage = "") {
        if (is_string($addToMessage)) {
            $message = $addToMessage ."\n". @mssql_get_last_message();
        }
        else {
            $message = @mssql_get_last_message();
        }
        throw new DbControlException($message);
    }
}

?>