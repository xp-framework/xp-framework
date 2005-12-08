<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.profiling.unittest.TestCase');

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
     * @access  public
     */
    function setUp() {
      $this->assertEquals(E_ALL, error_reporting(), 'Error reporting level not E_ALL');
      $this->assertEmpty(xp::registry('errors'), 'Error registry initially not empty');
    }

    /**
     * Teardown method. Clears the xp error registry.
     *
     * @access  public
     */
    function tearDown() {
      xp::gc();
    }
    
    /**
     * Tests that PHP errormessages get appended to the xp error registry
     *
     * @access  public
     */
    #[@test]
    function errorsGetAppendedToRegistry() {
      $a.= '';    // E_NOTICE: Undefined variable:  a
      $this->assertEquals(1, sizeof(xp::registry('errors')));
    }

    /**
     * Tests that PHP errormessages get appended to the xp error registry
     *
     * @access  public
     */
    #[@test]
    function errorsAppearInStackTrace() {
      $a.= '';    // E_NOTICE: Undefined variable:  a
      $line= __LINE__;

      try(); {
        throw(new Exception(''));
      } if (catch('Exception', $e)) {
        $self= get_class($this);
        foreach ($e->getStackTrace() as $element) {
          if ($element->class != $self || $element->line != $line - 1) continue;
          
          // We've found the stack trace element we're looking for
          // TBI: Check more detailed if this really is the correct one?
          return;
        }

        // Fall through
      }
      $this->fail('Exception not caught', NULL, 'lang.Exception');
    }
  }
?>
