<?php


/**
* Database platform selector
* @author Michal Palma <palmic at email dot cz>

* @package DbControl
* @version 1.5

* @date 2006-01-11
*/
class DbSelector
{

    //== Attributes ======================================================================

    /**
    * current platform name
    * @var string
    */
    protected $platformName;

    /**
    * handler of Config handler
    * @var DbCfg
    */
    protected $cfgH;


    //== constructors ====================================================================
    //== destructors =====================================================================
    //== public functions ================================================================

    /**
    * Getter for object to control database platform assigned for received task
    * @return DbPlatform
    */
    public function getPlatform(/*string*/ $task, $dbStateHandler) {
        if (!is_string($task)) {
            throw new DbControlException("Ilegal parameter task. Must be string.");
        }
		try {
	        $dbParametersMessenger = new DbParametersMessenger($this->getTaskHost($task), $this->getTaskName($task), $this->getTaskPassword($task), $this->getTaskSchema($task), false, $this->getTaskPort($task));
	        
	        switch ($platformName = strtolower($this->getPlatformName($task))) {
	            case "mysql":
	                $platform = new DbMysql($dbParametersMessenger, $dbStateHandler);
	                break;
	            case "pgsql":
	                $platform = new DbPgsql($dbParametersMessenger, $dbStateHandler);
	                break;
	            case "mssql":
	                $platform = new DbMssql($dbParametersMessenger, $dbStateHandler);
	                break;
	            case "odbc":
	                $platform = new DbOdbc($dbParametersMessenger, $dbStateHandler);
	                break;
	            default:
	                throw new DbControlException("There is no attached platform library for platformName: '".$platformName."'.");
	        }
	        return $platform;
		}
		catch (Exception $e) {
			throw $e;
		}
    }

    /**
    * Getter for object to build SQL dynamicaly according currently used database platform
    * @return QueryBuilderInterface
    */
    public function getQueryBuilderPlatform(/*string*/ $task) {
        if (!is_string($task)) {
            throw new DbControlException("Ilegal parameter task. Must be string.");
        }

        switch ($platformName = strtolower($this->getPlatformName($task))) {
            case "mysql":
                $queryBuilder = new QueryBuilderPlatformMysql($task);
                break;
            case "pgsql":
                $queryBuilder = new QueryBuilderPlatformPgsql($task);
                break;
            case "mssql":
                $queryBuilder = new QueryBuilderPlatformMssql($task);
                break;
            case "odbc":
                $queryBuilder = new QueryBuilderPlatformOdbc($task);
                break;
            default:
                throw new DbControlException("There is no attached platform specific query builder for platformName: '".$platformName."'.");
        }
        return $queryBuilder;
    }

    //== protected functions =============================================================

    /**
    * Getter for name of database platform assigned for received task
    * @return String
    */
    protected function getPlatformName(/*string*/ $task) {
        if (!is_string($task)) {
            throw new DbControlException("Ilegal parameter task. Must be string.");
        }
        if (is_string($this->platformName)) {
            return $this->platformName;
        }
        $platformName = $this->getXpathElm("/tasks/".$task."/platform");
        $platformName = (string) current($platformName);
        if (strlen($platformName) < 1) {
            throw new DbControlException("No Platform defined for task: '".$task."'.");
        }
        return $this->platformName = $platformName;
    }

    /**
    * Getter for host address of database platform assigned for received task
    * @return String
    */
    protected function getTaskHost(/*string*/ $task) {
        if (!is_string($task)) {
            throw new DbControlException("Ilegal parameter task. Must be string.");
        }
        $taskHost = $this->getXpathElm("/tasks/".$task."/host");
        $taskHost = (string) current($taskHost);
        if (strlen($taskHost) < 1) {
            throw new DbControlException("No host defined for task: '".$task."'.");
        }
        return $taskHost;
    }

    /**
    * Getter for login name assigned for received task
    * @return String
    */
    private function getTaskName(/*string*/ $task) {
        if (!is_string($task)) {
            throw new DbControlException("Ilegal parameter task. Must be string.");
        }
        $taskName = $this->getXpathElm("/tasks/".$task."/login/name");
        $taskName = (string) current($taskName);
        if (strlen($taskName) < 1) {
            throw new DbControlException("No login name defined for task: '".$task."'.");
        }
        return $taskName;
    }

    /**
    * Getter for login password assigned for received task
    * @return String
    */
    private function getTaskPassword(/*string*/ $task) {
        if (!is_string($task)) {
            throw new DbControlException("Ilegal parameter task. Must be string.");
        }
        $taskPassword = $this->getXpathElm("/tasks/".$task."/login/password");
        $taskPassword = (string) current($taskPassword);
        if (strlen($taskPassword) < 1) {
            throw new DbControlException("No login password defined for task: '".$task."'.");
        }
        return $taskPassword;
    }

    /**
    * Getter for default schema assigned for received task
    * @return String
    */
    public function getTaskSchema(/*string*/ $task) {
        if (!is_string($task)) {
            throw new DbControlException("Ilegal parameter task. Must be string.");
        }
        $taskSchema = $this->getXpathElm("/tasks/".$task."/schema");
        $taskSchema = trim((string) current($taskSchema));
        return $taskSchema;
    }

    /**
    * Getter for DSN of database connection assigned for received task
    * @return String
    */
    private function getTaskDsn(/*string*/ $task) {
        if (!is_string($task)) {
            throw new DbControlException("Ilegal parameter task. Must be string.");
        }
        $taskDsn = $this->getXpathElm("/tasks/".$task."/dsn");
        $taskDsn = (string) current($taskDsn);
        if (strlen($taskDsn) < 1) {
            throw new DbControlException("No ODBC data source name defined for task: '".$task."'.");
        }
        return $taskDsn;
    }

    /**
    * Getter for DSN of database connection assigned for received task
    * @return String
    */
    private function getTaskPort(/*string*/ $task) {
        if (!is_string($task)) {
            throw new DbControlException("Ilegal parameter task. Must be string.");
        }
        $taskPort = $this->getXpathElm("/tasks/".$task."/port");
        $taskPort = (string) current($taskPort);

// port does not must be defined, => default PHP value will be used
//        if (strlen($taskPort) < 1) {
//            throw new DbControlException("No port defined for task: '".$task."'.");
//        }

        return $taskPort;
    }

    /**
    * Getter for content of XPATH element of XML file. Use SimpleXML
    * @return Array
    */
    private function getXpathElm(/*string*/ $xpath) {
        if (strlen($xpath) < 1) {
            throw new DbControlException("Bad parameter XPATH. Must be string.");
        }
		try {
	        $resarray = $this->getCfgH()->get($xpath);
    	    return (array)$resarray;
		}
		catch (Exception $e) {
		  	throw $e;
		}
    }

    /**
    * Getter for Config handler
    * @return DbCfg
    */
    private function getCfgH() {
	  	if (!is_a($this->cfgH, "DbCfg")) {
			$this->cfgH = new DbCfg();
		}
	    return $this->cfgH;
	}
}

?>
