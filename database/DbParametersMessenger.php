<?php

/**
* Messenger for DBMS connection parameters
* @author Michal Palma <palmic at centrum dot cz>
* @copyleft (l) 2005-2006  Michal Palma
* @package DbControl
* @version 1.5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
    * database source name (essential for ODBC)
    */
    public $dsn;

    /**
    * Connect string exclusively for PostgreSQL and Oracle. They ignore other elements. Mysql + Mssql + ODBC ignore this element.
    */
    public $port;


    //== constructors ====================================================================

    public function DbParametersMessenger($host = false, $name = false, $password = false, $dsn = false, $port = false) {
        $this->loginHost = $host;
        $this->loginName = $name;
        $this->loginPassword = $password;
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
