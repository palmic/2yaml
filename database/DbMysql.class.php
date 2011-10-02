<?php


/**
* Database platform implementation for MySQL.
* @author Michal Palma <palmic at centrum dot cz>
* @copyleft (l) 2005-2006  Michal Palma
* @package DbControl
* @version 1.5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @date 2006-01-11
*/
class DbMysql extends DbPlatform
{

    //== Attributes ======================================================================
    //== constructors ====================================================================

    public function DbMysql(DbParametersMessenger $DbParametersMessenger, DbStateHandler $dbStateHandler) {
        if (!in_array("mysql", get_loaded_extensions())) {
            throw new DbControlException("Your PHP configuration does not support MySQL extension. Check php.ini.");
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
        @mysql_close($this->connection);
    }


    //== public functions ================================================================

    public function fetchAssoc($result) {
        if (!is_resource($result)) {
            throw new DbControlException("Ilegal parameter result. Must be valid result resource.");
        }
        else {
            return @mysql_fetch_assoc($result);
        }
    }

    public function getLastId() {
        return @mysql_insert_id($this->getConnection());
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
        if (!$result = @mysql_query($query, $this->getConnection())) {
            $this->throwMysqlException();
        }
        return $result;
    }

    public function getNumRows(/*resource*/ $result) {
        if (!is_resource($result)) {
            throw new DbControlException("Ilegal parameter result. Must be valid result resource.");
        }
        else {
            return @mysql_num_rows($result);
        }
    }

    public function getColnames(/*resource*/ $result) {
        if (!is_resource($result)) {
            throw new DbControlException("Ilegal parameter result. Must be valid result resource.");
        }
        if (!$numFields = @mysql_num_fields($result)) {
            throw new DbControlException("No Column in result.");
        }
        for ($i = 0; $i < $numFields; $i++) {
            if (!$colname = @mysql_field_name($result, $i)) {
                $this->throwMysqlException("Colnames reading error.");
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
                            DATA_TYPE AS type,
                            CHARACTER_OCTET_LENGTH AS length
                                FROM $dbSchema.COLUMNS
                                    WHERE TABLE_SCHEMA = '$schemaName'
                                    AND   TABLE_NAME   = '$tableName'";
            $result     = $this->query($sql);
            while ($row = $this->fetchAssoc($result)) {
                $length = (is_numeric($row["length"]) && $row["length"] > 0) ? "(". $row["length"] .")" : "";
                $columns[$row["name"]]["type"] = $row["type"] . $length;
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

            // we are not using port value in MYSQL connection - there was problems with that.
//            if (strlen($this->dbParametersMessenger->port) > 0) {
//                $hostString = $this->dbParametersMessenger->loginHost .":". $this->dbParametersMessenger->port;
//            }
//            else {
//                $hostString = $this->dbParametersMessenger->loginHost;
//            }

            $this->connection = @mysql_pconnect($hostString, $this->dbParametersMessenger->loginName, $this->dbParametersMessenger->loginPassword);
            if (!is_resource($this->connection)) {
                # Exception code -1 for connection error
                throw new DbControlException("Cant connect to database mysql.\nhost = ". $this->dbParametersMessenger->loginHost, -1);
            }

            //setting prior charset for connection with MySQL just after connection is needeed from last versions (Espetialy for non-English signs)
            # Caution: Using $this->query() may rewrite query that is waiting for execute
            if (!@mysql_query("SET NAMES '". $this->dbStateHandler->getCharset() ."';", $this->connection)) {
                $this->throwMysqlException("Cannot set charset of DB session.");
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
    * Throws Exception with Mysql error infos
    * @return void
    */
    protected function throwMysqlException(/*string*/ $addToMessage = "") {
        if (is_string($addToMessage)) {
            $message = $addToMessage ." \n". @mysql_error($this->connection);
        }
        else {
            $message = @mysql_error($this->connection);
        }
        throw new DbControlException($message, @mysql_errno($this->connection));
    }
}

?>