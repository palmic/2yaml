<?
/**
* Special Exception of DbControl.
* @author Michal Palma <palmic at centrum dot cz>
* @copyleft (l) 2005-2006  Michal Palma
* @package DbControl
* @version 1.5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @date 2005-01-11
*/
class DbControlException extends exception
{
    public function __construct($message = NULL, $code = 0) {
        parent::__construct($message, $code);
        DbLog::logError($this);
    }
}


?>
