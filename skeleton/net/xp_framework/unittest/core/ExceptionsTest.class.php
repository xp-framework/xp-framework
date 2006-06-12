<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.profiling.unittest.TestCase');

  /**
   * Test the XP exception mechanism
   *
   * @purpose  Testcase
   */
  class ExceptionsTest extends TestCase {

    /**
     * Basics: Tests nothing is caught when nothing is thrown
     *
     * @access  public
     */
    #[@test]
    function noException() {
      try(); {
        // Nothing
      } if (catch('Exception', $caught)) {
        return $this->fail('Caught an exception but non where thrown', $caught);
      }
    }

    /**
     * Basics: Tests thrown exception is caught
     *
     * @access  public
     */
    #[@test]
    function thrownExceptionCaught() {
      try(); {
        throw(new Exception('Test'));
      } if (catch('Exception', $caught)) {
        $this->assertSubclass($caught, 'Exception');
        unset($caught);
        return TRUE;
      }

      $this->fail('Thrown Exception not caught');
    }
  }
?>
