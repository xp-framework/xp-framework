<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('util.profiling.unittest.TestCase');

  /**
   * Shows different test scenarios
   *
   * @purpose  Unit Test
   */
  class DemoTest extends TestCase {
      
    /**
     * A test that always succeeds
     *
     * @access  public
     */
    #[@test]
    function alwaysSucceeds() {
      $this->assertTrue(TRUE);
    }

    /**
     * A test that is skipped
     *
     * @access  public
     */
    #[@test, @ignore('Skipped')]
    function alwaysSkipped() {
      $this->fail('Ignored test executed', 'executed', 'skipped');
    }

    /**
     * A test that always fails because of a failed assertion
     *
     * @access  public
     */
    #[@test]
    function alwaysFails() {
      $this->assertTrue(FALSE);
    }

    /**
     * A test that always fails because the expected exception was not
     * thrown.
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function expectedExceptionNotThrown() {
      TRUE;
    }
  }
?>
