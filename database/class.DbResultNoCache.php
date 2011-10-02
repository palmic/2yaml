<?php


/**
* Class to managing DB result whithout caching results. Essential for handling big data amount.
* @author Michal Palma <palmic at email dot cz>

* @package DbControl
* @version 1.5

* @date 2006-05-01
*/
class DbResultNoCache extends DbResult
{


    //== Attributes ======================================================================

    /**
    * Cache of loaded result rows
    * @var array
    */
    protected $resultCache = array();


    //== constructors ====================================================================

    public function DbResult(/*resource*/ $result, DbStateHandler $dbStateHandler) {
        try {
            parent::__construct($result, $dbStateHandler);
        }
        catch (Exception $e) {
            throw $e;
        }
    }


    //== destructors ====================================================================
    //== public functions ================================================================

    public function __get(/*string*/ $columnName = "*") {
        if (!is_string($columnName)) {
            throw new DbControlException("Ilegal parameter. Must be string.");
        }
        if (empty($this->resultCache)) {
            $this->next();
        }
        if ($columnName == "*") {
            return $this->resultCache;
        }
        else {
            return $this->resultCache[$columnName];
        }
    }

    public function get($columnName = "*") {
        return $this->__get($columnName);
    }

    public function first() {
        throw new DbControlException("Cannot seek 'last' via result whithout cache. Fetch result by next()!");
    }

    public function last() {
        throw new DbControlException("Cannot seek 'last' via result whithout cache. Fetch result by next()!");
    }

    public function previous() {
        throw new DbControlException("Cannot seek 'previous' via result whithout cache. Fetch result by next()!");
    }

    public function next() {
        if (!$this->resultCache = $this->dbStateHandler->getDbPlatform()->fetchAssoc($this->result)) {
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
}

?>