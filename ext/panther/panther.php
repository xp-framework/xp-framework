<?php
  class Test {
    public $value   = 'Initial';
    
    public function test() {
      var_dump(__CLASS__, __FUNCTION__);
    }
    
    public function hello() {
      $args= func_get_args();
      var_dump(__CLASS__, __FUNCTION__, $args);
      $this->test();
      return sizeof($args);
    }
  }
  
  $registry['rmi.RMIObject']= new Test();
?>
