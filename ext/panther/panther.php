<?php
  class Test {
    public $value   = 'Initial';
    private $used   = 0;
    
    private function update() {
      echo 'Test::update('; var_export($this->used); echo ")\n";
      $this->used++;
    }
    
    public function getUsageCount() {
      return $this->used;
    }
    
    public function hello() {
      $args= func_get_args();
      echo 'Test::hello('; var_export($args); echo ")\n";
      $this->update();
      return sizeof($args);
    }
  }
  
  $registry['rmi.RMIObject']= new Test();
  
  return $registry;
?>
