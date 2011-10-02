<?php

/**
* logging class for DbControl
* @author Michal Palma <palmic at email dot cz>

* @package DbControl
* @version 1.5

* @date 2006-01-11
*/
class DbLog
{

    /**
    * logfile handler
    * @var resource
    */
    protected static $handler;

    /**
    * logfile name
    * @var string
    */
    protected static $filename = "DbControl.log";


    //== public functions ================================================================

    /**
    * write log into logfile
    * @return void
    */
    public static function log(/*string*/ $message) {
        try {
            if (!is_string($message)) {
                throw new Exception("Invalid parameter message, must be string.");
            }
            $time = date("y-m-d H:i:s");
            $script = $_SERVER['PHP_SELF'];
            $log  = "\n";
            $log .= $time ."\n". $message ."\n";
            $log .= "Client script: ". str_replace ( "\\", "/", $script) ."\n";
            $log .= "#########################################################################################################\n";
            $log .= "\n";
            if (fwrite(self::getHandler(), $log) < 0) {
                throw new Exception("Cannot write into logfile.");
            }
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    /**
    * write Error log into logfile
    * @return void
    */
    public static function logError(DbControlException $e) {
        try {
            $message  = " ERROR!\n";
            $message .= "Exception code: ". $e->getCode() ."\n";
            $message .= "Exception message: ". $e->getMessage() ."\n";
            $message .= "Thrown by: '". $e->getFile() ."'";
            $message .= " on line: ". $e->getLine() ."\n";
            $message .= "Stack trace: ". $e->getTraceAsString() ."\n";
            self::log($message);
        }
        catch (Exception $e) {
			// DbControlException cant be thrown here
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }



    //== protected functions ================================================================

    /**
    * write log into logfile
    * @return void
    */
    protected static function getHandler() {
        if (is_resource(self::$handler)) {
            return self::$handler;
        }
        else {
            self::$handler = fopen(self::getFilename(), "a");
        }
        if (!is_resource(self::$handler)) {
            throw new Exception("Cannot open logfile.");
        }
        return self::$handler;
    }

    /**
    * getter for a filename
    * @return void
    */
    public static function getFilename() {
        $currentFileFull = str_replace("\\", "/", __FILE__);
        # An standard function dirname() is deranged by filenames with more than one doth.
        $currentFile = $currentFileFull;
        if (substr($currentFile, 0, 1) == "/") {
	        $currentFile = substr($currentFile, 1);
   	    }
        while($pos = strpos($currentFile, "/")) {
            $currentFile = substr($currentFile, $pos + 1);
        }
        $currentDir = substr($currentFileFull, 0, strpos($currentFileFull, $currentFile));
        return  $currentDir . self::$filename;
	}
}

?>
