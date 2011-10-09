<?php
require('_preload.php');
require('MotowebyYamlConverter.php');
set_time_limit(99999);

$task = 'export2yaml';

$databaseName = 'csv2db';
$tableName = 'ktm';

try {
    $dbC = new DbControl($task, 'utf8');
    $dbC->selectDb($databaseName);
    $dbC->setQuery(sprintf('select * from `%s`', $tableName));

	MotowebyYamlConverter::export($dbC->initiate(), 'ShopMotorcycle', sprintf('%s/%s', dirname(__FILE__), 'ktm.yml'));
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
