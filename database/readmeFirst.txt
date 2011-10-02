/*
* SQL databases interface DbControl version 1.5
* @package DbControl
* @date 2006-01-11
*/


*Advantages:
- You can change database platform for your application at the config Level (whithout any change in intarface of classes and so neither in app where you are using it).
- Any problem should to throw an Exception inherited from standard Exception (including connection problem and SQL errors). Its only up to you how do you want to process it.
- No unnecessary procesing with result to shrinkage performance.
- XML separate configuration file
- posibility of db activity logging

*Supported DB platforms:
- MySQL
- MSSQL
- ODBC
- PostgreSQL

*Possible Extensions (not applied yet):
- Very easy expansions of supported SQL database systems. For any more you can write your own class with platform specific commands that implements DbPlatform abstract class.

*Clear installation:
- PHP5 needed
- unpack it into your PHP directory
- set your database connection in config.xml for task <test>
- Check $query_select and $database_name in ___cases_of_use.php and modify it for your test on your database.
- Run ___cases_of_use.php for test.


*changes in version 1.5
- Change in API: Class now throws DbControlException inherited from Exception (Not Exception like before). It does not need any change of client application due the inheritance from standard Exception class. (You can catch it like Exception if you want)
  New DbControlException is very handy for prior and easy exceptions handling in client application.
- PgSQL is now supported.
- Client applications database activity logging is now available. You can optionaly set it on in config.xml file for any "task" separately.
  Check new DbControl.xml. Its only 1 new optional tag
  Errors are logged for ever. (You can uncomment it in DbControlException.class.php)
  PHP must have write permissions in DbControl-classes directory for write log if is it active!!!
- added DB result class whithout cache (essential for huge data transfers) - see how does it work for more details
- new function DbControl::getTableColumns($schema, $table) implemented

- Update instructions:
  1) Backup your old DbControl
  2) Delete all files but your adjusted config.xml
  3) add all files from new DbControl (pay attention to your DbControl)
  4) replace old template of prior task setting by new in config.xml
  5) add new optional setting <logon> to your <tasks> if you want

*changes in version 1.4
- Change in API: Method DbControl::getLastNumRows() moved to DbResult::getNumRows() - You can copy it back into DbControl if you are using it in your previous applications. Method has the same code in both classes. Take care of DbControlInterface and DbResultInterface to.
- Change in API: Method DbResult::get() added. Its a allias of DbResult::__get() - for Comfort.
- Bug fixed: Multiple changing of SQL query by DbControl::setQuery() and making independent instances of DbResult by calling DbControl::initiate() was not possible - FIXED.
- Update instructions: rewrite all files but config.xml

*changes in version 1.3
- No API changes applied, for update you must only replace all files of library and Type your DbSelector configuration into XML file in prior syntax (there are examples).
- XML configuration.
- Extension support check. Every class, that needs some PHP extension (4Ex.: MSSQL) checks it. If extension not supported, class throws an Exception.

*changes in version 1.2
- No API changes applied, for update you must only replace all files of library and reconfig your original Attributes in DbSelector class
- Transactions supported now

*changes in version 1.1
- No API changes applied (only 1method of DbResultinterface added - next point), for update to v1.1 you must only replace all files of library and reconfig your original Attributes in DbSelector class
- DbResultInterface now content method that returns array of Column names of his result
- fixed bug in DbMysql::selectDb, DbMssql::selectDb [There was not used connection identifier]
- preloading of lib files changed from require() to require_once() - repeated preloading will not cause error warning
- DbMssql class now implements getLastId method
- ODBC supported
- DbMysql, DbMssql [repaired Exceptions handling of connect methods]
- added connection error specific Exception code (-1) - very handy if you want to use backup hosts


*How does it work / how to use it:

 - PHP 5 or higher needed.

 I wanted interface to dealing with SQL DB systems, that will allow me changing platforms how will i want whithout any change of work with interface.
 To that i had to specified somehow relationship between purpose (to database querying) and connection essentials (platform, host, login).
 To this purpose serve $task parameter.

 So first you must specify the task of your application (for Example "forum") and attach a connection essential informations to it in config.xml.
 By this "task" attribute you will specify purpose of your database querying, its something like ID of your client application.
 You can very easy change DBMS for this "task" in config.xml file without any change of API. You can be using as many tasks as you want.

 Example of use:
  - $DbC = new DbControl($task = "forum",  $charset = "utf8");
   (Second attribute charset is not needed. He is very handy if you are using new MySQL with Eastern languages.)

 Then you can call $DbC->selectDb("databaseSchemaName"); for selecting of DB schema.

 If you are Then calling $DbC->setQuery("SQL query", $noCache = false), you can use parametrized querying (For Example: "select * from tablename where id = '<1>' and user ='<2>'");
 In next call $DbC->initiate() you can use this <numbers> to set parameters: initiate(125, "John");
 It is of course very handy if you are using it in some loops with changing parameters.
 In setQuery is possible to choose if you want DbResult with,or whithout cache. DbResult with cache is recomended to handle little data sizes, DbResult whithout cache is needed to handle results with huge data sizes. For example transfers.
 DbResult whithout cache does not support previous()!
 Method DbControl::initiate() is working in every case with the last query, which was set by DbControl::setQuery().
 If DBMS does return result, DbControl::initiate() returns object of class DbResult (You can use it according to DbResultInterface that is implemented by it).
 If case of queries, that does not returns result resources class returns boolean indication of succes
 in every case of problem classes throws standard PHP Exception with message and in case of bad SQL query with DBMS Error code to.
 in case of database connection error does library throws Exception with specific code (-1). You can use it for switching backup hosts / DBMS.
 If You want to use transaction, you can control them through the methods DbControl::transactionStart($autoCommit = false)), DbControl::transactionRollback() and DbControl::transactionCommit().
 Any new instance of DbControl has his exclusive connection to database. There are independent themselfs.

 Some cases of use are defined in ___case_of_use.php.
