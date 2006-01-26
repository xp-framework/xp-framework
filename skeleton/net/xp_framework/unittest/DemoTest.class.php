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
     * A test that is ignored
     *
     * @access  public
     */
    #[@test, @ignore('Ignored')]
    function ignored() {
      $this->fail('Ignored test executed', 'executed', 'ignored');
    }

    /**
     * Setup method
     *
     * @access  public
     */
    function setUp() {
      if (0 == strcasecmp('alwaysSkipped', $this->name)) {
        throw(new PrerequisitesNotMetError('Skipping', $this->name));
      }
    }

    /**
     * A test that is skipped due to not met prerequisites.
     *
     * @access  public
     */
    #[@test]
    function alwaysSkipped() {
      $this->fail('Skipped test executed', 'executed', 'skipped');
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
