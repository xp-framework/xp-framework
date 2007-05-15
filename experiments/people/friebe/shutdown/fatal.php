<?php
  require('lang.base.php');
  xp::sapi('cli');
  
  class WithDestructor extends Object {
  
    public function __destruct() {
      Console::writeLine('DESTRUCT');
    }
  }

  function atexit() {
    Console::writeLine('SHUTDOWN-FUNC');
  }
  
  register_shutdown_function('atexit');  
  
  $a= array(new WithDestructor());
  Console::writeLine('FATAL...');
  $null= NULL;
  $null->hello();
?>
