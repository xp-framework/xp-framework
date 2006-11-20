<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

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
        return $this->fail('Caught an exception but none where thrown', $caught);
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
        delete($caught);
        return TRUE;
      }

      $this->fail('Thrown Exception not caught');
    }

    /**
     * Basics: Tests thrown exception is caught by fully qualified 
     * class name ("FQCN")
     *
     * @access  public
     */
    #[@test]
    function thrownExceptionCaughtByFqCn() {
      try(); {
        throw(new Exception('Test'));
      } if (catch('lang.Exception', $caught)) {
        $this->assertSubclass($caught, 'Exception');
        delete($caught);
        return TRUE;
      }

      $this->fail('Thrown Exception not caught');
    }

    /**
     * Basics: Tests thrown exception is caught in the correct catch
     * block.
     *
     * @access  public
     */
    #[@test]
    function multipleCatches() {
      try(); {
        throw(new Exception('Test'));
      } if (catch('IllegalArgumentException', $caught)) {
        return $this->fail('Exception should have been caught in Exception block', 'IllegalArgumentException');
      } if (catch('Exception', $caught)) {
        return TRUE;
      } if (catch('Throwable', $caught)) {
        return $this->fail('Exception should have been caught in Exception block', 'Throwable');
      }

      $this->fail('Thrown Exception not caught');
    }
  }
?>
