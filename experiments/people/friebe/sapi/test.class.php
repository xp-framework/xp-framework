<?php
/* Imitate Java/C# "public static void main(String[] args)" behaviour.
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('class');

  // {{{ final class test
  class test extends Object {
  
    function main(&$args) {
      echo 'In ', __CLASS__ , '::', __FUNCTION__, "()...\n";

      $test= &new Test();
      var_dump($test->getClassName());
      return 0;
    }
  
  } runnable();
  // }}}
?>
