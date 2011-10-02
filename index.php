<?php
require('_preload.php');
set_time_limit(99999);

$task = 'export2yaml';
$database_name = 'csv2db';
$query_select = 'select * from `Yamaha_-_Martin`';

try {
    $dbC = new DbControl($task, 'utf8');
    $dbC->selectDb($database_name);
    $dbC->setQuery($query_select);
    $dbR = $dbC->initiate();
    echo 'We found '. $dbR->getNumRows() .' rows in database result.';
    echo '<table border="1">';
    echo '<tr>';
    foreach ($dbR->getColnames() as $colname) {
        echo  '<th>'. $colname .'</th>';
    }
    echo '</tr>';
    while ($dbR->next()) {
        echo '<tr>';
        foreach($dbR->get('*') as $colname => $col) {
        # you can use direct columnname ($dbR->colName)
            echo sprintf('<td>%s</td>', $col);
        }
        echo '</tr>';
    }
    echo '</table>';
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
