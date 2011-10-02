<?php

/**
* Interface of class that helps with queries
* @author Michal Palma <palmic at centrum dot cz>
* @copyleft (l) 2005-2006  Michal Palma
* @package DbControl
* @version 1.5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @date 2006-01-11
*/
interface QueryBuilderInterface
{

    //== constructors ====================================================================

    public function QueryBuilder(/*string*/ $platform = "mysql");


    //== public functions ===============================================================

    /**
    * Make insert query from values array indexed by colnames
    * @return string
    */
     public function insert(/*string*/ $table, /*array*/ $values);
}

?>
