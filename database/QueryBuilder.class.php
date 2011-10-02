<?php

/**
* Class that helps with queries
* @author Michal Palma <palmic at centrum dot cz>
* @copyleft (l) 2005-2006  Michal Palma
* @package DbControl
* @version 1.5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @date 2006-01-11
*/
class QueryBuilder implements QueryBuilderInterface
{

    //== Attributes ======================================================================

    /**
    * current aplication platform
    * @type string
    */
    protected $platform;

    //== constructors ====================================================================

    public function QueryBuilder(/*string*/ $platform = "mysql") {
        if (!is_string($platform)) {
            throw new DbControlException("Ilegal parameter platform. Must be string.");
        }
        $this->platform = strtolower($platform);
    }


    //== destructors ====================================================================
    //== public functions ===============================================================

     public function insert(/*string*/ $table, /*array*/ $values) {
        if (!is_string($table)) {
            throw new DbControlException("Ilegal parameter table. Must be string.");
        }
        if (!is_array($values)) {
            throw new DbControlException("Ilegal parameter values. Must be array.");
        }
        foreach( $values as $index => $value) {
            $colNames[] = "`$index`";
            $colValues[] = "'$value'";
        }
        return "INSERT INTO $table (". $this->arrayToStr($colNames) .") VALUES (". $this->arrayToStr($colValues) .")";
     }


    //== protected functions =============================================================

    /**
    * Make separated values string from array
    * @return string
    */
    protected function arrayToStr(/*array*/ $values, /*string*/ $separator = ", ") {
        if (!is_array($values)) {
            throw new DbControlException("Ilegal parameter values. Must be array.");
        }
        if (!is_string($separator)) {
            throw new DbControlException("Ilegal parameter separator. Must be string.");
        }
        return implode($separator, $values);
    }
}

?>
