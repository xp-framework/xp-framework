<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses('lang.Runnable');
  
  class Heartbeat extends Object implements Runnable {
    private $status= 'FAIL';
    
    public function run(ParamString $p= NULL) {
      register_shutdown_function(array($this, 'atexit'));
      
      // Work
      switch ($p->value(1, NULL, 'ok')) {
        case 'fatal': $null= NULL; $null->method();
        case 'throw': throw new XPException('Uncaught');
        case 'ok': default: $this->status= 'OK';
      }
    }
    
    public function atexit() {
      Console::writeLine('*** atexit ', $this->status);
    }
  }
  
  create(new Heartbeat())->run(new ParamString());
?>
