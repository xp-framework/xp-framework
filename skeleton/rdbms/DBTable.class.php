<?php
	uses('rdbms.DBTableAttribute', 'rdbms.DBIndex');
	
	class DBTable extends Object {
		var 
			$name=			'',
			$attributes= 	array(),
			$indexes= 		array(),
			$constraints= 	array();
		
		function __construct($name) {
			$this->name= $name;
			parent::__construct();
		}
		
		function &getByName(&$adapter, $name) {
			return $adapter->getTable($name);
		}
		
		function &getByDatabase(&$adapter, $database) {
			return $adapter->getTables($database);
		}
		
		function &getFirstAttribute() {
			reset($this->attributes);
			return current($this->attributes);
		}

		function &getNextAttribute() {
			return next($this->attributes);
		}
		
		function &addAttribute(&$attr) {
			$this->attributes[]= &$attr;
			return $attr;
		}
		
		function &addIndex(&$index) {
			$this->indexes[]= &$index;
			return $index;
		}

		function &getFirstIndex() {
			reset($this->indexes);
			return current($this->indexes);
		}

		function &getNextIndex() {
			return next($this->indexes);
		}
		
		function hasAttribute($name) {
			for ($i= 0, $m= sizeof($this->attributes); $i < $m; $i++) {
				if ($name == $this->attributes[$i]->name) {
					return TRUE;
				}
			}
			return FALSE;
		}
	}
?>
