<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.profiling.unittest.TestSuite');

  /**
   * Test suite
   *
   * Example:
   * <code>
   *   uses(
   *     'util.profiling.unittest.TestSuite', 
   *     'net.xp_framework.unittest.rdbms.DBTest'
   *   );
   *   
   *   $dsn= 'sybase://user:password@host/?autoconnect=1';
   *
   *   $suite= &new TestSuite();
   *   $suite->addTest(new DBTest('testConnect', $dsn));
   *   $suite->addTest(new DBTest('testSelect', $dsn));
   *   
   *   $result= &$suite->run();
   *   echo $result->toString();
   * </code>
   *
   * @see      http://junit.sourceforge.net/doc/testinfected/testing.htm
   * @purpose  Testcase container
   */
  class RemoteTestSuite extends RemoteTestSuite {

    /**
     * Add a test
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @return  &util.profiling.unittest.TestCase
     */
    public function &addTest(&$test) {
      if (is('util.profiling.unittest.InstantTestCase', $test)) {
        // XXX TBI: Load sourcecode and pass to remote instance
        // $this->tests[]= 
        return;
      }
      
      $this->tests[]= &$test;
      return $test;
    }
    
    /**
     * Run this test suite
     *
     * @access  public
     * @return  &util.profiling.unittest.TestResult
     */
    public function &run() {
      Console::writeLine('===> Invoking remote tests...');
      $result= &new TestResult();
      return $result;
      for ($i= 0, $s= sizeof($this->tests); $i < $s; $i++) {
        $this->runTest($this->tests[$i], $result);
      }

      return $result;
    }
  }
?>
