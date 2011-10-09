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
	 * @param DbResultInterface $result
	 * @param string $modelName
	 * @param string $outputFilePath
	 * @return void
	 */
	public static function export(DbResultInterface $result, $modelName = '-', $outputFilePath = '') {
		if (!class_exists('sfYaml')) {
			throw new Exception('We need to have Symfony yaml component installed: http://components.symfony-project.org/yaml/installation');
		}
		$data = array();
		while ($result->next()) {
			$data[$modelName]['-'] = $result->get('*');
		}
		$data = static::convertData($data);
		$fp = fopen($outputFilePath, 'w');
		$dumper = new sfYamlDumper();
		$yaml = $dumper->dump($data, 3);
		file_put_contents($outputFilePath, $yaml);
	}

	/**
	 * @param array $data
	 * @return array
	 */
	protected static function convertData($data) {
		$out = array();
		foreach ($data as $modelName => $modelData) {
			foreach ($modelData as $recordName => $recordData) {
				$recordName = self::cleanName($recordName);
				foreach ($recordData as $columnName => $columnValue) {
					$columnName = self::cleanName($columnName);
					$out[$modelName][$recordName][$columnName] = $columnValue;
				}
			}
		}
		return $out;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected static function cleanName($name) {
		$name = strtr($name, array(
								 "á" => "a", "č" => "c", "ď" => "d", "é" => "e", "ě" => "e", "í" => "i", "ň" => "n", "ó" => "o", "ř" => "r", "š" => "s", "ť" => "t", "ú" => "u", "ů" => "u", "ý" => "y", "ž" => "z",
								 "Á" => "a", "Č" => "c", "Ď" => "d", "É" => "e", "Ě" => "e", "Í" => "i", "Ň" => "n", "Ó" => "o", "Ř" => "r", "Š" => "s", "Ť" => "t", "Ú" => "u", "Ů" => "u", "Ý" => "y", "Ž" => "z",
								 "." => "-", "," => "-", ";" => "-", ":" => "-", "&" => "and", "_" => "-", "@" => "", " " => "-",
							 ));
		return preg_replace('/([^a-zA-Z0-9-])/', '_', $name);
	}
}
