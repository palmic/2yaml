<?php
require('_preload.php');
require('MotowebyYamlConverter.php');
set_time_limit(99999);

$task = 'export2yaml';

$databaseName = 'csv2db';
$tableNames = array('ktm', 'suzuki', 'yamaha');

try {
    $dbC = new DbControl($task, 'utf8');
    $dbC->selectDb($databaseName);

	$photosBounds = array();

	$categories = array();
	$types = array();

	foreach ($tableNames as $tableName) {
		$q = sprintf('select *, \'%s\' as vendor from `%s`', $tableName, $tableName);
		$result = $dbC->initiateQuery($q);

		$photos = array();

		$photos = array();
		while($result->next()) {

			$name = $result->get('název_motorky');
			$photosBounds[$tableName][$name] = array();

			$categories[MotowebyYamlConverter::cleanRecordName($result->get('kategorie_moto'))] = array(
				'name' => $result->get('kategorie_moto'),
				'limitProducts' => 'motorcycles',
			);
			$types[MotowebyYamlConverter::cleanRecordName(MotowebyYamlConverter::filterSellType($result->get('typ_nabídky')))] = array(
				'name' => MotowebyYamlConverter::filterSellType($result->get('typ_nabídky')),
				'global' => true,
			);

			if (strlen(trim($result->get('hlavní_fotka'))) > 0) {
				$photos = array_merge($photos, getDataPhoto($result->get('hlavní_fotka'), $tableName));
				$photosBounds[$tableName][$name][] = MotowebyYamlConverter::cleanRecordName($result->get('hlavní_fotka'));
			}
			if (strlen(trim($result->get('foto1'))) > 0) {
				$photos = array_merge($photos, getDataPhoto($result->get('foto1'), $tableName));
				$photosBounds[$tableName][$name][] = MotowebyYamlConverter::cleanRecordName($result->get('foto1'));
			}
			if (strlen(trim($result->get('foto2'))) > 0) {
				$photos = array_merge($photos, getDataPhoto($result->get('foto2'), $tableName));
				$photosBounds[$tableName][$name][] = MotowebyYamlConverter::cleanRecordName($result->get('foto2'));
			}
			if (strlen(trim($result->get('foto3'))) > 0) {
				$photos = array_merge($photos, getDataPhoto($result->get('foto3'), $tableName));
				$photosBounds[$tableName][$name][] = MotowebyYamlConverter::cleanRecordName($result->get('foto3'));
			}
			if (strlen(trim($result->get('foto4'))) > 0) {
				$photos = array_merge($photos, getDataPhoto($result->get('foto4'), $tableName));
				$photosBounds[$tableName][$name][] = MotowebyYamlConverter::cleanRecordName($result->get('foto4'));
			}
		}
		// generate photos yaml
		MotowebyYamlConverter::exportArray(array('Photo' => $photos), sprintf('%s/%s', dirname(__FILE__), sprintf('import_photos_%s.yml', $tableName)));

		// export products yaml
		MotowebyYamlConverter::export($dbC->initiateQuery($q), 'ShopMotorcycle', sprintf('%s/%s', dirname(__FILE__), sprintf('import_shop_motorcycle_%s.yml', $tableName)));
	}

	// generate categories yaml
	MotowebyYamlConverter::exportArray(array('ShopItemCategory' => $categories), sprintf('%s/%s', dirname(__FILE__), sprintf('import_shop_item_category.yml', $tableName)));

	// generate sellTypes yaml
	MotowebyYamlConverter::exportArray(array('ShopSellType' => $types), sprintf('%s/%s', dirname(__FILE__), sprintf('import_shop_sell_types.yml', $tableName)));

	// generate photos bounds
	foreach ($photosBounds as $tableName => $photosBoundsTable) {
		$photosBoundsExport = array();
		foreach ($photosBoundsTable as $recordName => $photosRecordsNames) {
			foreach ($photosRecordsNames as $photoRecordName) {
				$k = sprintf('%s_%d', $tableName, count($photosBoundsExport));
				$photosBoundsExport[$k] = array(
					'photo' => MotowebyYamlConverter::cleanRecordName($photoRecordName),
					'shopMotorcycle' => MotowebyYamlConverter::cleanRecordName($recordName),
				);
			}
		}
		MotowebyYamlConverter::exportArray(array('PhotoShopMotorcycle' => $photosBoundsExport), sprintf('%s/%s', dirname(__FILE__), sprintf('import_photo_shop_motorcycle_%s.yml', $tableName)));
	}
}
catch (Exception $e) {
    echo '<hr />';
    echo 'Exception code:  <font style="color:blue">'. $e->getCode() .'</font>';
    echo '<br />';
    echo 'Exception message: <font style="color:blue">'. nl2br($e->getMessage()) .'</font>';
    echo '<br />';
    echo 'Thrown by: '. $e->getFile() .'';
    echo '<br />';
    echo 'on line: '. $e->getLine() .'.';
    echo '<br />';
    echo '<br />';
    echo 'Stack trace:';
    echo '<br />';
    echo nl2br($e->getTraceAsString());
    echo '<hr />';
}

/**
 * @param string $photoName
 * @param string $tableName
 * @return array
 */
function getDataPhoto($photoName, $tableName) {
	$photoNameClean = MotowebyYamlConverter::cleanRecordName($photoName);
	return array(
		$photoNameClean => array(
			'dirname' => sprintf('import_%s', $tableName),
			'filename' => sprintf('%s.jpg', $photoName),
		),
	);
}
