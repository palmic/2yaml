<?php
class QueryBuilderPlatformOdbc extends QueryBuilderPlatform
{
	public function getQuotesDatabaseName() {
		return array("`", "`");
	}

	public function getQuotesTableName() {
		return array("`", "`");
	}

	public function getQuotesColumnName() {
		return array("`", "`");
	}

	public function getQuotesValue() {
		return array("'", "'");
	}
	
	protected function escapeString($string = "") {
		return mysql_escape_string($string);
	}
}
?>