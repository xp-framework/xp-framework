<?php
/* Imitate Java/C# "public static void main(String[] args)" behaviour.
 *
 * $Id$ 
 */

  require('lang.base.php');
  require_once(dirname(__FILE__).'/class.sapi.php'); // xp::sapi('class');

  // {{{ final class test
  class test extends Object {
  
    function main(&$args) {
      echo 'In ', __CLASS__, '::', __FUNCTION__, "()...\n";

      $test= &this::newInstance();
      echo $test->getClassName(), "\n";
      
      return 0;
    }
  
  } run(__FILE__);
  // }}}
?>
