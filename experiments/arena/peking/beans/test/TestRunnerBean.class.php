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
     * @param   string classname
     * @return  mixed results
     */ 
    #[@remote]
    public function runTestClass($classname) {
      $suite= new TestSuite();
      try {
        $suite->addTestClass(XPClass::forName($classname));
      } catch (Exception $e) {
        throw($e);
      }

      return $suite->run();
    }
  }
?>
