<?php
/* Versioning experiment: Show that having the same class defined 
 * in different "threads" is possible
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses('lang.Thread');

  // {{{ class Runner
  class Runner extends Thread {
  
    function run() {
      $class= &XPClass::forName('versions.'.$this->getName().'.Date');
      $instance= &$class->newInstance();
      
      Console::writeLine(
        '- Runner #', $this->getName(), 
        ' created instance of date: ', $instance->toString()
      );
    }
  }
  // }}}
  
  // {{{ main
  for ($i= 0; $i < 3; $i++) {
    $t[$i]= &new Runner($i+ 1);
    $t[$i]->start();
    $t[$i]->join();
  }
  // }}}
?>
