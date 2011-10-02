<?php

/**
* Messenger for DBMS connection parameters
* @author Michal Palma <palmic at email dot cz>

* @package DbControl
* @version 1.5

* @date 2006-01-11
*/
class DbParametersMessenger
{

    //== Attributes ======================================================================

    /**
    * database host
    */
    public $loginHost;

    /**
    * database login name
    */
    public $loginName;

    /**
    * database login password
    */
    public $loginPassword;

    /**
    * database default schema
    */
    public $schema;

    /**
    * database source name (essential for ODBC)
    */
    public $dsn;

    /**
    * Connect string exclusively for PostgreSQL and Oracle. They ignore other elements. Mysql + Mssql + ODBC ignore this element.
    */
    public $port;


    //== constructors ====================================================================

    public function DbParametersMessenger($host = false, $name = false, $password = false, $schema = "", $dsn = false, $port = false) {
        $this->loginHost = $host;
        $this->loginName = $name;
        $this->loginPassword = $password;
        $this->schema = $schema;
        $this->dsn = $dsn;
        $this->port = $port;
    }

    //== destructors =====================================================================
    //== public functions ================================================================

    public function __set($n, $v) {
        throw new DbControlException("Changing attributes of DbParametersMessenger class not allowed!");
    }

    //== protected functions =============================================================
}

?>
