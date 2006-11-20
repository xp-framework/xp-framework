<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestSuite');

  /**
   * Unittest Runner
   *
   * @purpose  Bean
   */
  #[@bean(type = STATELESS, name = 'xp/test/TestRunner')]
  class TestRunnerBean extends Object {
 
    /**
     * Runs a test
     *
     * @access  public
     * @param   string classname
     * @return  mixed results
     */ 
    #[@remote]
    function runTestClass($classname) {
      $suite= &new TestSuite();
      try(); {
        $suite->addTestClass(XPClass::forName($classname));
      } if (catch('Exception', $e)) {
        return throw($e);
      }

      return $suite->run();
    }
  }
?>
