<?php

/**
* Class dealing with XML configuration file
* @author Michal Palma <palmic at centrum dot cz>
* @copyleft (l) 2005-2006  Michal Palma
* @package DbControl
* @version 1.5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @date 2006-01-11
*/
class DbCfg
{

    //== Attributes ======================================================================

    /**
    * configuration filename
    * @type string
    */
    private $cfgFilename = "config.xml";

    /**
    * path of configuration file (with filename to)
    * @type string
    */
    private $cfgFilepath;


    /**
    * instance of Xml2Array class
    * @type Xml2Array
    */
    private $xmlH;


    //== constructors ====================================================================
	public function DbCfg($filename = "") {
		if (strlen($filename) < 1) {
			$filename = $this->cfgFilename;
		}
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
        $this->cfgFilepath = $currentDir . $filename;
	}

    //== public functions ================================================================

    /**
    * returns content of XML element specified by XPATH
    * @return array
    */
    public function __get(/*string*/ $xpath)
    {
        try {
			if ($value = $this->getXpathElm($xpath)) {
				return $value;
			}
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    /**
    * an alias of __get() for comfort of use
    * @return array
    */
    public function get($xpath) {
		try {
		  	return $this->__get($xpath);
		}
        catch (Exception $e) {
            throw $e;
        }
	}


    //== protected functions =============================================================

    /**
    * Opening XML file if possible. Using SimpleXML
    * @return void
    */
	protected function open() {
    	if (!is_a($this->xmlH, "SimpleXMLElement")) {
            $this->xmlH = @simplexml_load_file($this->cfgFilepath);
		}
    	if (!is_a($this->xmlH, "SimpleXMLElement")) {
            throw new DbControlException("Cannot open XML file handler of config file!");
		}
	}

    /**
    * Getter for XML file handler
    * @return SimpleXMLElement
    */
    protected function getXmlH() {
		try {
			$this->open();
		}
		catch (Exception $e) {
		  	throw $e;
		}
	  	return $this->xmlH;
	}

    /**
    * Getter for content of XPATH element of XML file.
    * @return Array
    */
    protected function getXpathElm(/*string*/ $xpath) {
        if (strlen($xpath) < 1) {
            throw new DbControlException("Bad parameter XPATH. Must be string.");
        }
        if (!$resarray = $this->getXmlH()->xpath($xpath)) {
			return false;
		}
        return $this->sXmlToArray(current($resarray));
    }

    /**
    * Converts given simpleXML object array into simple array
    * @return Array
    */
    protected function sXmlToArray(SimpleXMLElement $sxmlo)
    {
        $values = ((array) $sxmlo);
        foreach ($values as $index => $value) {
            if (!is_string($value)) {
                $values[$index] = $this->sXmlToArray($value);
            }
            else {
                $values[$index] = $value;
            }
        }
        return $values;
    }
}

?>
