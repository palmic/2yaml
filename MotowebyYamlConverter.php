<?php
/**
 * Created by JetBrains PhpStorm.
 * User
 * Date
 * Time
 * To change this template use File | Settings | File Templates.
 */

class MotowebyYamlConverter extends YamlConverter {

	protected static $columnsTranslate = array(
		'název_motorky' => 'name',
		'cena' => 'price',
		'typ_nabídky' => 'selltype',
		'kategorie_moto' => 'category',
		'krátký_popis' => 'description_short',
		'podrobný_popis' => 'description',
		'zdvihový_objem' => 'objem',
		'vrtání/zdvih' => 'zdvih',
		'kompresní_poměr' => 'komprese',
		'karburátor(y)_/_vstřikování' => 'karburator',
		'světlá_výška_bez_zatížení' => 'svetla_vyska_bez_zateze',
		'výška_sedadla_bez_zátěže' => 'vyska_sedadla_bez_zateze',
		'objem_palivové_nádrže' => 'objem_nadrze',
		'hmotnost_bez_paliva' => 'hmotnost',
		'zadní_pneumatika' => 'pneu_zadek',
		'přední_pneumatika' => 'pneu_predek',
	);

	protected static $columnsIgnore = array(
		'cena_půjčovného_na_den',
		'cena_půjčovného_na_víkend',
		'cena_půjčovného_na_týden',
		'hlavní_fotka',
		'foto1',
		'foto2',
		'foto3',
		'foto4',
	);

	public static function export(DbResultInterface $result, $modelName = '', $outputFilePath = '') {
		$data = array();
		while ($result->next()) {
			$data[$modelName][static::cleanRecordName($result->get('název_motorky'))] = $result->get('*');
		}
		static::exportArray($data, $outputFilePath);
	}

	protected static function cleanValue($name, $value) {
		$value = trim(preg_replace('/(cm³)|(ccm)|(ccm)|(kč)/i', '', $value));
		switch ($name) {
			case 'price':
					return (int)preg_replace('/[,\-\.,]/', '', $value);
				break;
			case 'category':
					return static::cleanRecordName($value);
				break;
			case 'selltype':
					return static::cleanRecordName(static::filterSellType($value));
				break;
			case 'svetla_vyska_bez_zateze':
			case 'vyska_sedadla_bez_zateze':
			case 'hmotnost':
			case 'zdvih':
			case 'zdvih_pruziny_vpredu':
			case 'zdvih_pruziny_vzadu':
					return trim(preg_replace('/(mm)|(kg)/i', '', $value));
				break;
			default:
				return $value;
		}
	}

	/**
	 * osetruje pripadny chybejici selltype
	 * @param string $sellType
	 * @return string
	 */
	public static function filterSellType($sellType) {
		switch ($sellType) {
			case 'Na objednávku':
					return 'Objednávka';
				break;
			case 'Skladem':
					return 'Sklad';
				break;
			default:
					return strlen(trim($sellType)) > 0 ? $sellType : 'Neupřesněno';
		}
	}
}
