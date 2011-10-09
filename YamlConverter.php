<?php
/**
 * Created by JetBrains PhpStorm.
 * User: palmic
 * Date: 9.10.11
 * Time: 13:02
 * To change this template use File | Settings | File Templates.
 */

class YamlConverter {

	/**
	 * @var array
	 */
	protected static $columnsTranslate = array();

	/**
	 * @var array
	 */
	protected static $columnsIgnore = array();

	/**
	 * @param DbResultInterface $result
	 * @param string $modelName
	 * @param string $outputFilePath
	 * @return void
	 */
	public static function export(DbResultInterface $result, $modelName = '', $outputFilePath = '') {
		$data = array();
		while ($result->next()) {
			$data[$modelName][] = $result->get('*');
		}
		static::exportArray($data, $outputFilePath);
	}

	/**
	 * @static
	 * @param array $data
	 * @param string $outputFilePath
	 * @return void
	 */
	public static function exportArray($data, $outputFilePath) {
		if (!class_exists('sfYaml')) {
			throw new Exception('We need to have Symfony yaml component installed: http://components.symfony-project.org/yaml/installation');
		}
		file_put_contents($outputFilePath, sfYaml::dump(static::convertData($data), 4));
	}

	/**
	 * @param array $data
	 * @return array
	 */
	protected static function convertData($data) {
		$out = array();
		foreach ($data as $modelName => $modelData) {
			foreach ($modelData as $recordName => $recordData) {
				$recordName = static::cleanRecordName($recordName);
				foreach ($recordData as $columnName => $columnValue) {
					if (is_numeric(array_search($columnName, static::$columnsIgnore))) {
						continue;
					}
					$columnName = array_key_exists($columnName, static::$columnsTranslate) ? static::$columnsTranslate[$columnName] : $columnName;
					$columnName = static::cleanColumnName($columnName);
					$out[$modelName][$recordName][$columnName] = static::cleanValue($columnName, $columnValue);
				}
			}
		}
		return $out;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public static function cleanRecordName($name) {
		return preg_replace('/(record_)+/', 'record_', sprintf('record_%s', strtolower(preg_replace('/\-/', '_', self::cleanBasic($name)))));
	}

	/**
	 * @static
	 * @param string $name columnName after conversion (if any)
	 * @param string $value
	 * @return string
	 */
	protected static function cleanValue($name, $value) {
		return $value;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	private static function cleanColumnName($name) {
		return preg_replace('/\-/', '_', self::cleanBasic($name));
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected static function cleanBasic($name) {
		$name = strtr($name, array(
								 "á" => "a", "č" => "c", "ď" => "d", "é" => "e", "ě" => "e", "í" => "i", "ň" => "n", "ó" => "o", "ř" => "r", "š" => "s", "ť" => "t", "ú" => "u", "ů" => "u", "ý" => "y", "ž" => "z",
								 "Á" => "a", "Č" => "c", "Ď" => "d", "É" => "e", "Ě" => "e", "Í" => "i", "Ň" => "n", "Ó" => "o", "Ř" => "r", "Š" => "s", "Ť" => "t", "Ú" => "u", "Ů" => "u", "Ý" => "y", "Ž" => "z",
								 "." => "-", "," => "-", ";" => "-", ":" => "-", "&" => "and", "_" => "-", "@" => "", " " => "-",
							 ));
		return preg_replace('/([^a-zA-Z0-9-])/', '_', $name);
	}
}
