<?php
	uses('rdbms.DBTable');
	
	class DBAdapter extends Object {
		var
			$conn=	NULL;
			
		function __construct(&$conn) {
			$this->conn= &$conn;
			parent::__construct();
		}
		
		function getTable($name) {}
		
		function getTables($database) {}
		
		function getDatabases() {}
	}
?>
