<?php
/*
* preloads all libs
* @package DbControl
* @date 2006-01-11
*/


// interfaces
require_once("DbControlInterface.class.php");
require_once("DbResultInterface.class.php");
require_once("QueryBuilderInterface.class.php");

// abstract classes
require_once("DbPlatform.class.php");

// exceptions
require_once("DbControlException.class.php");

// classes
require_once("DbLog.class.php");
require_once("DbCfg.class.php");
require_once("DbParametersMessenger.php");
require_once("DbControl.class.php");
require_once("DbResult.class.php");
require_once("DbResultNoCache.class.php");
require_once("DbSelector.class.php");
require_once("DbStateHandler.class.php");
require_once("QueryBuilder.class.php");
require_once("DbMysql.class.php");
require_once("DbPgsql.class.php");
require_once("DbMssql.class.php");
require_once("DbOdbc.class.php");

?>