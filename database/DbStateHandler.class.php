<?php


/**
* Messenger of shared collective attributes
* @author Michal Palma <palmic at centrum dot cz>
* @copyleft (l) 2005-2006  Michal Palma
* @package DbControl
* @version 1.5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @date 2006-01-11
*/
class DbStateHandler
{


    //== Attributes ======================================================================

    /**
    * Charset of database connection
    * @type string
    */
    protected $charset;

    /**
    * object of class dealing with concrete database
    * @type DbPlatform
    */
    protected $dbPlatform;

    /**
    * current aplication task on database work (for example web_forum...). Something like unique id of task
    * @type string
    */
    protected $task;


    //== constructors ====================================================================

    public function DbStateHandler(/*string*/ $task, /*string*/ $charset) {
        if (!is_string($task)) {
            throw new DbControlException("Ilegal parameter task. Must be string.");
        }
        if (!is_string($charset)) {
            throw new DbControlException("Ilegal parameter charset. Must be string.");
        }
        $this->task = $task;
        $this->charset = $charset;
    }


    //== destructors =====================================================================
    //== public functions ================================================================

    /**
    * getter of attribute charset
    * @return String
    */
    public function getCharset() {
        return $this->charset;
    }

    /**
    * getter of attribute dbPlatform
    * @return DbPlatform
    */
    public function getDbPlatform() {
        if (!is_a($this->dbPlatform, "DbPlatform")) {
            try {
                $dbSelector = new DbSelector();
                $this->setDbPlatform($dbSelector->getPlatform($this->getTask(), $this));
            }
            catch(DbControlException $e) {
                throw $e;
            }
        }
        return $this->dbPlatform;
    }

    /**
    * getter of copy of self instance
    * @return DbStateHandler
    */
    public function getCopy() {
        $copy = new DbStateHandler($this->task, $this->charset);
        $copy->setDbPlatform($this->dbPlatform);
        return $copy;
    }

    /**
    * getter for attribute task
    * @return string
    */
    public function getTask() {
        return $this->task;
    }


    //== protected functions =============================================================

    /**
    * setter of attribute dbPlatform
    * @return void
    */
    public function setDbPlatform(DbPlatform $dbPlatform) {
        $this->dbPlatform = $dbPlatform;
    }
}

?>
