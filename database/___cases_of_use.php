<?php
/*
* Some cases of use (for your use you must reconfigure Connection Attributes in DbSelector class - read readmeFirst.txt)
* @package DbControl
* @date 2006-01-11
*/

require("_preload.php");

$task = "test";
$query_select = "select * from your_db_table_name";
$database_name = "your_db_schema_name";

try {
    $dbC = new DbControl($task, "cp1250");
    $dbC->selectDb($database_name);
    $dbC->setQuery($query_select);
    $dbR = $dbC->initiate();
    echo "We found ". $dbR->getNumRows() ." rows in database result.";
    echo "<table border='1'>";
    echo "<tr>";
    foreach ($dbR->getColnames() as $colname) {
        echo  "<th>". $colname ."</th>";
    }
    echo "</tr>";
    while ($dbR->next()) {
        echo "<tr>";
        foreach($dbR->get("*") as $colname => $col) {
        # you can use direct columnname ($dbR->colName)
            echo "<td>$col</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
catch (Exception $e) {
    echo "<hr />";
    echo "Exception code:  <font style='color:blue'>". $e->getCode() ."</font>";
    echo "<br />";
    echo "Exception message: <font style='color:blue'>". nl2br($e->getMessage()) ."</font>";
    echo "<br />";
    echo "Thrown by: '". $e->getFile() ."'";
    echo "<br />";
    echo "on line: '". $e->getLine() ."'.";
    echo "<br />";
    echo "<br />";
    echo "Stack trace:";
    echo "<br />";
    echo nl2br($e->getTraceAsString());
    echo "<hr />";
}

/*
try {
    $query = "INSERT INTO user VALUES('', 'palmic', 'heslo', 'user', 0)";
    $dbC = new DbControl($task);
    $dbC->selectDb("wwera");
    $dbC->transactionStart();
    $dbC->setQuery($query);
    $dbC->initiate();
    echo "Id of last inserted record is '". $dbC->getLastId() ."'";
    $dbC->transactionCommit();
}
catch (Exception $e) {
    echo "<hr />";
    echo "Exception code:  <font style='color:blue'>". $e->getCode() ."</font>";
    echo "<br />";
    echo "Exception message: <font style='color:blue'>". nl2br($e->getMessage()) ."</font>";
    echo "<br />";
    echo "Thrown by: '". $e->getFile() ."'";
    echo "<br />";
    echo "on line: '". $e->getLine() ."'.";
    echo "<br />";
    echo "<br />";
    echo "Stack trace:";
    echo "<br />";
    echo nl2br($e->getTraceAsString());
    echo "<hr />";
}
*/
/*
try {
    $dbC = new DbControl($task);
    $dbC->selectDb("wwera");
    $dbC->setQuery("delete from user where id > 1");
    $dbC->transactionStart($autoCommit = true);
    $dbC->initiate();
    $dbC->setQuery("select * from user");
    $dbR = $dbC->initiate();

    echo "<table border='1'>";
    while ($dbR->next()) {
        echo "<tr>";
        foreach($dbR->get("*") as $colname => $col) {
            echo "<td>$colname = $col</td>";
        }
        echo "</tr>";
    }
    echo "</table>";

}
catch (Exception $e) {
    echo "<hr />";
    echo "Exception code:  <font style='color:blue'>". $e->getCode() ."</font>";
    echo "<br />";
    echo "Exception message: <font style='color:blue'>". nl2br($e->getMessage()) ."</font>";
    echo "<br />";
    echo "Thrown by: '". $e->getFile() ."'";
    echo "<br />";
    echo "on line: '". $e->getLine() ."'.";
    echo "<br />";
    echo "<br />";
    echo "Stack trace:";
    echo "<br />";
    echo nl2br($e->getTraceAsString());
    echo "<hr />";
}
*/
?>
