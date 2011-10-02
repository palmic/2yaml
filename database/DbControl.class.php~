<?php

/**
* class to dealing with database resources independable on db platform
* @author Michal Palma <palmic at email dot cz>

* @package DbControl
* @version 1.5

* @date 2006-01-11
*/
class DbControl implements DbControlInterface
{

    //== Attributes ======================================================================

	/**
	 * debug verbose
	 * @var bool
	 */
    public static $debug = false;
	
    /**
    * database query to initiate
    * @var string
    */
    protected $query;

    /**
    * Task identifier (config.xml settings ID)
    * @var string
    */
    protected $task;

    /**
    * Handler of shared attributes
    * @var DbStateHandler
    */
    protected $dbStateHandler;

    /**
    * Log on flag
    * @var boolean
    */
    protected $logOn;

    /**
    * Log handling object
    * @var DbLog
    */
    protected $DbLogH;
    
    /**
    * Config handling object
    * @var DbCfg
    */
    protected $DbCfg;
    
    /**
    * No result-cache flag
    * @var boolean
    */
    protected $noCache;

    /**
    * count of initiated queries
    * @var integer
    */
    protected static $queryCount = 0;

    //== constructors ====================================================================

    public function DbControl(/*String*/ $task, $charset = "utf8") {
        if (!is_string($task)) {
            throw new DbControlException("Ilegal parameter task. Must be string.");
        }
        $this->task = $task;
        try {
            $this->dbStateHandler = new DbStateHandler($task, $charset);
        }
        catch (Exception $e) {
            throw $e;
        }
    }


    //== destructors ====================================================================
    //== public functions ===============================================================

    public function setQuery(/*string*/ $query, /*boolean*/ $noCache = true) {
        if (!is_string($query)) {
            throw new DbControlException("Ilegal parameter query. Must be string.");
        }
        if (!is_bool($noCache)) {
            throw new DbControlException("Ilegal parameter noCache. Must be boolean.");
        }
        $this->noCache  = $noCache;
        $this->query    = $query;
    }

    public function initiate() {
        if (!is_string($this->getQuery())) {
            throw new DbControlException("No query to initiate!");
        }
        $query = $this->getQuery();
        if (func_num_args() > 0) {
            $arguments = func_get_args();
            for ($i = 1; $i <= count($arguments); $i++) {
                try {
                    if (!strstr($query, "<$i>")) {
                        throw new DbControlException("Number of replacement elements in query does not corresponds the number of parametres in initiate.");
                    }
                    $query = str_ireplace("<$i>", $arguments[$i - 1], $query);
                }
                catch (Exception $e) {
                    throw new DbControlException($e->getMessage() ."\nCheck the count of initiate parameters.");
                }
            }
        }
        if (eregi("<[[:digit:]]+>", $query)) {
            throw new DbControlException("Number of replacement elements does not corresponds the number of parametres in initiate. \n". $query);
        }
        try {
			self::$queryCount++;
/*if (stristr($query, "`lft` FROM `photogalleriesImages")) {
	throw new LBoxException($query);
}*/
			$this->debug($query);
			$result = $this->dbStateHandler->getDbPlatform()->query($query);
			if ($this->isLogOn()) {
				$this->log("Query succesfully done.\n". $query);
			}
            if ($result) {
                if (is_resource($result)) {
                    $resultStateHandler = $this->dbStateHandler->getCopy();
                    if ($this->noCache) {
                        return new DbResultNoCache($result, $resultStateHandler);
                    }
                    return new DbResult($result, $resultStateHandler);
                }
                else {
                    return $result;
                }
            }
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function initiateQuery(/*string*/ $query, /*boolean*/ $noCache = true) {
        try {
            $this->setQuery($query, $noCache);
            return $this->initiate();
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function selectDb($dbName) {
        try {
        	$this->dbStateHandler->getDbPlatform()->selectDb($dbName);
			if ($this->isLogOn()) {
				$this->log("Database ". $dbName ." succesfully choosed.");
			}
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function getLastId() {
        return $this->dbStateHandler->getDbPlatform()->getLastId();
    }

    public function transactionStart($autoCommit = false) {
        try {
            $this->dbStateHandler->getDbPlatform()->transactionStart($autoCommit);
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function transactionCommit() {
        try {
            $this->dbStateHandler->getDbPlatform()->transactionCommit();
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function transactionRollback() {
        try {
            $this->dbStateHandler->getDbPlatform()->transactionRollback();
        }
        catch (Exception $e) {
            throw $e;
        }
    }


    //== protected functions =============================================================

    /**
    * getter for query
    * @return string
    */
    protected function getQuery() {
        return $this->query;
    }

    /**
    * Log on?
    * @return boolean
    */
    protected function isLogOn() {
		if (is_bool($this->logOn)) {
		  	return $this->logOn;
		}
		$logOnCfg = $this->getDbCfgH()->get($this->dbStateHandler->getTask() ."/logon");
		if ($logOnCfg) {
			if (current($logOnCfg) > 0) {
			  	$this->logOn = true;
			}
			else {
			  	$this->logOn = false;
			}
		}
		return $this->logOn;
	}

	/**
	 * @return DbCfg
	 */
	protected function getDbCfgH() {
		if ($this->DbCfg instanceof DbCfg) {
			return $this->DbCfg;
		}
		return $this->DbCfg = new DbCfg;
	}
	
    /**
    * getter for DbLog object
    * @return DbLog
    */
    protected function getDbLogH() {
	  	if ($this->DbLogH instanceof DbLog) {
		    return $this->DbLogH;
		}
		$this->DbLogH = new DbLog;
		return $this->DbLogH;
	}

    /**
    * do log into a log file
    * @return void
    */
    protected function log(/*String*/ $log) {
        if (!is_string($log)) {
            throw new DbControlException("Ilegal parameter log. Must be string.");
        }
        $add = "Current task ID: ". $this->task ."\n";
		$this->getDbLogH()->log($log ."\n". $add);
	}
	
	/**
	* returns number of queries already called
	* @return integer
	*/
	public static function getQueryCount() {
		return self::$queryCount;
	}

	protected function debug($sql) {
		if (!self::$debug) return;
		if (strtolower(self::$debug) == "firephp") {
			switch (true) {
				case is_numeric(strpos($sql, "UPDATE")):
				case is_numeric(strpos($sql, "DELETE")):
						LBoxFirePHP::warn(round(LBoxTimer::getInstance()->getTimeOfLife(), 5) ." - ". str_replace("\n", "", self::$queryCount .": ". $sql));
					break;
				default:
						LBoxFirePHP::log(round(LBoxTimer::getInstance()->getTimeOfLife(), 5) ." - ". str_replace("\n", "", self::$queryCount .": ". $sql));
			}
		}
		else {
			switch (true) {
				case is_numeric(strpos($sql, "UPDATE")):
				case is_numeric(strpos($sql, "DELETE")):
						$bg	= "#962C2C";
					break;
				case is_numeric(strpos($sql, "INSERT")):
						$bg	= "#6BC764";
					break;
				default:
						$bg	= "#5D5D5D";
			}
			$color	= "#ffffff";
			$msg 	= "<table><th bgcolor='$bg' align='left'><font color='#C0C0C0'>". round(LBoxTimer::getInstance()->getTimeOfLife(), 5) ."</font></th><th bgcolor='$bg' align='left'><b><font color='$color'>". nl2br(self::$queryCount .": ". $sql) ."</font></b></th></table>\n";
			echo $msg;
			flush();
			}
		}
}

?>