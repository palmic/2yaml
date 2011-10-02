<?php

/**
* Special Exception of DbControl.
* @author Michal Palma <palmic at email dot cz>

* @package DbControl
* @version 1.5

* @date 2005-01-11
*/
class DbControlException extends LBoxException
{
	protected $logVerbose = false;
	
    public function __construct($message = NULL, $code = 0) {
        parent::__construct($message, $code);
        // logging is switched off here
        //        DbLog::logError($this);
    }
}
?>