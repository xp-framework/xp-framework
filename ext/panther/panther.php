<?php
  class Test {
    public $value   = 'Initial';
    
    private function update($arg) {
      echo 'Test::update('; var_export($arg); echo ")\n";
    }
    
    public function hello() {
      $args= func_get_args();
      echo 'Test::hello('; var_export($args); echo ")\n";
      $this->update($args[0]);
      return sizeof($args);
    }
  }
  
  $registry['rmi.RMIObject']= new Test();
  
  return $registry;
?>
