<?php
	class DBIndex extends Object {
		var
			$name= 		'',
			$keys= 		array(),
			$unique= 	FALSE,
			$primary= 	FALSE;
			
		function __construct($name, $keys) {
			$this->name= $name;
			$this->keys= $keys;
			parent::__construct();
		}
		
		function isPrimaryKey() {
			return $this->primary;
		}
		
		function isUnique() {
			return $this->unique;
		}
		
		function getName() {
			return $this->name;
		}
		
		function getKeys() {
			return $this->keys;
		}
	}
?>
