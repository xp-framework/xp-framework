<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('unittest.TestCase');

  /**
   * This class is used in the SuiteTest class' running* methods and
   * by the LimitTest methods
   *
   * @see      xp://net.xp_framework.unittest.tests.SuiteTest
   * @see      xp://net.xp_framework.unittest.tests.LimitTest
   * @purpose  Unit Test
   */
  class SimpleTestCase extends TestCase {
    public static 
      $init    = 0, 
      $dispose = 0;
      
    /**
     * Beforeclass method
     *
     */
    #[@beforeClass]
    public static function init() {
      self::$init++;
      if (0 === self::$init) {
        throw new PrerequisitesNotMetError('BeforeClass failed', self::$init);
      }
    }
    
    /**
     * Afterclass method
     *
     */
    #[@afterClass]
    public static function dispose() {
      self::$dispose++;
    }

    /**
     * Sets up this test. Throws a PrerequisitesNotMetError if the "skipped" 
     * test is run.
     *
     */
    public function setUp() {
      if ('skipped' === $this->name) {
        throw new PrerequisitesNotMetError('SKIP', $this->name);
      } else if ('raisesAnErrorInSetup' === $this->name) {
        $a.= '';
        throw new AssertionFailedError('WARN', $this->name);
      }
    }

    /**
     * Always succeeds
     *
     */
    #[@test]
    public function succeeds() {
      $this->assertTrue(TRUE);
    }

    /**
     * Always fails
     *
     */
    #[@test]
    public function fails() {
      $this->assertTrue(FALSE);
    }

    /**
     * Always fails
     *
     */
    #[@test]
    public function throws() {
      throw new IllegalArgumentException('');
    }

    /**
     * Always fails
     *
     */
    #[@test]
    public function raisesAnError() {
      $a.= '';
    }

    /**
     * Always fails
     *
     */
    #[@test]
    public function raisesAnErrorAndFails() {
      $a.= '';
      $this->assertTrue(FALSE);
    }

    /**
     * Always fails
     *
     */
    #[@test]
    public function raisesAnErrorInSetup() {
    }

    /**
     * Always skipped
     *
     */
    #[@test]
    public function skipped() {
      $this->fail('Prerequisites not met, should not be executed');
    }

    /**
     * Always ignored
     *
     */
    #[@test, @ignore('For test purposes')]
    public function ignored() {
    }

    /**
     * Catches the expected exception 
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function catchExpected() {
      throw new IllegalArgumentException('');
    }

    /**
     * Catches the expected exception 
     *
     */
    #[@test, @expect('lang.XPException')]
    public function catchSubclassOfExpected() {
      throw new IllegalArgumentException('');
    }

    /**
     * Does not catch the expected exception 
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function expectedExceptionNotThrown() {
      throw new FormatException('');
    }

    /**
     * Catches the expected exception 
     *
     */
    #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= 'Hello')]
    public function catchExpectedWithMessage() {
      throw new IllegalArgumentException('Hello');
    }

    /**
     * Catches the expected exception
     *
     */
    #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= 'Hello')]
    public function catchExpectedWithWrongMessage() {
      throw new IllegalArgumentException('Another message');
    }

    /**
     * Catches the expected exception
     *
     */
    #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= '/message/')]
    public function catchExpectedWithPatternMessage() {
      throw new IllegalArgumentException('Another message');
    }

    /**
     * Catches the expected exception
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function catchExpectedWithWarning() {
      $a.= '';
      throw new IllegalArgumentException('');
    }

    /**
     * A test that timeouts
     *
     */
    #[@test, @limit(time= 0.010)]
    public function timeouts() {
      $start= gettimeofday();
      $end= (1000000 * $start['sec']) + $start['usec'] + 1000 * 50;    // 0.05 seconds
      do {
        $now= gettimeofday();
      } while ((1000000 * $now['sec']) + $now['usec'] < $end);
    }

    /**
     * A test that does not timeout
     *
     */
    #[@test, @limit(time= 1.0)]
    public function noTimeout() {
    }
  }
?>
