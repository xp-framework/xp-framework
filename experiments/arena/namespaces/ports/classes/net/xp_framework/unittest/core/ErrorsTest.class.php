<?php
/* This class is part of the XP framework
 *
 * $Id: ErrorsTest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::core;

  ::uses('unittest.TestCase');

  /**
   * Test the XP error handling semantics
   *
   * @purpose  Testcase
   */
  class ErrorsTest extends unittest::TestCase {

    /**
     * Setup method. Ensures xp error registry is initially empty and
     * that the error reporting level is set to E_ALL (which is done
     * in lang.base.php).
     *
     */
    public function setUp() {
      $this->assertEquals(E_ALL, error_reporting(), 'Error reporting level not E_ALL');
      ::xp::registry('errors', array());
      $this->assertEmpty(::xp::registry('errors'), 'Error registry initially not empty');
    }

    /**
     * Teardown method. Clears the xp error registry.
     *
     */
    public function tearDown() {
      ::xp::gc();
    }
    
    /**
     * Tests that PHP errormessages get appended to the xp error registry
     *
     */
    #[@test]
    public function errorsGetAppendedToRegistry() {
      $a.= '';    // E_NOTICE: Undefined variable:  a
      $this->assertEquals(1, sizeof(::xp::registry('errors')));
    }

    /**
     * Tests xp::errorAt() finds errors have occured in this file
     *
     */
    #[@test]
    public function errorAtFile() {
      $a.= '';    // E_NOTICE: Undefined variable:  a
      $this->assertTrue(::xp::errorAt(__FILE__));
    }

    /**
     * Tests xp::errorAt() finds errors have occured in this file *and*
     * and at the specified line.
     *
     */
    #[@test]
    public function errorAtFileAndLine() {
      $a.= '';    // E_NOTICE: Undefined variable:  a
      $this->assertTrue(::xp::errorAt(__FILE__, __LINE__ - 1));
    }

    /**
     * Tests that PHP errormessages get appended to the xp error registry
     *
     */
    #[@test]
    public function errorsAppearInStackTrace() {
      $a.= '';    // E_NOTICE: Undefined variable:  a
      $line= __LINE__ - 1;

      try {
        throw(new lang::XPException(''));
      } catch (::Exception $e) {
        $self= get_class($this);
        foreach ($e->getStackTrace() as $element) {
          if ($element->class != $self || $element->line != $line) continue;
          
          // We've found the stack trace element we're looking for
          // TBI: Check more detailed if this really is the correct one?
          return;
        }

        $this->fail('Error not in stacktrace', NULL, 'lang.StackTraceElement');
        return;
      }

      $this->fail('Exception not caught', NULL, 'lang.Exception');
    }
  }
?>
