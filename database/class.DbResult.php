<?php


/**
* Class to managing DB result
* @author Michal Palma <palmic at email dot cz>

* @package DbControl
* @version 1.5

* @date 2006-01-11
*/
class DbResult implements DbResultInterface
{


    //== Attributes ======================================================================

    /**
    * specific database result resource
    * @var resource
    */
    protected $result;

    /**
    * Handler of shared attributes
    * @var DbStateHandler
    */
    protected $dbStateHandler;

    /**
    * Count of fetched result rows
    * @var Integer
    */
    protected $indexCount = 0;

    /**
    * Current cursor position in fetched result rows
    * @var Integer
    */
    protected $currentIndex = 0;

    /**
    * Cache of loaded result rows
    * @var array
    */
    protected $resultCache = array();

    /**
    * Flag of end of result for rows preload
    * @var Boolean
    */
    protected $done = false;


    //== constructors ====================================================================

    public function DbResult(/*resource*/ $result, DbStateHandler $dbStateHandler) {
		if (!is_resource($result)) {
		  throw new DbControlException("Invalid argument, must be a valid database resource!");
		}
        $this->dbStateHandler = $dbStateHandler;
        $this->result = $result;
    }


    //== destructors ====================================================================
    //== public functions ================================================================

    public function __get(/*string*/ $columnName = "*") {
        if (!is_string($columnName)) {
            throw new DbControlException("Ilegal parameter. Must be string.");
        }
        # measure for case of calling first() => __get()
        if ($this->currentIndex < 1) {
            $index = 1;
        }
        # measure for case of calling last() => __get()
        else if ($this->currentIndex > count($this->resultCache)) {
            $index = count($this->resultCache);
        }
        else {
            $index = $this->currentIndex;
        }
        if (array_key_exists($index, $this->resultCache))
        if (!is_array($this->resultCache[$index])) {
            $this->readOne();
        }
        if ($columnName == "*") {
            return $this->resultCache[$index];
        }
        else {
        	return array_key_exists($index, $this->resultCache) ?
        			$this->resultCache[$index][$columnName] :
        				NULL;
        }
    }

    public function get($columnName = "*") {
        return $this->__get($columnName);
    }

    public function first() {
        if ($this->indexCount < 1) {
            $this->readOne();
        }
        # measure for case of calling first() => while(next())
        $this->currentIndex = 0;
    }

    public function last() {
        $this->readRemain();
        # measure for case of calling last() => while(previous())
        $this->currentIndex = count($this->resultCache) + 1;
    }

    public function previous() {
        $this->currentIndex--;
        if ($this->currentIndex < 1) {
            return false;
        }
        return true;
    }

    public function next() {
        if (!is_array($this->resultCache[$this->currentIndex + 1]) ) {
            $this->readOne();
            if ($this->done) {
                return false;
            }
        }
        $this->currentIndex++;
        if ($this->currentIndex > count($this->resultCache)) {
            return false;
        }
        return true;
    }

    public function getColnames() {
        try {
            return $this->dbStateHandler->getDbPlatform()->getColnames($this->result);
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function getNumRows() {
        return $this->dbStateHandler->getDbPlatform()->getNumRows($this->result);
    }


    //== protected functions =============================================================

    /**
    * fetch one row from database to local result array
    * @return void
    */
    protected function readOne() {
        if ($this->done) {
            return;
        }
        if (!$row = $this->dbStateHandler->getDbPlatform()->fetchAssoc($this->result)) {
            $this->done = true;
            return;
        }
        $this->resultCache[$this->indexCount + 1] = $row;
        $this->indexCount++;
    }

    /**
    * fetch remain rows from database to local result array
    * @return void
    */
    protected function readRemain() {
        while (!$this->done) {
            $this->readOne();
        }
    }
}

?>
