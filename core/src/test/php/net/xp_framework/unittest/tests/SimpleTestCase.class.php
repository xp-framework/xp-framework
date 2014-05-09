<?php namespace net\xp_framework\unittest\tests;
 
/**
 * This class is used in the SuiteTest class' running* methods and
 * by the LimitTest methods
 *
 * @see  xp://net.xp_framework.unittest.tests.SuiteTest
 * @see  xp://net.xp_framework.unittest.tests.LimitTest
 */
class SimpleTestCase extends \unittest\TestCase {
  public static 
    $init    = 0, 
    $dispose = 0;
    
  /**
   * Beforeclass method
   */
  #[@beforeClass]
  public static function init() {
    self::$init++;
    if (0 === self::$init) {
      throw new \unittest\PrerequisitesNotMetError('BeforeClass failed', self::$init);
    }
  }
  
  /**
   * Afterclass method
   */
  #[@afterClass]
  public static function dispose() {
    self::$dispose++;
  }

  /**
   * Sets up this test. Throws a PrerequisitesNotMetError if the "skipped" 
   * test is run.
   */
  public function setUp() {
    if ('skipped' === $this->name) {
      throw new \unittest\PrerequisitesNotMetError('SKIP', null, $this->name);
    } else if ('raisesAnErrorInSetup' === $this->name) {
      $a.= '';
      throw new \unittest\AssertionFailedError('WARN', $this->name);
    }
  }

  #[@test]
  public function succeeds() {
    $this->assertTrue(true);
  }

  #[@test]
  public function fails() {
    $this->assertTrue(false);
  }

  #[@test]
  public function throws() {
    throw new \lang\IllegalArgumentException('');
  }

  #[@test]
  public function raisesAnError() {
    $a.= '';
  }

  #[@test]
  public function raisesAnErrorAndFails() {
    $a.= '';
    $this->assertTrue(false);
  }

  #[@test]
  public function raisesAnErrorInSetup() {
  }

  #[@test]
  public function skipped() {
    $this->fail('Prerequisites not met, should not be executed');
  }

  #[@test, @ignore('For test purposes')]
  public function ignored() {
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function catchExpected() {
    throw new \lang\IllegalArgumentException('');
  }

  #[@test, @expect('lang.XPException')]
  public function catchSubclassOfExpected() {
    throw new \lang\IllegalArgumentException('');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function expectedExceptionNotThrown() {
    throw new \lang\FormatException('');
  }

  #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= 'Hello')]
  public function catchExpectedWithMessage() {
    throw new \lang\IllegalArgumentException('Hello');
  }

  #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= 'Hello')]
  public function catchExpectedWithWrongMessage() {
    throw new \lang\IllegalArgumentException('Another message');
  }

  #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= '/message/')]
  public function catchExpectedWithPatternMessage() {
    throw new \lang\IllegalArgumentException('Another message');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function catchExpectedWithWarning() {
    $a.= '';
    throw new \lang\IllegalArgumentException('');
  }

  #[@test, @limit(time= 0.010)]
  public function timeouts() {
    $start= gettimeofday();
    $end= (1000000 * $start['sec']) + $start['usec'] + 1000 * 50;    // 0.05 seconds
    do {
      $now= gettimeofday();
    } while ((1000000 * $now['sec']) + $now['usec'] < $end);
  }

  #[@test, @limit(time= 1.0)]
  public function noTimeout() {
  }

  #[@test]
  public function doFail() {
    $this->fail('Test');
  }

  #[@test]
  public function doSkip() {
    $this->skip('Test');
  }
}
