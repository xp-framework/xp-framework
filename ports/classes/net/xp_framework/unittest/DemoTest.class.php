<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('unittest.TestCase');

  /**
   * Shows different test scenarios
   *
   * @purpose  Unit Test
   */
  class DemoTest extends TestCase {
      
    /**
     * A test that always succeeds
     *
     */
    #[@test]
    public function alwaysSucceeds() {
      $this->assertTrue(TRUE);
    }

    /**
     * A test that is ignored
     *
     */
    #[@test, @ignore('Ignored')]
    public function ignored() {
      $this->fail('Ignored test executed', 'executed', 'ignored');
    }

    /**
     * Setup method
     *
     */
    public function setUp() {
      if (0 == strcasecmp('alwaysSkipped', $this->name)) {
        throw new PrerequisitesNotMetError('Skipping', $this->name);
      }
    }

    /**
     * A test that is skipped due to not met prerequisites.
     *
     */
    #[@test]
    public function alwaysSkipped() {
      $this->fail('Skipped test executed', 'executed', 'skipped');
    }

    /**
     * A test that always fails because of a failed assertion
     *
     */
    #[@test]
    public function alwaysFails() {
      $this->assertTrue(FALSE);
    }

    /**
     * A test that always fails because the expected exception was not
     * thrown.
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function expectedExceptionNotThrown() {
      TRUE;
    }

    /**
     * A test that always fails because an exception is thrown.
     *
     */
    #[@test]
    public function throwsAnException() {
      throw new IllegalArgumentException('');
    }

    /**
     * A test that timeouts
     *
     */
    #[@test, @limit(time= 0.1)]
    public function timeouts() {
      $start= gettimeofday();
      $end= (1000000 * $start['sec']) + $start['usec'] + 1000 * 200;    // 0.2 seconds
      do {
        $now= gettimeofday();
      } while ((1000000 * $now['sec']) + $now['usec'] < $end);
    }
  }
?>
