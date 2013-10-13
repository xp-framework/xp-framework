<?php namespace net\xp_framework\unittest\core;

use unittest\TestCase;


/**
 * Test the XP error handling semantics
 *
 * @purpose  Testcase
 */
class ErrorsTest extends TestCase {

  /**
   * Setup method. Ensures xp error registry is initially empty and
   * that the error reporting level is set to E_ALL (which is done
   * in lang.base.php).
   *
   */
  public function setUp() {
    $this->assertEquals(E_ALL, error_reporting(), 'Error reporting level not E_ALL');
    \xp::$errors= array();
    $this->assertEmpty(\xp::$errors, 'Error registry initially not empty');
  }

  /**
   * Teardown method. Clears the xp error registry.
   *
   */
  public function tearDown() {
    \xp::gc();
  }

  #[@test]
  public function errorsGetAppendedToRegistry() {
    $a.= '';    // E_NOTICE: Undefined variable:  a
    $this->assertEquals(1, sizeof(\xp::$errors));
  }

  #[@test]
  public function errorAtFile() {
    $a.= '';    // E_NOTICE: Undefined variable:  a
    $this->assertTrue((bool)\xp::errorAt(__FILE__));
  }

  #[@test]
  public function errorAtFileAndLine() {
    $a.= '';    // E_NOTICE: Undefined variable:  a
    $this->assertTrue((bool)\xp::errorAt(__FILE__, __LINE__ - 1));
  }
  
  #[@test]
  public function errorWithClassAndMethod() {
    $a.= '';  // E_NOTICE: Undefined variable: a
      
    try {
      throw new \lang\XPException('');
    } catch (\lang\XPException $e) {
      foreach ($e->getStackTrace() as $element) {
        if ($element->file !== __FILE__) continue;
        
        $this->assertEquals(__CLASS__, $element->class);
        $this->assertEquals(__FUNCTION__, $element->method);
        
        return;
      }
      
      $this->fail('Error not in stacktrace', $e->getStackTrace(), 'lang.StackTraceElement');
      return;
    }

    $this->fail('Exception not caught', NULL, 'lang.Exception');
  }

  #[@test]
  public function errorsAppearInStackTrace() {
    $a.= '';    // E_NOTICE: Undefined variable:  a
    $line= __LINE__ - 1;

    try {
      throw new \lang\XPException('');
    } catch (\lang\XPException $e) {
      foreach ($e->getStackTrace() as $element) {
        if ($element->file !== __FILE__ || $element->line !== $line) continue;
        
        // We've found the stack trace element we're looking for
        // TBI: Check more detailed if this really is the correct one?
        return;
      }

      $this->fail('Error not in stacktrace', $e->getStackTrace(), 'lang.StackTraceElement');
      return;
    }

    $this->fail('Exception not caught', NULL, 'lang.Exception');
  }
}
