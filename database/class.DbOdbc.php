<?php


/**
* Database platform implementation for ODBC.
* @author Michal Palma <palmic at email dot cz>

* @package DbControl
* @version 1.5

* @date 2006-01-11
*/
class DbOdbc extends DbPlatform
{

    //== Attributes ======================================================================
    //== constructors ====================================================================

    public function DbOdbc(DbParametersMessenger $DbParametersMessenger, DbStateHandler $dbStateHandler) {
        $this->dbParametersMessenger = $DbParametersMessenger;
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
        @odbc_close($this->connection);
    }


    //== public functions ================================================================

    public function fetchAssoc($result) {
        if (!is_resource($result)) {
            throw new DbControlException("Ilegal parameter result. Must be valid result resource.");
        }
        else {
            # odbc_fetch_array returns the same array like forexample mysql_fetch_assoc (array associated by colnames)
            return $row = odbc_fetch_array($result);
        }
    }

    public function getLastId() {
        try {
            $result = $this->query("SELECT LAST_INSERT_ID()");
            $id = @odbc_result($result, 1);
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

        if (!$result = @odbc_exec($this->getConnection(), $query)) {
            $this->throwOdbcException();
        }
        return $result;
    }

    public function getNumRows(/*resource*/ $result) {
        if (!is_resource($result)) {
            throw new DbControlException("Ilegal parameter result. Must be valid result resource.");
        }
        else {
            return @odbc_num_rows($result);
        }
    }

    public function getColnames(/*resource*/ $result) {
        if (!is_resource($result)) {
            throw new DbControlException("Ilegal parameter result. Must be valid result resource.");
        }
        if (!$numFields = @odbc_num_fields($result)) {
            throw new DbControlException("No Column in result.");
        }
        for ($i = 0; $i < $numFields; $i++) {
            if (!$colname = @odbc_field_name($result, $i + 1)) {
                $this->throwOdbcException("Colnames reading error.");
            }
            $colnames[$i] = $colname;
        }
        return $colnames;
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
            $cursorType = "SQL_CUR_USE_ODBC";
            $this->connection = @odbc_pconnect($this->dbParametersMessenger->dsn, $this->dbParametersMessenger->loginName, $this->dbParametersMessenger->loginPassword, $cursorType);
            if (!is_resource($this->connection)) {
                # Exception code -1 for connection error
                throw new DbControlException("Cant connect to database ODBC.\nhost = ". $this->dbParametersMessenger->loginHost ."\nDSN = ". $this->dbParametersMessenger->dsn ."\ncursor type = ". $cursorType, -1);
            }
            //setting prior charset for connection with ODBC just after connection is needeed from last versions (Espetialy for non-English signs)
            # Caution: Using $this->query() may rewrite query that is waiting for execute
            if (!@odbc_exec($this->connection, "SET NAMES '". $this->dbStateHandler->getCharset() ."';")) {
                $this->throwOdbcException("Cannot set charset of DB session.");
            }

            //setting default task database schema - if defined 
            # Caution: Using $this->query() may rewrite query that is waiting for execute
            if (strlen($schema = $this->dbParametersMessenger->schema) > 0) {            	
	            if (!@odbc_exec($this->connection, "USE $schema;")) {
	                $this->throwOdbcException("Cannot select default database schema '$schema'.");
	            }
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
    * Throws Exception with ODBC error infos
    * @return void
    */
    protected function throwOdbcException(/*string*/ $addToMessage = "") {
        if (is_string($addToMessage)) {
            $message = $addToMessage ." \n". @odbc_errormsg($this->connection);
        }
        else {
            $message = @odbc_errormsg($this->connection);
        }
        # ODBC error code can be invalid for Exception code..
        $message = "Message: ". $message ."\nCode: ". @odbc_error($this->connection);
        throw new DbControlException($message);
    }
}

?>
