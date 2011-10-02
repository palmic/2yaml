<?php

/**
* Database platform implementation for PgSQL.
* @author Michal Palma <palmic at centrum dot cz>
* @copyleft (l) 2005-2006  Michal Palma
* @package DbControl
* @version 1.5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @date 2006-01-11
*/
class DbPgsql extends DbPlatform
{

    //== Attributes ======================================================================

    /*
    * Working database name. Every connection has fixed working database in PostgreSQL
    * @type string
    */
    protected $dbName;

    /*
    * Target table of last INSERT query. Needed for possible future calling of getLastId
    * @type string
    */
    protected $lastInsertToTable;


    //== constructors ====================================================================

    public function DbPgsql(DbParametersMessenger $DbParametersMessenger, DbStateHandler $dbStateHandler) {
        if (!in_array("pgsql", get_loaded_extensions())) {
            throw new DbControlException("Your PHP configuration does not support PgSQL extension. Check php.ini.");
        }
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
        @pg_close($this->connection);
    }

    //== public functions ================================================================

    public function fetchAssoc($result) {
        if (!is_resource($result)) {
            throw new DbControlException("Ilegal parameter result. Must be valid result resource.");
        }
        else {
            return @pg_fetch_assoc($result);
        }
    }

    public function getLastId() {
        try {
            if (!is_resource($result = $this->query("SELECT LASTVAL() FROM ". $this->lastInsertToTable))) {
                throw new DbControlException("Acquire of Last inserted id failed.");
            }
            $id = $this->fetchAssoc($result);
            return current($id);
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function selectDb(/*string*/ $dbName) {
        if (!is_string($dbName)) {
            throw new DbControlException("Ilegal parameter dbName. Must be string.");
        }
        $this->dbName = $dbName;
        if (is_resource($this->connection)) {
            if (@pg_dbname($this->connection) == $this->dbName) {
                return;
            }
        }
        try {
            $this->connect();
        }
        catch (Exception $e) {
            $this->throwPgsqlException("Cannot select DB: ". $e->getMessage());
        }
    }

    public function query(/*string*/ $query) {
        if (!is_string($query)) {
            throw new DbControlException("Ilegal parameter query. Must be string.");
        }

        if (stristr($query, "INSERT")) {
            $qry = trim(substr($query, stripos($query, "INTO") + 4));
            trim($this->lastInsertToTable = substr($qry, 0, stripos($qry, " ") + 1));
        }

        if (!$result = @pg_query($this->getConnection(), $query)) {
            $this->throwPgsqlException();
        }
        return $result;
    }

    public function getNumRows(/*resource*/ $result) {
        if (!is_resource($result)) {
            throw new DbControlException("Ilegal parameter result. Must be valid result resource.");
        }
        else {
            return @pg_num_rows($result);
        }
    }

    public function getColnames(/*resource*/ $result) {
        if (!is_resource($result)) {
            throw new DbControlException("Ilegal parameter result. Must be valid result resource.");
        }
        if (!$numFields = @pg_num_fields($result)) {
            throw new DbControlException("No Column in result.");
        }
        for ($i = 0; $i < $numFields; $i++) {
            if (!$colname = @pg_field_name($result, $i)) {
                $this->throwPgsqlException("Colnames reading error.");
            }
            $colnames[$i] = $colname;
        }
        return $colnames;
    }

    public function getTableColumns(/*string*/ $schemaName, /*string*/ $tableName) {
        if (strlen($schemaName) < 1) {
            throw new DbControlException("Ilegal parameter schemaName. Must be string.");
        }
        if (strlen($tableName) < 1) {
            throw new DbControlException("Ilegal parameter tableName. Must be string.");
        }
        try {
            $dbSchema   = "information_schema";
            $sql        = "SELECT
                            COLUMN_NAME AS name,
                            DATA_TYPE AS type
                                FROM $dbSchema.COLUMNS
                                    WHERE TABLE_SCHEMA = '$schemaName'
                                    AND   TABLE_NAME   = '$tableName'";
            $result     = $this->query($sql);
            while ($row = $this->fetchAssoc($result)) {
                $columns[$row["name"]]["type"] = $row["type"];
            }
            return $columns;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function transactionStart(/*boolean*/ $autoCommit) {
        if (!is_bool($autoCommit)) {
            throw new DbControlException("Ilegal parameter autoCommit. Must be boolean.");
        }
        if (!$this->activeTransaction) {
            try {
                $this->query("BEGIN TRANSACTION");
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
                $this->query("COMMIT TRANSACTION");
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
                $this->query("ROLLBACK TRANSACTION");
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

    /**
    * Set persistent connection.
    */
    protected function connect() {
        if(!is_resource($this->connection)) {
            if (strlen($this->dbName) < 1) {
                # Exception code -1 for connection error
                throw new DbControlException("For PostgreSQL you have to call selectDb(\$dbname) before initiate please.", -1);
            }
            if (strlen($this->dbParametersMessenger->port) > 0) {
                $portString = " port=". $this->dbParametersMessenger->port;
            }
            else {
                $portString = "";
            }
            $this->connection = @pg_pconnect("host=". $this->dbParametersMessenger->loginHost . $portString ." dbname= ". $this->dbName ." user=". $this->dbParametersMessenger->loginName ." password=". $this->dbParametersMessenger->loginPassword, PGSQL_CONNECT_FORCE_NEW);
            if (!is_resource($this->connection)) {
                # Exception code -1 for connection error
                throw new DbControlException("Cant connect to database pgsql.\nhost = ". $this->dbParametersMessenger->loginHost ."\ndatabase name = ". $this->dbName, -1);
            }
            switch (strtoupper($this->dbStateHandler->getCharset())) {
                case "CP1250":
                    $encoding = "WIN1250";
                break;
                default:
                    $encoding = strtoupper($this->dbStateHandler->getCharset());
            }
            if (strlen($encoding) > 0) {
                if (pg_set_client_encoding($this->connection, $encoding) < 0) {
                    $this->throwPgsqlException("Cannot set charset of DB session to '". $encoding ."'.");
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
    * Throws Exception with Pgsql error infos
    * @return void
    */
    protected function throwPgsqlException(/*string*/ $addToMessage = "") {
        if (strlen($addToMessage) > 0) {
            $message = $addToMessage ." \n". @pg_last_error($this->connection);
        }
        else {
            $message = @pg_last_error($this->connection);
        }
        throw new DbControlException($message, 1); // Exception code>0 needed for easy detection of connection error Exception with code of -1. 0 has Exception whithout code number.
    }
}

?>