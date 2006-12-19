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
    public function noException() {
      try {
        // Nothing
      } catch (Exception $caught) {
        return $this->fail('Caught an exception but none where thrown', $caught);
      }
    }

    /**
     * Basics: Tests thrown exception is caught
     *
     * @access  public
     */
    #[@test]
    public function thrownExceptionCaught() {
      try {
        throw(new XPException('Test'));
      } catch (Exception $caught) {
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
    public function multipleCatches() {
      try {
        throw(new XPException('Test'));
      } catch (IllegalArgumentException $caught) {
        return $this->fail('Exception should have been caught in Exception block', 'IllegalArgumentException');
      } catch (Exception $caught) {
        return TRUE;
      } catch (Throwable $caught) {
        return $this->fail('Exception should have been caught in Exception block', 'Throwable');
      }

      $this->fail('Thrown Exception not caught');
    }
  }
?>
