<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'util.profiling.unittest.TestResult'
  );

  /**
   * Test suite
   *
   * Example:
   * <code>
   *   uses(
   *     'util.profiling.unittest.TestSuite', 
   *     'net.xp-framework.unittest.SybaseTest'
   *   );
   *   
   *   $dsn= 'sybase://user:password@host/?autoconnect=1';
   *
   *   $suite= &new TestSuite();
   *   $suite->addTest(new SybaseTest('testConnect', $dsn));
   *   $suite->addTest(new SybaseTest('testSelect', $dsn));
   *   
   *   var_dump($suite->run());
   * </code>
   *
   * @purpose  purpose
   */
  class TestSuite extends Object {
    var
      $tests    = array();

    /**
     * Add a test
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @return  &util.profiling.unittest.TestCase
     */
    function addTest(&$test) {
      $this->tests[]= &$test;
      return $test;
    }
    
    /**
     * Run this test suite
     *
     * @access  public
     * @return  &util.profiling.unittest.TestResult
     */
    function &run() {
      $result= &new TestResult();
      for ($i= 0, $s= sizeof($this->tests); $i < $s; $i++) {
        if (NULL !== ($reason= $this->tests[$i]->setUp())) {
          
          $result->setSkipped($this->tests[$i], $reason);
          continue;
        }
        
        try(); {
          $this->tests[$i]->run();
        } if (catch('Exception', $e)) {
          $e->message= $e->getStackTrace();
          $result->setFailed($this->tests[$i], $e);
          $this->tests[$i]->tearDown();
          continue;
        }

        $result->setSucceeded($this->tests[$i], $e);
        $this->tests[$i]->tearDown();
      }

      return $result;
    }
  }
?>
